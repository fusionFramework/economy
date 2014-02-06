<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade bid view data
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class View_Trades_Bid extends Views
{

	public $title = 'Trade lots';

	/**
	 * transferable items that are located in the player's inventory
	 * @var array
	 */
	public $items = array();

	/**
	 * Maximum amount of items a user can trade
	 * @var integer
	 */
	public $max_items = 0;

	/**
	 * Contains trade lot data
	 * @var Model_User_Trade
	 */
	public $lot = FALSE;

	/**
	 * Simplify lot data
	 */
	public function lot()
	{
		if ($this->lot != FALSE && $this->lot->loaded())
		{
			$items = array();

			foreach ($this->lot->items() as $item)
			{
				$items[] = array(
					'name' => $item->name(),
					'img'  => $item->img(),
				);
			}

			return array(
				'id'          => $this->lot->id,
				'url'         => Route::url('trades.lot', array('id' => $this->lot->id)),
				'username'    => $this->lot->user->username,
				'profile'     => Route::url('user.profile', array('name' => $this->lot->user->username)),
				'inventory'   => $items,
				'description' => $this->lot->description
			);
		}

		return FALSE;
	}

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
					'id'   => $item->id,
					'name' => $item->name(),
					'img'  => $item->img(),
				);
			}
		}

		return $list;
	}
}
