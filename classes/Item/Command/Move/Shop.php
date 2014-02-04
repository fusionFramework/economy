<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Move an item to the player's shop
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_Move_Shop extends Item_Command_Move {

	public $default = TRUE;

	protected function _build($name)
	{
		return NULL;
	}

	public function validate($param)
	{
		return NULL;
	}

	public function perform($item, $amount, $data = null)
	{
		$name = $item->item->name($amount);

		if (!$item->move('shop', $amount))
		{
			return Item_Result::factory('You can\'t move ' . $name . ' to your shop.', 'error');
		}
		else
		{
			return Item_Result::factory('You have successfully moved ' . $name . ' to your shop.');
		}
	}

}
