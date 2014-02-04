<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * Store admin
 *
 * @package    fusionFramework/economy
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Admin_Economy_Store_Restock extends Admin
{
	public  $resource = "store.restock";
	public $icon = 'fa fa-shopping-cart-o';
	public $primary_key = 'store_restock.id';
	public $filter = 'store_id';
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
		$table->add_column('item', array('head' => 'Item', 'class' => 'col-lg-3', 'retrieve' => 'item.name'), false);
		$table->add_column('amount', array('head' => 'Amount', 'class' => 'col-lg-1', 'retrieve' => function($rec){
				return $rec->min_amount.' - '. $rec->max_amount;
			}), false, false);
		$table->add_column('price', array('head' => 'Price', 'class' => 'col-lg-2', 'retrieve' => function($rec){
				return $rec->min_price.' - '. $rec->max_price;
			}), false, false);
		$table->add_column('frequency', array('head' => 'Frequency', 'class' => 'col-lg-2'));
		$table->add_column('cap_amount', array('head' => 'Stock cap', 'class' => 'col-lg-2'));

		return $table;
	}

	protected function _setup()
	{
		$this->model = ORM::factory('Store_Restock');

		// a wider modal is needed for managing the item commands
		$this->modal['width'] = 650;
		$this->modal['height'] = 400;

		$this->_assets['set'][] = 'typeahead';
		$this->_assets['js'][] = 'admin/store_restock.js';

		//$store = ORM::factory('Store', Request::$current->param('id'));
		//$this->title = 'Restock for "'.$store->title.'"';
	}

	/**
	 * Add restocks when the modal requests a record.
	 *
	 * @param ORM $record
	 * @return array
	 */
	public function load(ORM $record)
	{
		return array('item_name' => $record->item->name, 'store' => $record->store->as_array());
	}

	public function save(ORM $restock, array $data, $namespace)
	{
		$data[$namespace]['item_id'] = ORM::factory('Item', ['item.name' => $data[$namespace]['item_name']])->id;
		unset($data[$namespace]['item_name']);

		return $data;
	}

	public function modal(Array $data)
	{
		$data['typeAhead'] = Admin::typeAhead_tpl('item');
		$data['store_id'] = Request::$current->param('id');
		return View::factory('admin/modal/store_restock', $data);
	}
}