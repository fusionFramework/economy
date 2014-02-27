<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade bids view data
 *
 * (view bids the user has made on other lots)
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Trades_Bids extends Views {

	public $title = 'Trade bids';

	/**
	 * Stores a bid
	 * @var array|false
	 */
	public $bids = FALSE;

	/**
	 * The image URL to the defined currency image
	 * @var string|false
	 */
	public $currency_image = FALSE;

	/**
	 * Total amount of bids the user has made
	 * @var integer
	 */
	public $count = 0;

	/**
	 * Return a simplified bid data definition.
	 *
	 * @return array
	 */
	protected function _bid($bid)
	{
		if ($bid != NULL)
		{
			$items = array();

			foreach ($bid->items() as $item)
			{
				$items[] = array('name' => $item->name(), 'img' => $item->img());
			}

			return array(
				'id'        => $bid->id,
				'points'    => ($bid->points > 0) ? array('amount' => $bid->points) : FALSE,
				'username'  => $bid->lot->user->username,
				'lot'       => Route::url('trades.lot', array('id' => $bid->lot_id)),
				'lot_id'    => $bid->lot_id,
				'profile'   => Route::url('user.profile', array('name' => $bid->lot->user->username)),
				'inventory' => $items,
				'retract'   => Route::url('trades.retract', array('id' => $bid->id))
			);
		}

		return FALSE;
	}

	/**
	 * Simplify bid data
	 * @return array
	 */
	public function bids()
	{
		$list = array();

		$bids = $this->bids;

		if (count($bids) > 0)
		{
			foreach ($bids as $bid)
				$list[] = $this->_bid($bid);
		}

		return $list;
	}
}
