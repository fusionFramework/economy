<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Increase a pet's hunger level
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Item_Command_Pet_Feed extends Item_Command_Pet {

	protected function _build($name)
	{
		return array(
			'title' => 'Pet hunger',
			'fields' => array(
				array(
						'name' => $name, 'class' => 'input-mini'
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
		if ($pet->hunger == 100)
		{
			return Item_Result::factory($pet->name . ' is already full', 'error');
		}
		else
		{
			$level = $pet->hunger + $param;

			if ($level > 100)
			{
				$pet->hunger = 100;
			}
			else
			{
				$pet->hunger = $level;
			}

			$pet->save();

			return Item_Result::factory($pet->name . ' has been fed ' . $item->name);
		}
	}
}
