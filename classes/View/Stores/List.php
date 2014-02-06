<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Shop view data
 *
 * View a list of open shops
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class View_Stores_List extends Views
{
	public $title = 'Stores';

	/**
	 * Contains all open stores
	 * @var array
	 */
	public $stores = array();

	/**
	 * Simplify store data
	 * @return array
	 */
	public function stores()
	{
		$list = array();

		if (count($this->stores) > 0)
		{
			foreach ($this->stores as $store)
			{
				$list[] = array(
					'link' => Route::url('stores.view', array('id' => $store->id), true),
					'name'        => $store->title,
					'id'          => $store->id,
					'stock'       => $store->items->where('stock', '>', 0)->count_all()
				);
			}
		}

		return $list;
	}
}
