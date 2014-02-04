<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Increase a pet's happiness level
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_Pet_Play extends Item_Command_Pet {

	protected function _build($name)
	{
		return array(
			'title' => 'Pet mood',
			'fields' => array(
				array(
						'name' => $name, 'class' => 'input-xs'

				)
			)
		);
	}

	public function validate($param)
	{
		return (Valid::digit($param) AND $param > 0);
	}

	public function perform($item, $param, $pet = null)
	{
		if ($pet->happiness == 100)
		{
			return Item_Result::factory($pet->name . ' is already too happy', 'error');
		}
		else
		{
			$level = $pet->happiness + $param;

			if ($level > 100)
			{
				$pet->happiness = 100;
			}
			else
			{
				$pet->happiness = $level;
			}

			$pet->save();

			return Item_Result::factory($pet->name . ' played with ' . $item->name);
		}
	}
}
