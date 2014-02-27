<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Auctions controller
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Controller_Auctions extends Controller_Fusion_Site
{
	/**
	 * Add the auction menu to the template
	 */
	public function after()
	{
		if($this->_tpl != null)
		{
			$this->_tpl->menu = Element::factory('auctions')->render('Menu', 'button_group');
		}
		parent::after();
	}

	/**
	 * List auctions
	 */
	public function action_index()
	{
		$this->_tpl = new View_Auctions_Index;

		$auctions = ORM::factory('User_Auction')
			->with('bid:user')
			->where('user_auction.until', '>', time())
			->where('user_auction.auto_buy', '>', '-1')
			->order_by('user_auction.id', 'DESC');

		$paginate = Paginate::factory($auctions, Kohana::$config->load('auctions.list.pagination'))
			->execute();

		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->lots = $paginate->result();
	}

	/**
	 * View a single auction
	 */
	public function action_view()
	{
		$id = $this->request->param('id');

		$auction = ORM::factory('User_Auction', $id)
			->with('bid:user');

		//Check if the auction exists
		if(!$auction->loaded())
		{
			Throw new HTTP_Exception_404('No such auction exists.');
		}

		// See if it's ended (only auction owner can still view it)
		if($auction->until <= time() && Fusion::$user->id != $auction->user_id && $auction->auto_buy > -1)
		{
			RD::warning('That auction has ended.');

			$this->redirect(Route::url('auctions.index', null, true));
		}

		// Load the item that's up for auction
		$item = Item::location('auction', false, $id)
			->find();

		$this->_tpl = new View_Auctions_View;

		$this->_tpl->auction = $auction;
		$this->_tpl->bid = $auction->bid;
		$this->_tpl->item = $item;
		$this->_tpl->auto_buy = ($auction->auto_buy > 0) ? $auction->auto_buy : false;

		$this->_tpl->bid_url = Route::url('auctions.bid', ['auction_id' => $auction->id], true);
	}

	/**
	 * Bid on an auction
	 */
	public function action_bid()
	{
		$id = $this->request->param('auction_id');

		// If it's not a POST request throw a 403, permission denied
		if($this->request->method() != Request::POST)
		{
			// If an id was specified we'll redirect to the auction
			if($id != null)
			{
				$this->redirect(Route::url('auctions.view', ['id' => $id], true));
			}

			//otherwise throw an exception
			Throw new HTTP_Exception_403;
		}

		$auction = ORM::factory('User_Auction', $id)
			->with('bid:user');

		//Check if the auction exists
		if(!$auction->loaded())
		{
			Throw new HTTP_Exception_404('No such auction exists.');
		}

		// See if it's ended
		if($auction->until <= time() && $auction->auto_buy > -1)
		{
			Throw new HTTP_Exception_404('This auction has ended.');
		}

		// Check if the auction is available
		if($auction->bid->loaded() && $auction->bid->user_id == Fusion::$user->id && !isset($_POST['auto_buy']))
		{
			RD::warning('You already made the last bid');
		}
		// if bidding points, check if it's the right increment
		else if(isset($_POST['points']) && $_POST['points'] < $auction->bid->points + $auction->min_increment)
		{
			RD::warning('Your bid should be at least :points', [':points' => $auction->bid->points + $auction->min_increment]);
		}
		// Check if the user has enough points
		else if((isset($_POST['points']) && $_POST['points'] > Fusion::$user->setting('points')) ||
			(isset($_POST['auto_buy']) && $auction->auto_buy > Fusion::$user->setting('points')))
		{
			RD::warning('You don\'t have :points :currency', [
				':points' => $_POST['points'],
				':currency' => Fusion::$config['currency']['plural']
			]);
		}
		else
		{
			try {
				// Let's monitor our database queries
				Database::instance()->begin();

                // Load the item up for auction
                $item = Item::location('auction', true, $id)
                    ->find();

				$auto_buy = isset($_POST['auto_buy']);
				$points = ($auto_buy) ? $auction->auto_buy : $_POST['points'];

				//create the log
				$log = Fusion::$log->create('auction.bid', 'economy', ':username made a bid of :points', [
                    'alias_id' => $id,
                    ':points' => $points,
                    ':auto_buy' => $auto_buy,
                    ':item_name' => $item->item->name,
                    ':item_img' => $item->img(),
                    ':owner' => $auction->user->username,
                    ':until' => $auction->until
                ]);

				// If a previous bid was made
				if($auction->bid->loaded())
				{
					// give back the points
					$auction->bid->user->points($auction->bid->points);

					// notify the previous bidder
					$log->notify($auction->bid->user, 'auctions.outbid', [
						':owner' => $auction->user->username,
						':auction_id' => $id
					]);

					// delete the bid
					$auction->bid->delete();
				}

				// if it's no auto buy
				if(!$auto_buy)
				{
					// Create the bid
					$bid = ORM::factory('User_Auction_Bid')
						->values([
							'user_auction_id' => $id,
							'user_id' => Fusion::$user->id,
							'points' => $points
						])
						->save();

					//notify auction owner
					$log->notify($auction->user, 'auctions.bid', [':auction_id' => $id]);
				}
				else
				{
					// end the auction by setting auto_buy to -1
					$auction->auto_buy = '-1';
					$auction->save();

					//notify auction owner
					$log->notify($auction->user, 'auctions.auto_buy', [':auction_id' => $id]);

					// Transfer it immediately
					$item->transfer(Fuson::$user);

					// Give the auction owner the points
					$auction->user->points($points);
				}

				// deduct the points
				Fusion::$user->points($points, '-');

				// everything went fine
				RD::success('Thanks for making a bid of :points on this auction', [':points' => $points], [
					'username' => Fusion::$user->username,
					'time' => Fusion::date($bid->created_at)
				]);

				// Commit all the changes to the database
				Database::instance()->commit();
			}
			catch(ORM_Validation_Exception $e)
			{
				// Rollback any queries that were performed
				Database::instance()->rollback();

				RD::set_array(RD::ERROR, $e->errors());
			}
		}

		// redirect to auction
		$this->redirect(Route::url('auctions.view', ['id' => $id], true));
	}

	/**
	 * Create an auction
	 */
	public function action_create()
	{
		$this->_tpl = new View_Auctions_Create;

        $auctions = DB::select([DB::expr('count(id)'), 'total'])
            ->from('user_auctions')
            ->where('user_id', '=', Fusion::$user->id)
            ->where('auto_buy', '>', -1)
            ->where('until', '>', time())
            ->execute()
            ->get('total');

        $this->_tpl->unable = ($auctions == Kohana::$config->load('auctions.creation.max'));
		$this->_tpl->items = Item::location('inventory', true)
			->find_all();
	}

	/**
	 * Process an auction
	 */
	public function action_create_process()
	{
        //error_reporting(E_ALL);
		// If it's not a POST request go to create
		if($this->request->method() != Request::POST)
		{
            $this->redirect(Route::url('auctions.create', null, true));
		}

        // Check if the user doesn't have too much auctions running
        $auctions = DB::select([DB::expr('count(id)'), 'total'])
            ->from('user_auctions')
            ->where('user_id', '=', Fusion::$user->id)
            ->where('auto_buy', '>', -1)
            ->where('until', '>', time())
            ->execute()
            ->get('total');

        if($auctions == Kohana::$config->load('auctions.creation.max'))
        {
            RD::warning('You can\'t have more than :amount auctions running at the same time',
                [':amount' => Kohana::$config->load('auctions.creation.max')]);

            $this->redirect(Route::url('auctions.create', null, true));
        }

        if(!isset($_POST['item_id']))
        {
            RD::warning('Select an item');

            $this->redirect(Route::url('auctions.create', null, true));
        }

        $item = ORM::factory('User_Item', $_POST['item_id']);

        if(!$item->loaded())
        {
            RD::warning('That item does not exist');
            $this->redirect(Route::url('auctions.create', null, true));
        }
        else if($item->user_id != Fusion::$user->id)
        {
            RD::warning('That\'s not your item.');
            $this->redirect(Route::url('auctions.create', null, true));
        }
        else if($item->location != 'inventory' || $item->item->transferable == false)
        {
            RD::warning('You can\'t put :item up for auction', [':item' => $item->item>name]);
            $this->redirect(Route::url('auctions.create', null, true));
        }
        else
        {
            $lengths = Kohana::$config->load('auctions.creation.lengths');

            // Validate the post data
            $validation = Validation::factory($_POST)
                ->rule('start_bid', 'not_empty')
                ->rule('start_bid', 'digit')
                ->rule('start_bid', 'at_least', [':value', 0])
                ->rule('until', 'not_empty')
                ->rule('until', 'array_key_exists', [':value', $lengths])
                ->rule('min_increment', 'not_empty')
                ->rule('min_increment', 'digit')
                ->rule('min_increment', 'at_least', [':value', Kohana::$config->load('auctions.creation.increment.min')])
                ->rule('min_increment', 'smaller_equal', [':value', Kohana::$config->load('auctions.creation.increment.max')])
                ->rule('min_increment', 'smaller_field', [':value', 'start_bid', ':validation'])
                ->rule('auto_buy', 'digit')
                ->rule('auto_buy', 'at_least_fields_or_empty', [':value', ['start_bid', 'min_increment'], ':validation', 'auto_buy']);

            if(!$validation->check())
            {
                $errors = $validation->errors('validation');

                RD::set_array(RD::WARNING, $errors);

                $this->redirect(Route::url('auctions.create', null, true));
            }
            else
            {
                Database::instance()->begin();

                // make sure auto buy s set to 0 if the user is not using it
                if(!isset($_POST['auto_buy']) || empty($_POST['auto_buy']) || $_POST['auto_buy'] < 0)
                {
                    $_POST['auto_buy'] = 0;
                }

                try {
                    // Create the auction
                    $auction = ORM::factory('User_Auction')
                        ->values($_POST, ['start_bid', 'min_increment', 'auto_buy']);
                    $auction->user_id = Fusion::$user->id;
                    $auction->until = strtotime('+'.$lengths[$_POST['until']]);
                    $auction->save();

                    Fusion::$log->create('auction.create', 'economy', ':username created an auction with :item_name', [
                        'alias_id' => $auction->id,
                        ':item_name' => $item->item->name,
                        ':item_img' => $item->img(),
                        ':meta' => Arr::extract($_POST, ['start_bid', 'min_increment', 'auto_buy', 'until'])
                    ]);

                    // Move the item
                    $item->move('auction', 1, FALSE, $auction->id);

                    Database::instance()->commit();
                    RD::success('Thanks for creating an auction for your :item', [':item' => $item->item->name]);
                }
                catch(Kohana_Exception $e)
                {
                    // Rollback any queries that were performed
                    Database::instance()->rollback();

                    RD::error('There was an error creating your auction.');
                }
            }
        }

		$this->redirect(Route::url('auctions.index', null, true));
	}

	/**
	 * Show user's bids
	 */
	public function action_bids()
	{
		$this->_tpl = new View_Auctions_Bids;

		$this->_tpl->bids = ORM::factory('Log')
            ->where('user_id', '=', Fusion::$user->id)
            ->where('time', '>', strtotime('-'.Kohana::$config->load('auctions.list.length')))
            ->where('alias', '=', 'auction.bid')
            ->order_by('id', 'DESC')
            ->group_by('alias_id')
            ->find_all();
	}

	/**
	 * Show user's created auctions
	 */
	public function action_list()
	{
		$this->_tpl = new View_Auctions_List;

		$this->_tpl->auctions = ORM::factory('Log')
			->where('user_id', '=', Fusion::$user->id)
			->where('time', '>', strtotime('-'.Kohana::$config->load('auctions.list.length')))
			->where('alias', '=', 'auction.create')
			->find_all();
	}

	/**
	 * Search for an item in auctions
	 */
	public function action_search()
	{
		// If it's a post request we'll route to the term
		if($this->request->method() == Request::POST)
		{
			$this->redirect(Route::url('auctions.search', ['term' => $_POST['term']], true));
		}

		$term = $this->request->param("term", false);
		$limit = (isset($_GET['l'])) ? $_GET['l'] : 20;


		$this->_tpl = new View_Auctions_Search;

		if($term != false)
		{
            $lots = DB::select('user_items.item_id', 'user_auctions.id', 'user_auctions.until', 'user_auctions.auto_buy', 'users.username', 'items.name', 'items.image', 'item_types.img_dir')
                ->from('user_items')
                ->join('user_auctions')
                ->on('user_items.parameter_id', '=', 'user_auctions.id')
                ->join('users')
                ->on('user_items.user_id', '=', 'users.id')
                ->join('items')
                ->on('user_items.item_id', '=', 'items.id')
                ->join('item_types')
                ->on('items.type_id', '=', 'item_types.id')
                ->where('user_items.location', '=', 'auction')
                ->where('items.name', 'LIKE', '%'.$term.'%')
                ->where('user_auctions.until', '>', time())
                ->where('user_auctions.auto_buy', '>', -1)
                ->as_object();

			$paginate = Paginate::factory($lots, array('total_items' => $limit))
				->execute();

			$this->_tpl->term = $term;
			$this->_tpl->count_results = $paginate->count_total();
			$this->_tpl->pagination = $paginate->render();
			$this->_tpl->auctions = $paginate->result();
		}
	}
}
