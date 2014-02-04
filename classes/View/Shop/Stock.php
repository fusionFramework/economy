<?php defined('SYSPATH') OR die('No direct script access.');

class View_Shop_Stock extends Views {

	public $title = 'Shop';

	/**
	 * Contains a list of User_item
	 * @var array
	 */
	public $items = array();

	/**
	 * Pagination HTML
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * @var int The current page the user s viewing
	 */
	public $page = 1;

	public $menu = false;

	/**
	 * Simplify User_item data for the template.
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
					'id'     => $item->id,
					'price'  => $item->parameter,
					'img'    => $item->img(),
					'name'   => $item->item->name,
					'amount' => $item->amount
				);
			}
		}

		return $list;
	}
}
