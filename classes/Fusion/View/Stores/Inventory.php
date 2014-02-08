<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Shop inventory view data
 *
 * View a shop & its contents
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Stores_Inventory extends Views
{

	public function title()
	{
		return $this->store->title;
	}

	/**
	 * @var Model_Store Store information
	 */
	public $store = false;

	/**
	 * @var array Contains all items the store has in stock
	 */
	public $inventory = [];

	/**
	 * @var Model_NPC Contains the store's NPC model
	 */
	public $npc = false;

	/**
	 * Simplify inventory data
	 *
	 * @return array
	 */
	public function items()
	{
		$list = array();

		if (count($this->inventory) > 0)
		{
			foreach ($this->inventory as $item)
			{
				$list[] = array(
					'link' => Route::url('stores.buy', array('store_id' => $this->store->id, 'item_id' => $item->id), true),
					'name'        => ($item->stock == 1) ? $item->item->name : Inflector::plural($item->item->name),
					'id'          => $item->id,
					'stock'       => $item->stock,
					'price'       => $item->price,
					'img'         => $item->item->img()
				);
			}
		}

		return $list;
	}

	/**
	 * Parse the NPC
	 *
	 * @return array
	 */
	public function npc()
	{
		return [
			'img' => $this->npc->img(),
			'msg' => __($this->npc->message('introduction'), [':store_name' => $this->store->title, ':name' => $this->npc->name])
		];
	}
}
