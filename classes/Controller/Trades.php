<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item trade controller
 *
 * Trade items
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Controller_Trades extends Controller_Fusion_Site
{
	/**
	 * Add the trades menu to the template
	 */
	public function after()
	{
		if($this->_tpl != null)
		{
			$this->_tpl->menu = Element::factory('trades')->render('Menu', 'button_group');
		}
		parent::after();
	}

	/**
	 * Show all available lots
	 */
	public function action_index()
	{
		$config = Kohana::$config->load('items.trade.lots');
		$max_lots = $config['max_results'];

		$lots = ORM::factory('User_Trade')
			->order_by('user_trade.id', 'DESC');

		$paginate = Paginate::factory($lots, array('total_items' => $max_lots))->execute();

		$this->_tpl = new View_Trades_Index;
		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->lots = $paginate->result();
	}

	/**
	 * Show user's lots
	 */
	public function action_lots()
	{
		$config = Kohana::$config->load('items.trade.lots');
		$max_lots = $config['max_results'];

		$lots = ORM::factory('User_Trade')
			->where('user_id', '=', Fusion::$user->id);

		$paginate = Paginate::factory($lots, array('total_items' => $max_lots))->execute();

		$this->_tpl = new View_Trades_Lots;
		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->lots = $paginate->result();
	}

	/**
	 * Create a new lot
	 */
	public function action_create()
	{
		$items = Item::location('inventory', TRUE)->find_all();

		$this->_tpl = new View_Trades_Create;

		if (count($items) == 0)
		{
			RD::error('You don\'t have any items in your inventory to put up for trade.');
			$this->_tpl->unable = TRUE;
		}
		else
		{
			$this->_tpl->items = $items;
			$this->_tpl->max_items = Kohana::$config->load('items.trade.lots.max_items');
			$this->_tpl->max_type = (Kohana::$config->load('items.trade.lots.count_amount')) ? 'items' : 'stacks';
			$this->_tpl->process_url = Route::url('trades.create.process');
		}
	}

	/**
	 * Process a lot
	 */
	public function action_process_create()
	{
		$config = Kohana::$config->load('items.trade.lots');

		if ($this->request->method() != HTTP_Request::POST)
		{
			$this->redirect(Route::url('item.trade.create', null, true));
		}

		if ($config['max_items'] < count($this->request->post('items')))
		{
			RD::error('You can\'t create a trading lot with more than :amount items', array(':amount' => $config['max_items']));
		}
		else
		{
			$items = $this->request->post('items');
			$a_count = 0;
			$stored_items = array();

			Database::instance()->begin();

			//let's start by validating and moving the items away from the inventory
			foreach ($items as $id => $amount)
			{
				if (!empty($amount) && $amount > 0)
				{
					$item = ORM::factory('User_Item', $id);

					if (!$item->loaded())
					{
						RD::error('You want to trade an item that does not seem to exist.');
						break;
					}
					if ($item->location != 'inventory')
					{
						RD::error('You can only trade items from your inventory.');
						break;
					}
					if ($item->user_id != Fusion::$user->id)
					{
						RD::error('You can\'t trade an item that isn\'t yours.');
						break;
					}
					if ($item->amount < $amount)
					{
						RD::error('You only have :items, you\'re trying to trade :amount', array(
							':items' => $item->name(),
						    ':amount' => $amount
						));
						break;
					}

					$a_count += $amount;

					if ($config['count_amount'] == TRUE && $a_count > $config['max_items'])
					{
						RD::error('You can\'t trade more than a total of :amount items.', array(':amount' => $config['max_items']));
						break;
					}

					$stored_items[] = $item->move('trade.lot', $amount, FALSE);
				}
			}

			if (count(RD::get_current(RD::ERROR)) > 0)
			{
				Database::instance()->rollback();
				$this->redirect(Route::url('trades.create', null, true));
			}
			else
			{
				try
				{
					$lot = ORM::factory('User_Trade');
					$lot->user_id = Fusion::$user->id;
					$lot->description = $this->request->post('description');
					$lot->save();

					//point the items to the created lot
					foreach ($stored_items as $item)
					{
						$item->parameter_id = $lot->id;
						$item->save();
					}
				} catch (Kohana_ORM_Validation_Exception $e)
				{
					foreach ($e->errors('models') as $error)
					{
						RD::error($error);
					}

					Database::instance()->rollback();

					$this->redirect(Route::url('trades.create', null, true));
				}

				Database::instance()->commit();
				RD::success('You\'ve successfully created your trading lot!');

				$this->redirect(Route::url('trades.lot', array('id' => $lot->id), true));
			}
		}
	}

	/**
	 * Retract a bid
	 */
	public function action_delete()
	{
		$id = $this->request->param('id');

		$lot = ORM::factory('User_Trade', $id);

		if (!$lot->loaded())
		{
			RD::error('You tried deleting a lot that does not exists.');
			$this->redirect(Route::url('trades.index', null, true));
		}
		else if ($lot->user_id != Fusion::$user_id)
		{
			RD::error('You tried deleting a lot that isn\'t yours.');
			$this->redirect(Route::url('trades.index', null, true));
		}

		$bids = $lot->bids->find_all();

		//remove all bids made to this lot
		if (count($bids) > 0)
		{
			$log = Fusion::$log->create('item.trade.' . $id . '.delete', 'item', 'Trade #id deleted', array(':id' => $id));

			foreach ($bids as $bid)
			{
				$items = $bid->items();

				foreach ($items as $item)
				{
					$item->move('inventory', $item->amount);
				}

				if ($bid->points > 0)
				{
					$bid->user->points($bid->points);
				}

				$log->notify($bid->user, 'item.trades.delete', array(':lot' => $id));

				$bid->delete();
			}
		}

		//move back the lot's items to the inventory
		foreach ($lot->items() as $item)
		{
			$item->move('inventory', '*');
		}

		$lot_id = $lot->id;

		$lot->delete();

		RD::success('You\'ve successfully cancelled lot #:lot', array(':lot' => $lot_id));
		$this->redirect(Route::url('trades.index', null, true));
	}

	/**
	 * View a lot
	 */
	public function action_lot()
	{
		$id = $this->request->param('id');

		$lot = ORM::factory('User_Trade', $id);
		$this->_tpl = new View_Trades_Lot;

		if (!$lot->loaded())
		{
			RD::error('The lot you want to load does not seem to exist.');
		}
		else
		{
			$this->_tpl->lot = $lot;
			$this->_tpl->currency_image = Fusion::$config['currency']['image'];

			//let's see if the user has put down a bid on this lot
			if (Fusion::$user->id != $lot->user_id)
			{
				$bid = ORM::factory('User_Trade_bid')
					->where('user_id', '=', Fusion::$user->id)
					->where('lot_id', '=', $lot->id)
					->find();

				if ($bid->loaded())
				{
					$this->_tpl->bid = $bid;
				}
			}
			else
			{
				//the owner's view
				$this->_tpl->owner_actions = TRUE;
			}
		}
	}

	/**
	 * Bid on a lot
	 */
	public function action_bid()
	{
		$id = $this->request->param('id');

		$lot = ORM::factory('User_Trade', $id);
		$this->_tpl = new View_Trades_Bid;

		if (!$lot->loaded())
		{
			RD::error('No trade lot found to bid on.');
			$this->_tpl->unable = TRUE;
		}
		else if (Item::location('inventory')->count_all() == 0)
		{
			RD::error('You don\'t have any items in your inventory to make a bid with.');
			$this->_tpl->unable = TRUE;
		}
		else
		{
			$this->_tpl->lot = $lot;
			$this->_tpl->items = Item::location('inventory', TRUE)->find_all();
			$this->_tpl->max_items = Kohana::$config->load('items.trade.bids.max_items') - 1;
			$this->_tpl->max_type = (Kohana::$config->load('items.trade.bids.count_amount')) ? 'items' : 'stacks';
			$this->_tpl->process_url = Route::url('trades.bid.process', array('id' => $lot->id));
		}
	}

	/**
	 * Process a bid
	 */
	public function action_process_bid()
	{
		$id = $this->request->param('id');
		$config = Kohana::$config->load('items.trade.bids');

		if ($this->request->method() != HTTP_Request::POST)
		{
			$this->redirect(Route::url('trades.bid', array('id' => $id), true));
		}

		$points = $this->request->post('points');

		if ($config['max_items'] < count($this->request->post('items')))
		{
			RD::error('You can\'t bid on a lot with more than :amount items', array(':amount' => $config['max_items']));
		}
		else if (!empty($points) && (!Valid::digit($points) || points < 0))
		{
			RD::error('If you want to add points to your bid specify a number (:points)', array(':points' => $points));
		}
		else if (Valid::digit($points) && points > Fusion::$user->setting('points', 0))
		{
			RD::error('You don\'t have enough points to add to this bid (:points)', array(':points' => $points));
		}
		else
		{
			$items = $this->request->post('items');
			$a_count = 0;
			$stored_items = array();

			Database::instance()->begin();

			//let's start by validating and moving the items away from the inventory
			foreach ($items as $id => $amount)
			{
				if (!empty($amount) && $amount > 0)
				{
					$item = ORM::factory('User_Item', $id);

					if (!$item->loaded())
					{
						RD::error('You want to bid an item that does not seem to exist.');
						break;
					}
					if ($item->location != 'inventory')
					{
						RD::error('You can only bid items from your inventory.');
						break;
					}
					if ($item->user_id != Fusion::$user->id)
					{
						RD::error('You can\'t bid an item that isn\'t yours.');
						break;
					}
					if ($item->amount < $amount)
					{
						RD::error('You only have :items, you\'re trying to bid :amount', array(
							':items' => $item->name(),
							':amount' => $amount
						));
						break;
					}

					$a_count += $amount;

					if ($config['count_amount'] == TRUE && $a_count > $config['max_items'])
					{
						RD::error('You can\'t bid more than a total of :amount items.', array(
							':amount' => $config['max_items']
						));
						break;
					}

					$stored_items[] = $item->move('trade.bid', $amount, FALSE);
				}
			}

			//check stack total if needed
			if ($config['count_amount'] == FALSE && count($stored_items) > $config['max_items'])
			{
				RD::error('You can\'t bid more than a total of :amount different items.', array(
					':amount' => $config['max_items']
				));
			}
			//check stack amount total if needed
			else if ($config['count_amount'] == FALSE && $a_count > $config['max_in_stack'])
			{
				RD::error('You can\'t bid more than a total of :amount items.', array(':amount' => $config['max_in_stack']));
			}

			if (count(RD::get_current(RD::ERROR)) > 0)
			{
				Database::instance()->rollback();
				$this->redirect(Route::url('trades.bid', array('id' => $id), true));
			}
			else
			{
				try
				{
					$bid = ORM::factory('User_Trade_bid');
					$bid->lot_id = $id;
					$bid->user_id = Fusion::$user->id;

					//deduct points if specified
					if (Valid::digit($points))
					{
						Fusion::$user->points($points, '-');
						$bid->points = $points;
					}

					$bid->save();

					$item_names = array();
					//point the items to the created lot
					foreach ($stored_items as $item)
					{
						$item->parameter_id = $bid->id;
						$item_names[] = $item->name();
						$item->save();
					}
				} catch (Kohana_ORM_Validation_Exception $e)
				{
					foreach ($e->errors('models') as $error)
					{
						RD::error($error);
					}

					Database::instance()->rollback();

					$this->redirect(Route::url('trades.bid', array('id' => $id), true));
				}

				$log = Fusion::$log->create('item.trade.bid.' . $bid->lot_id, 'items', 'Made a bid with :amount items and :points points', array(
					':amount' => $a_count, ':points' => (int)$points, 'items' => $item_names));

				$log->notify($bid->lot_user, 'items.trades.bid', array(
					':user' => Fusion::$user->username,
					':lot' => '<strong>#<a href="' . Route::url('trades.lot', array('id' => $bid->lot_id)) . '">' . $bid->lot_id . '</a></strong>'
				));

				Database::instance()->commit();
				RD::success('You\'ve successfully made a bid!');

				$this->redirect(Route::url('trades.bids', null, true));
			}
		}
	}

	/**
	 * View all bids on a lot
	 */
	public function action_bids()
	{
		$bids = ORM::factory('User_Trade_Bid')
			->where('user_id', '=', Fusion::$user->id)
			->find_all();

		$this->_tpl = new View_Trades_Bids;
		$this->_tpl->currency_image = Fusion::$config['currency']['image'];
		$this->_tpl->bids = $bids;
		$this->_tpl->count = count($bids);
	}

	/**
	 * Accept a bid
	 */
	public function action_accept()
	{
		$id = $this->request->param('id');

		$bid = ORM::factory('User_Trade_Bid', $id);

		if (!$bid->loaded())
		{
			RD::error('No bid found to reject');
		}
		else if ($bid->trade->user_id != Fusion::$user->id)
		{
			RD::error('You can\'t accept a bid on a trade lot that isn\'t yours.');
			$this->redirect(Route::url('trades.lot', array('id' => $id), true));
		}
		else
		{
			$lot = $bid->trade;

			//send offered items to the trade's owner
			$offered_items = $bid->items();

			foreach ($offered_items as $item)
			{
				$item->transfer($lot->user, $item->amount);
			}

			//if points were added give them to the trade owner
			if ($bid->points > 0)
			{
				$user = $lot->user;
				$user->points($bid->points);
			}

			//send the items up for trade to the winning bidder
			$lot_items = $lot->items();

			foreach ($lot_items as $item)
			{
				$item->transfer($bid->user, $item->amount);
			}

			$log = Fusion::$log->create('item.trade.' . $id . '.accept', 'item', 'Trade #id completed', array(':id' => $id));
			$log->notify($user, 'items.trades.accept', array(':username' => Fusion::$user->username));

			RD::success('You\'ve accepted bid #:id made by :username', array(':id' => $bid->id, ':username' => $bid->user->username));

			$bid->delete();

			//reject all other bids
			$bids = ORM::factory('User_Trade_Bid')
				->where('trade_id', '=', $lot->id)
				->find_all();

			if(count($bids) > 0)
			{
				foreach($bids as $bid) {
					$items = $bid->items();

					foreach ($items as $item)
					{
						$item->move('inventory', $item->amount);
					}

					if ($bid->points > 0)
					{
						$user = $bid->user;
						$user->points($bid->points);
					}

					$log = Fusion::$log->create('item.trade.' . $id . '.reject', 'item', 'Bid from :user declined', array(':user' => Fusion::$user->username));
					$log->notify($user, 'items.trades.reject', array(':lot' => $id));
					$bid->delete();
				}
			}
		}

		$this->redirect(Route::url('trades.index', null, true));
	}

	/**
	 * Reject a bid
	 */
	public function action_reject()
	{
		$id = $this->request->param('id');

		$bid = ORM::factory('User_Trade_Bid', $id);

		if (!$bid->loaded())
		{
			return RD::error('No bid found to reject');
		}

		if ($bid->trade->user_id != Fusion::$user->id)
		{
			RD::error('You can\'t reject a bid on a trade lot that isn\'t yours.');
		}
		else
		{
			$items = $bid->items();

			foreach ($items as $item)
			{
				$item->move('inventory', $item->amount);
			}

			if ($bid->points > 0)
			{
				$user = $bid->user;
				$user->points($bid->points);
			}

			RD::success('You\'ve rejected bid #:id made by :username', array(':id' => $bid->id, ':username' => $user->username));

			$log = Fusion::$log->create('item.trade.' . $id . '.reject', 'item', 'Bid from :user declined', array(':user' => Fusion::$user->username));
			$log->notify($user, 'items.trades.reject', array(':lot' => $id));
			$bid->delete();
		}

		$this->redirect(Route::url('item.trade.lot', array('id' => $bid->trade_id), true));
	}

	/**
	 * Retract a bid
	 */
	public function action_retract()
	{
		$id = $this->request->param('id');

		$bid = ORM::factory('User_Trade_Bid', $id);

		if (!$bid->loaded())
		{
			//@todo change to HTTP exception
			return RD::error('No bid found to reject');
		}

		if ($bid->user_id != Fusion::$user->id)
		{
			RD::error('You can\'t retract a bid that isn\'t yours.');
		}
		else
		{
			$items = $bid->items();

			foreach ($items as $item)
			{
				$item->move('inventory', $item->amount);
			}

			if ($bid->points > 0)
			{
				Fusion::$user->points($bid->points);
			}

			RD::success('You\'ve retracted your bid');

			$log = Fusion::$log->create('item.trade.' . $id . '.retract', 'item', 'Retracted bid for :id', array(':id' => $id));
			$log->notify($log, $bid->lot->user, 'items.trades.retract', array(':lot' => $id, ':username' => Fusion::$user->username));

			$bid->delete();
		}

		$this->redirect(Route::url('trades.lot', array('id' => $bid->trade_id), true));
	}

	/**
	 * Search lots
	 */
	public function action_search()
	{
		// If it's a post request we'll route to the term
		if($this->request->method() == Request::POST)
		{
			$this->redirect(Route::url('trades.search', ['term' => $_POST['term']], true));
		}

		$term = $this->request->param("term", false);
		$limit = (isset($_GET['l'])) ? $_GET['l'] : 20;


		$this->_tpl = new View_Trades_Search;

		if($term != false)
		{
			$lots = ORM::factory('User_Item')
				->with('item')
				->where('user_item.location', '=', 'trade.lot')
				->where('item.name', 'LIKE', '%'.$term.'%');

			$paginate = Paginate::factory($lots, array('total_items' => $limit))
				->execute();

			$this->_tpl->term = $term;
			$this->_tpl->count_results = $paginate->count_total();
			$this->_tpl->pagination = $paginate->render();
			$this->_tpl->items = $paginate->result();
		}
	}
}