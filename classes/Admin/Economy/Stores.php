<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * Store admin
 *
 * @package    fusionFramework/economy
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Admin_Economy_Stores extends Admin
{
	public  $resource = "stores";
	public $icon = 'fa fa-shopping-cart-o';
	public $primary_key = 'store.id';
	public $track_changes = TRUE;

	/**
	 * Set up the dataTable definition for this controller.
	 *
	 * @see Table
	 *
	 * @param Table $table
	 *
	 * @return Table A fully configured dataTable definition
	 */
	public function setup_table($table)
	{
		$table->add_column('title', array('head' => 'Title', 'class' => 'col-lg-4'));
		$table->add_column('stock_type', array('head' => 'Stock', 'class' => 'col-lg-2'));
		$table->add_column('stock_cap', array('head' => 'Stock cap', 'class' => 'col-lg-2'));
		$table->add_column('status', array('head' => 'Status', 'class' => 'col-lg-2'), true, false);
		$table->add_button('stock-item', 'fa fa-lemon-o', 'primary', 'after', 'edit');

		return $table;
	}

	protected function _setup()
	{
		$this->model = ORM::factory('Store');

		// a wider modal is needed for managing the item commands
		$this->modal['width'] = 650;
		$this->modal['height'] = 400;

		$this->_assets['set'][] = 'typeahead';
		$this->_assets['js'][] = 'plugins/bootbox.min.js';
		$this->_assets['js'][] = 'admin/store.js';
	}

	/**
	 * Add restocks when the modal requests a record.
	 *
	 * @param ORM $record
	 * @return array
	 */
	public function load(ORM $record)
	{
		$restocks = [];
		$records = $record->restocks->find_all();
		foreach($records as $r)
		{
			$restocks[] = array_merge($r->as_array(), ['item_name' => $r->item->name]);
		}
		return array('restocks' => $restocks);
	}

	public function modal(Array $data)
	{
		$data['routes']['item'] = [
			'save' => Route::url('admin.stores.item_save', null, true),
			'load' => Route::url('admin.stores.item_load', null, true),
			'remove' => Route::url('admin.stores.item_remove', null, true)
		];
		$data['typeAhead'] = Admin::typeAhead_tpl('item');
		return View::factory('admin/modal/stores', $data);
	}

	public $actions = ['item_save', 'item_remove', 'item_load'];

	/**
	 * Add an item to a store's stock.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function action_item_save(Request $request, Response $response)
	{
		$values = $request->post();

		if(isset($values['store_id']))
		{
			$store = ORM::factory('Store', $values['store_id']);
		}
		else if(isset($values['store_name']))
		{
			$store = ORM::factory('Store')
				->where('title', '=', $values['store_name'])
				->find();
		}


		if(!$store->loaded())
		{
			RD::set(RD::ERROR, 'No store found to add this item to.');
		}
		else
		{
			$item = ORM::factory('Item')
				->where('item.name', '=', $values['item_name'])
				->find();

			if(!$item->loaded())
			{
				RD::set(RD::ERROR, 'No item found by the name of "'.$values['item_name'].'".');
			}
			else
			{
				try {
					$values['store_id'] = $store->id;
					$values['item_id'] = $item->id;

					if($values['id'] == '')
					{
						$restock = ORM::factory('Store_Restock')
							->values($values, ['item_id', 'store_id', 'min_price', 'max_price', 'min_amount', 'max_amount', 'cap_amount', 'frequency'])
							->save();

						RD::set(RD::SUCCESS, $item->name.' has been added to the "'.$store->title.'" store\'s stock.', null, array_merge($restock->as_array(), ['item_name' => $item->name]));
					}
					else
					{
						$restock = ORM::factory('Store_Restock', $values['id'])
							->values($values, ['item_id', 'store_id', 'min_price', 'max_price', 'min_amount', 'max_amount', 'cap_amount', 'frequency'])
							->save();

						RD::set(RD::SUCCESS, $item->name.' has been added updated.', null, array_merge($restock->as_array(), ['item_name' => $item->name]));
					}

				}
				catch(ORM_Validation_Exception $e)
				{
					RD::set(RD::ERROR, 'Problem validating form', null, array('errors' => $e->errors('orm'), 'submit_data' => $values));
				}
			}
		}
	}

	/**
	 * Load an item from a store's stock.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function action_item_load(Request $request, Response $response)
	{
		$values = $request->query();

		$restock = ORM::factory('Store_Restock', $values['restock_id']);

		if(!$restock->loaded())
		{
			RD::set(RD::ERROR, 'No restock record found.');
		}
		else
		{
			RD::set(RD::SUCCESS, 'Item loaded', null, array_merge($restock->as_array(), ['item_name' => $restock->item->name]));
		}
	}

	/**
	 * Remove an item from a store's stock.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function action_item_remove(Request $request, Response $response)
	{
		$values = $request->query();

		$restock = ORM::factory('Store_Restock', $values['restock_id']);

		if(!$restock->loaded())
		{
			RD::set(RD::ERROR, 'No restock record found.');
		}
		else
		{
			$restock->delete();
			RD::set(RD::SUCCESS, 'Item successfully removed from the store\'s stock');
		}
	}
}