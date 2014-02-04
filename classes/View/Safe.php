<?php defined('SYSPATH') OR die('No direct script access.');

class View_Safe extends Views {

	public $title = 'Safe';
	/**
	 * Pagination HTML
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * form submit url
	 * @var string
	 */
	public $process_url = FALSE;

	/**
	 * Whether or not the user has a shop
	 * @var boolean
	 */
	public $shop = FALSE;

	/**
	 * Contains Model_User_Items
	 * @var array
	 */
	public $items = NULL;

	/**
	 * Format item data
	 * @return array
	 */
	public function items()
	{
		$list = array();

		$options = array();
		$options[] = array('name' => 'Inventory', 'value' => 'inventory');

		if ($this->shop == 1)
		{
			$options[] = array('name' => 'Shop', 'value' => 'shop');
		}

		if (count($this->items) > 0)
		{
			foreach ($this->items as $item)
			{
				$list[] = array(
					'img'     => $item->img(),
					'name'    => $item->item->name,
					'amount'  => $item->amount,
					'id'      => $item->id,
					'options' => $options
				);
			}
		}

		return $list;
	}
}
