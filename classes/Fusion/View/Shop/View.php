<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * User shop view data
 *
 * View a user shop & its inventory
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Shop_View extends Views
{

	public $title = 'Shop';

	/**
	 * Contains shop info
	 * @var array
	 */
	public $shop = FALSE;

	/**
	 * Contains the shop's owner info
	 * @var array
	 */
	public $owner = FALSE;

	/**
	 * Contains priced Model_User_Items
	 * @var array
	 */
	public $items = array();

	/**
	 * Parse the shop's items into inventory
	 * @return array
	 */
	public function inventory()
	{
		$list = array();

		if (count($this->items) > 0)
		{
			foreach ($this->items as $item)
			{
				$list[] = array(
					'id'    => $item->id,
					'name'  => $item->name(),
					'price' => $item->parameter,
					'img'   => $item->img(),
					'url'   => Route::url('shop.buy', ['id' => $this->shop['id'], 'item_id' => $item->id], true)
				);
			}
		}

		return $list;
	}

	/**
	 * simplifies the shop owner's data
	 * @return array
	 */
	public function owner()
	{
		return array('url' => Route::url('user.profile', array('name' => $this->owner['username'])), 'username' => $this->owner['username']);
	}
}
