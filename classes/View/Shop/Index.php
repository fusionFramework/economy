<?php defined('SYSPATH') OR die('No direct script access.');

class View_Shop_Index extends Views {

	public $title = 'Shop';

	/**
	 * Contains user shop model
	 * @var Model_User_Shop
	 */
	public $shop = FALSE;

	/**
	 * Contains a unit's size
	 * @var integer|false
	 */
	public $units = FALSE;

	public $menu = false;

	/**
	 * formats user shop data
	 */
	public function shop()
	{
		return array_merge($this->shop, array('link' => Route::url('shop.update')));
	}

	/**
	 * Calculates user shop unit size, inventory size
	 */
	public function units()
	{
		$extra = array(
			'size'    => $this->shop['size'],
			'content' => $this->shop['size'] * $this->units['unit_size'],
			'link'    => Route::url('shop.upgrade')
		);

		return array_merge($this->units, $extra);
	}
}
