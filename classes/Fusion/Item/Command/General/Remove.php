<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Delete an item from the inventory
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Item_Command_General_Remove extends Item_Command {

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
		return NULL;
	}
}
