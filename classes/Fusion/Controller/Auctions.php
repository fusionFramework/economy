<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Auctions controller
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Controller_Auctions extends Controller_Fusion_Site {

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

		$this->_tpl = new View_Auction_View;

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

				$auto_buy = isset($_POST['auto_buy']);
				$points = ($auto_buy) ? $auction->auto_buy : $_POST['points'];

				//create the log
				$log = Fusion::$log->create('auction.'.$id.'.bid', 'economy', ':username made a bid of :points', [':points' => $points, ':auto_buy' => $auto_buy]);

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

					// Load the item up for auction
					$item = Item::location('auction', false, $id)
						->find();

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

		$this->_tpl->items = Item::location('inventory', true)
			->find_all();
	}

	/**
	 * Process an auction
	 */
	public function action_create_process()
	{
		// If it's not a POST request go to create
		if($this->request->method() != Request::POST)
		{
			$this->redirect(Route::url('auctions.create', null, true));
		}

		try {
			Database::instance()->begin();

			$item = ORM::factory('User_Item', $_POST['item_id']);

			if(!$item->loaded())
			{
				RD::error('That item does not exist');
			}
			else if($item->user_id != Fusion::$user->id)
			{
				RD::warning('That\'s not your item.');
			}
			else if($item->location != 'inventory' || $item->item->transferable == false)
			{
				RD::warning('You can\'t put :item up for auction', [':item' => $item->item>name]);
			}
			else
			{
				$lengths = Kohana::$config->load('auctions.creation.lengths');

				$validation = Validation::factory($_POST)
					->rule('min_bid', 'not_empty')
					->rule('min_bid', 'digit')
					->rule('min_bid', 'at_least', [':value', 0])
					->rule('until', 'not_empty')
					->rule('until', 'array_key_exists', [':value', $lengths])
					->rule('increment', 'not_empty')
					->rule('increment', 'digit')
					->rule('increment', 'at_least', [':value', Kohana::$config->load('auctions.creation.increment.min')])
					->rule('increment', 'smaller_equal', [':value', Kohana::$config->load('auctions.creation.increment.max')])
					->rule('auto_buy', 'digit')
					->rule('auto_buy', function($value, Validation $validation){
						$minimum = $validation['min_bid'] + $validation['increment'];

						//auto_buy needs to be bigger than the min_bid + increment or empty
						if($value == '' || $value == '0' || $value > $minimum)
							return true;

						$validation->error('auto_buy', 'Your auto buy needs to be bigger than :minimum', [':minimum' => $minimum]);
						return false;
					}, [':value', ':validation']);

				if(!$validation->check())
				{
					RD::set_array(RD::ERROR, $validation->errors('auctions'));
				}
				else
				{
					// Create the auction
					$auction = ORM::factory('User_Auction')
						->values($_POST, ['min_bid', 'increment', 'auto_buy']);
					$auction->user_id = Fusion::$user->id;
					$auction->until = strtotime('+'.$lengths[$_POST['until']]);
					$auction->save();

					// Move the item
					$item->move('auction', 1, FALSE, $auction->id);

					RD::success('Thanks for creating an auction for your :item', [':item' => $item->item->name]);
					Database::instance()->commit();
				}
			}
		}
		catch(ORM_Validation_Exception $e)
		{
			// Rollback any queries that were performed
			Database::instance()->rollback();

			RD::set_array(RD::ERROR, $e->errors('model'));
		}

		$this->redirect(Route::url('auctions.index', null, true));
	}

	/**
	 * Show user's bids
	 */
	public function action_bids()
	{

	}

	/**
	 * Show user's created auctions
	 */
	public function action_list()
	{

	}

	/**
	 * Search for an item in auction
	 */
	public function action_search()
	{

	}
}
