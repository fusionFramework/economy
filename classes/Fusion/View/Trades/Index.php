<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade index view data
 *
 * View active lots
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Trades_Index extends Views
{

	public $title = 'Trade lots';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the trade lots
	 * @var unknown_type
	 */
	public $lots = array();

	/**
	 * Stores the navigation
	 * @var array
	 */
	public $trade_nav = array();

	/**
	 * Simplify lot data and add linked item
	 * @return array
	 */
	public function lots()
	{
		$list = array();

		if (count($this->lots) > 0)
		{
			foreach ($this->lots as $lot)
			{
				$inventory = array();

				foreach ($lot->items() as $item)
				{
					$inventory[] = array(
						'name' => $item->name(),
						'img'  => $item->img()
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
