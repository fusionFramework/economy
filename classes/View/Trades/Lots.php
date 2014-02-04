<?php defined('SYSPATH') OR die('No direct script access.');

class View_Trades_Lots extends Views {

	public $title = 'Trade lots';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the trade lots
	 * @var array
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
					'id'          => $lot->id,
					'lot_link'    => Route::url('trades.lot', array('id' => $lot->id)),
					'description' => $lot->description,
					'inventory'   => $inventory,
				);
			}
		}

		return $list;
	}
}
