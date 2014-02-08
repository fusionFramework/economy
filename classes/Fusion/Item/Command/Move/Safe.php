<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Move an item to the player's safe
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Item_Command_Move_Safe extends Item_Command_Move {

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

		if (!$item->move('safe', $amount))
		{
			return Item_Result::factory('You can\'t move ' . $name . ' to your safe.', 'error');
		}
		else
		{
			return Item_Result::factory('You have successfully moved ' . $name . ' to your safe.');
		}
	}

}
