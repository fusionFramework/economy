<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * User shop controller
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Controller_Shop extends Controller_Fusion_Site {
	protected $_shop = NULL;
	protected $_menu = null;

	/**
	 * Manage shop settings
	 */
	public function action_index()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		$config = Kohana::$config->load('shop');

		$this->_tpl = new View_Shop_Index;
		$this->_tpl->shop = $this->_shop->as_array();

		$this->_tpl->units = ($config['size']['active']) ? $config['size'] : FALSE;

	}

	/**
	 * Upgrade a shop's size
	 */
	public function action_upgrade()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		$config = Kohana::$config->load('shop.size');

		//if the shops are upgradeable
		if ($config['active'] == TRUE)
		{
			if (Fusion::$user->points($config['unit_cost'], '-') == true)
			{
				$this->_shop->size += 1;
				$this->_shop->save();

				RD::success('Your shop can now offer a maximum of :limit items.', [':limit' => $config['unit_size'] * $this->_shop->size]);
			}
			else
			{
				RD::error('You don\'t have enough :currency to upgrade your shop', [':currency' => Fusion::$config['currency']['plural']]);
			}
		}

		$this->redirect(Route::url('shop.index', null, true));
	}

	/**
	 * Process shop settings
	 *
	 * @throws HTTP_Exception_404
	 */
	public function action_update()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		if ($this->request->method() == HTTP_Request::POST)
		{
			try
			{
				// Save the submitted title and description
				$this->_shop
					->values($this->request->post(), ['title', 'description'])
					->save();

				RD::success('Your shop has been updated.');
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('models');

				foreach ($errors as $error)
				{
					RD::error($error);
				}
			}
		}
		else
			Throw new HTTP_Exception_404();

		$this->redirect(Route::url('shop.index', null, true));
	}

	/**
	 * Create a shop
	 */
	public function action_create()
	{
		$shop = $this->_check_shop();

		//if the user already has a shop redirect to index
		if ($shop->loaded())
		{
			$this->redirect(Route::url('shop.index', null, true));
		}

		$config = Kohana::$config->load('shop');

		$db = Database::instance();
		if ($this->request->method() == HTTP_Request::POST)
		{
			try
			{
				$db->begin();

				// If there's a creation cost
				if ($config['creation_cost'] != FALSE || $config['creation_cost'] > 0)
				{
					// charge the user
					if (Fusion::$user->points($config['creation_cost'], '-') == false)
					{
						// seems like he does not have enough points
						RD::error('You can\'t afford to open a shop!');

						$this->redirect(Route::url('shop.create', null, true));
					}
				}

				//create the shop
				$shop = ORM::factory('User_Shop')
					->values($this->request->post(), ['title', 'description']);

				$shop->user_id = Fusion::$user->id;
				$shop->size = 1;
				$shop->till = 0;
				$shop->save();

				RD::success('You\'ve successfully created your own shop, congratulations!');

				// Commit the changes made to the database
				$db->commit();

				$this->redirect(Route::url('shop.index', null, true));
			}
			catch (ORM_Validation_Exception $e)
			{
				// Roll back any changes made to the database
				$db->rollback();

				$errors = $e->errors('models');

				foreach ($errors as $error)
				{
					RD::error($error);
				}

				$this->redirect(Route::url('shop.create', null, true));
			}
		}
		else
		{
			$this->_tpl = new View_Shop_Create;

			if ($config['creation_cost'] != FALSE || $config['creation_cost'] > 0)
			{
				$this->_tpl->creation = [
					'cost' => $config['creation_cost'],
					'affordable' => (Fusion::$user->setting('points', 0) < $config['creation_cost'])
				];
			}
		}
	}

	/**
	 * Manage the shop's stock
	 */
	public function action_stock()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		$config = Kohana::$config->load('shop');

		$this->_tpl = new View_Shop_Stock;

		$items = Item::location('shop');

		$pagination = Paginate::factory($items, $config['stock']['pagination'])->execute();

		$this->_tpl->items = $pagination->result();
		$this->_tpl->pagination = $pagination->render();
		$this->_tpl->page = $this->request->param('page');
		$this->_tpl->inventory_url = Route::url('shop.inventory', null, true);
	}

	/**
	 * Process changes to the shop's stock
	 *  - Remove item
	 *  - Update price
	 */
	public function action_inventory()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		if ($this->request->method() == HTTP_Request::POST AND count($this->request->post('item')) > 0)
		{
			$lost_items = 0;
			$errors = FALSE;
			foreach ($this->request->post('item') as $id => $param)
			{
				$item = ORM::factory('User_Item', $id);

				if ( ! $item->loaded())
				{
					$lost_items++;
					$errors = TRUE;
				}
				else if ($item->user_id != Fusion::$user->id)
				{
					RD::error('you\'re trying to change an item you don\'t own');
					$errors = TRUE;
				}
				else if ($item->location != 'shop')
				{
					RD::error('You\'re trying to change an item that\'s not located in your shop');
					$errors = TRUE;
				}
				else if (isset($param['remove']) AND $param['remove'] == 1)
				{
					//move the item to the inventory
					$item->move('inventory', '*');
				}
				else if (Valid::digit($param['price']) AND $param['price'] > -1)
				{
					//update the item's price
					$item->parameter = $param['price'];
					$item->save();
				}
			}

			if ($lost_items > 0)
			{
				RD::error('Some items don\'t seem to exist anymore.');
			}
			else if ($errors != TRUE)
			{
				RD::success('You\'ve successfully updated your shop\'s stock.');
			}
		}

		$this->redirect(Route::url('shop.stock', ['page' => $this->request->param('page', 1)], true));
	}

	/**
	 * View shop's logs
	 */
	public function action_logs()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		$this->_tpl = new View_Shop_Logs;

		// load logs
		$logs = ORM::factory('Log')
			->where('alias', '=', 'buy.' . $shop->id)
			->where('time', '>', strtotime('-'.Kohana::$config->load('shop.log_retention')))
			->limit(Kohana::$config->load('shop.log_limit'))
			->order_by('id', 'DESC')
			->find_all();

		$this->_tpl->logs = $logs;
		$this->_tpl->earnings = $shop->till;
	}

	/**
	 * Collect money from the shop's earnings.
	 *
	 * @throws HTTP_Exception_404
	 */
	public function action_collect()
	{
		$shop = $this->_check_shop();

		//if the user does not have a shop redirect to create
		if (!$shop->loaded())
		{
			$this->redirect(Route::url('shop.create', null, true));
		}

		if ($this->request->method() == HTTP_Request::POST)
		{
			$amount = $this->request->post('amount');

			if (!Valid::digit($amount))
			{
				RD::error('The specified amount is unreadable');
			}
			else if($amount <= 0)
			{
				RD::error('If you want to collect earnings make sure the number is higher than 0.');
			}
			else if ($amount > $this->_shop->till)
			{
				RD::error('You\'re trying to collect more :currency than you have in your shop till.', [
					':currency' => Fusion::$config['currency']['plural']
				]);
			}
			else if ($amount > 0)
			{
				// Add the points to the user's points
				Fusion::$user->points($amount);

				// Remove from till
				$this->_shop->till -= $amount;
				$this->_shop->save();

				RD::success('You\'ve successfully withdrawn :amount :currency from your shop till.', [
					':amount' => $amount,
					':currency' => Fusion::$config['currency']['plural']
				]);
			}
		}
		else
			Throw new HTTP_Exception_404;

		$this->redirect(Route::url('shop.logs', null, true));
	}

	/**
	 * View a shop
	 *
	 * @throws HTTP_Exception_404
	 */
	public function action_view()
	{
		$id = $this->request->param('id');

		$shop = ORM::factory('User_Shop', $id);

		if ($shop->loaded())
		{
			$this->_tpl = new View_Shop_View;
			$this->_tpl->shop = $shop->as_array();
			$this->_tpl->owner = $shop->user->as_array();

			// load all items from the shop with a price higher than 0
			$inventory = Item::location('shop')
				->where('parameter', '>', '0')
				->find_all();

			$this->_tpl->items = $inventory;
		}
		else
			Throw new HTTP_Exception_404;
	}

	/**
	 * Buy an item from the shop
	 */
	public function action_buy()
	{
		$shop = ORM::factory('User_Shop', $this->request->param('id'));

		//if no shop's found redirect to previous page
		if (!$shop->loaded())
		{
			$this->redirect($this->request->referrer());
		}

		// Owner can't buy his own items
		if($shop->user_id == Fusion::$user->id)
		{
			RD::error('You can\'t buy an item from your own shop');

			$this->redirect(Route::url('shop.view', ['id' => $shop->id], true));
		}

		if ($this->request->param('item_id', false) != false)
		{
			$item_id = $this->request->param('item_id');

			$item = ORM::factory('User_item', $item_id);

			if (!$item->loaded() OR $item->location != 'shop')
			{
				RD::error('This item is not in stock');
			}
			else if($item->parameter == 0)
			{
				RD::error('This item is not for sale.');
			}
			else if (Fusion::$user->setting('points', 0) < $item->parameter)
			{
				RD::error('You don\'t have enough :currency to buy a ":item_name"', [':item_name' => $item->item->name]);
			}
			else
			{
				//subtract the points
				Fusion::$user->points($item->parameter, '-');

				//log this action
				$log = Fusion::$log->create('buy.' . $shop->id, 'shop', 'Bought 1 :item_name for :price from :other_user', [
					':alias_id' => $item->item_id,
					':item_name' => $item->item->name,
					':shop_owner' => $item->user->username,
					':price' => $item->parameter
				]);

				// transfer the item
				$item->transfer(Fusion::$user);

				//send notification
				if(Kohana::$config->load('shop.notify_sale') == true)
				{
					$log->notify($shop->user, 'shop.buy');
				}

				RD::success('You\'ve successfully bought :item_name from :owner for :price :currency', [
					':owner' => $shop->user->username,
					':item_name' => $item->item->name('1'),
					':price' => $item->parameter,
					':currency' => Fusion::$config['currency']['plural']
				]);
			}
		}

		$this->redirect(Route::url('shop.view', ['id' => $shop->id], true));
	}

	public function after()
	{
		if ($this->_tpl != NULL)
		{
			$map_links = array('index', 'stock', 'logs');

			if (in_array($this->request->action(), $map_links))
			{
				$this->_tpl->menu = $this->_menu->render('Menu', 'button_group');
			}
		}
		parent::after();
	}

	protected function _check_shop()
	{
		$this->_shop = ORM::factory('User_Shop')
			->where('user_id', '=', Fusion::$user->id)
			->find();

		if($this->_shop->loaded())
		{
			$this->_menu = Element::factory('shops');
			$this->_menu->get_item('shop.view')->param('id', $this->_shop->id);
		}

		return $this->_shop;
	}
}
