<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Inventory list view data
 *
 * View all items in inventory
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Inventory_Index extends Views
{
	public $title = 'Inventory';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * @var int Total amount of items the user has in his inventory
	 */
	public $total_items = 0;

	/**
	 * Stores the user's inventory items
	 * @var array
	 */
	public $items = array();

	/**
	 * @var int The maximum amount of items a user can have in his inventory
	 */
	public $limit = 0;

	/**
	 * Simplify item data
	 * @return array
	 */
	public function items()
	{
		$list = array();

		if (count($this->items) > 0)
		{
			foreach ($this->items as $item)
			{
				$list[] = array(
					'action_link' => Route::url('inventory.view', array('id' => $item->id), true),
					'img'         => $item->img(),
					'name'        => ($item->amount > 1) ? Inflector::plural($item->item->name) : $item->item->name,
					'amount'      => $item->amount,
					'id'          => $item->id
				);
			}
		}

		return $list;
	}
}
