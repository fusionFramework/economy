<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade Lot view data
 *
 * View a lot
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Trades_Lot extends Views
{

	public $title = 'Trade lots';

	/**
	 * Stores the trade lot data
	 * @var array
	 */
	public $lot = array();

	/**
	 * Stores a bid
	 * @var array|false
	 */
	public $bid = FALSE;

	/**
	 * Whether to show actions only an owner can perform
	 * on this lot.
	 *
	 * @var boolean
	 */
	public $owner_actions = FALSE;

	/**
	 * The image URL to the defined currency image
	 * @var string
	 */
	public $currency_image = FALSE;

	/**
	 * Stores the navigation
	 * @var array
	 */
	public $trade_nav = array();

	/**
	 * Simplify lot data and add linked item
	 * @return array
	 */
	public function lot()
	{
		$inventory = array();

		foreach ($this->lot->items() as $item)
		{
			$inventory[] = array(
				'name' => $item->name(),
				'img'  => $item->img()
			);
		}

		$lot = array(
			'id'           => $this->lot->id,
			'is_owner'     => $this->owner_actions,
			'can_bid'      => ($this->bid == FALSE && $this->owner_actions != FALSE) ? FALSE : array('link' => Route::url('trades.bid', array('id' => $this->lot->id))),
			'description'  => $this->lot->description,
			'inventory'    => $inventory,
			'username'     => $this->lot->user->username,
			'user_profile' => Route::url('user.profile', array('name' => $this->lot->user->username)),
			'delete_trade' => ($this->owner_actions) ? Route::url('trades.delete', array('id' => $this->lot->id)) : FALSE
		);

		return $lot;
	}

	/**
	 * Return a simplified bid data definition.
	 *
	 * @param User_Trade_Bid $bid
	 *
	 * @return array
	 */
	public function bid($bid = NULL)
	{
		if ($bid == NULL && $this->bid != FALSE)
		{
			$bid = $this->bid;
		}

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
				'username'  => $bid->user->username,
				'profile'   => Route::url('user.profile', array('name' => $bid->user->username)),
				'inventory' => $items,
				'accept'    => Route::url('trades.accept', array('id' => $bid->id)),
				'reject'    => Route::url('trades.reject', array('id' => $bid->id)),
				'retract'   => Route::url('trades.retract', array('id' => $bid->id))
			);
		}

		return FALSE;
	}

	/**
	 * If the owner is viewing the page
	 * return bids people have made.
	 *
	 * @return array
	 */
	public function bids()
	{
		$list = array();

		if ($this->owner_actions == TRUE)
		{
			$bids = $this->lot->bids->find_all();

			if (count($bids) > 0)
			{
				foreach ($bids as $bid)
					$list[] = $this->bid($bid);
			}
		}

		return $list;
	}
}
