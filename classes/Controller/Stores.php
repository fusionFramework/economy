<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Shops controller
 *
 * List shops & buy items
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Controller_Stores extends Controller_Fusion_Site {

	/**
	 * @todo Overwrite this method if you don't want to list all your game's shops
	 */
	public function action_index()
	{
		$this->_tpl = new View_Stores_List;

		$stores = ORM::factory('Store')
			->where('status', '=', 'open')
			->find_all();

		$this->_tpl->stores = $stores;
	}

	/**
	 * View a store
	 */
	public function action_view()
	{
		$id = $this->request->param('id');

		$store = ORM::factory('Store')
			->where('store.id', '=', $id)
			->with('npc')
			->find();

		if (!$store->loaded())
		{
			RD::warning('Store could not be found');
			$this->redirect(Route::url('stores.index', null, true));
		}
		else if ($store->status != 'open')
		{
			//check if the user can access closed stores
			$this->access('store.closed');
		}

		Fusion::$assets->add_set('notifications');
		Fusion::$assets->add_set('modals');
		Fusion::$assets->add_set('req');
		Fusion::$assets->add_js('plugins/bootbox.min.js');

		if(Kohana::$config->load('items.stores.haggle') == true)
		{
			$this->_tpl = new View_Stores_Haggle;
			Fusion::$assets->add_js('stores/haggle.js');
		}
		else
		{
			$this->_tpl = new View_Stores_Inventory;
			Fusion::$assets->add_js('stores/inventory.js');
		}

		$this->_tpl->store = $store;
		$this->_tpl->npc = $store->npc;
		$this->_tpl->inventory = $store->items->where('stock', '>', 0)->find_all();
	}

	/**
	 * buy an item from a store
	 */
	public function action_buy()
	{
		$store_id = $this->request->param('store_id');
		$item_id = $this->request->param('item_id');

		$item = ORM::factory('Store_Inventory')
			->where('store_inventory.id', '=', $item_id)
			->where('store_inventory.store_id', '=', $store_id)
			->with('store:npc')
			->with('item')
			->find();

		$price = false;

		if(!$item->loaded())
		{
			RD::error('The item you want to buy does not seem to be in stock');
		}
		else if(Kohana::$config->load('items.stores.haggle') == true && isset($_POST['price']))
		{
			//maximum haggle price must be higher than 84%-96% of the original price
			$haggle = rand(84,96) / 100;


			if($_POST['price'] > $haggle*$item->price && $_POST['price'] < 1.05 * $item->price)
			{
				$price = $_POST['price'];
			}
			else
			{
				RD::error($item->store->npc->message('price_low'), [
					':price' => $_POST['price'],
					':item' => $item->item->name
				], ['stock' => $item->stock, 'id' => $item->id]);
			}
		}
		else
		{
			$price = $item->price;
		}

		if($price != false)
		{
			try {
				$item->buy(Fusion::$user, $price);

				RD::success($item->store->npc->message('sale_success'), [':item' => $item->item->name, ':price' => $price], ['stock' => $item->stock, 'id' => $item->id]);
			}
			catch(Kohana_Exception $e)
			{
				RD::error($e->getMessage());
			}
		}

		$this->redirect(Route::url('stores.view', ['id' => $store_id], true));
	}
}

