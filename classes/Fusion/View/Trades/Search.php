<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade search view data
 *
 * Search lots for an item
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Trades_Search extends Views
{

	public $title = 'Trade lots';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the found items
	 * @var Model_User_Item
	 */
	public $items = array();

	/**
	 * Holds the search term
	 * @var string
	 */
	public $term = FALSE;

	/**
	 * Stores the navigation
	 * @var array
	 */
	public $trade_nav = array();

	/**
	 * Simplify lot data and add linked items
	 * @return array
	 */
	public function lots()
	{
		$list = array();

		if (count($this->items) > 0)
		{
			foreach ($this->items as $item)
			{
				$lot = ORM::factory('User_Trade', $item->parameter_id);

				$inventory = array();

				foreach ($lot->items() as $i)
				{
					$inventory[] = array(
						'name' => $i->name(),
						'img'  => $i->img()
					);
				}

				$list[] = array(
					'id'           => $lot->id,
					'bid_link'     => Route::url('trades.bid', array('id' => $lot->id)),
					'lot_link'     => Route::url('trades.lot', array('id' => $lot->id)),
					'description'  => $lot->description,
					'inventory'    => $inventory,
					'username'     => $lot->user->username,
					'user_profile' => Route::url('user.profile', array('name' => $lot->user->username))
				);
			}
		}

		return $list;
	}
}
