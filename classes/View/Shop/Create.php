<?php defined('SYSPATH') OR die('No direct script access.');

class View_Shop_Create extends Views {

	public $title = 'Shop';

	/**
	 * Contains 2 keys:
	 * - cost (integer)
	 * - affordable (bool) whether the user can afford it to create a shop
	 * @var array
	 */
	public $creation = FALSE;
}
