<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Change a pet's colour (if possible)
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_Pet_Paint extends Item_Command_Pet {

	protected function _build($name)
	{
		return array(
			'title' => 'Pet color',
			'search' => 'pet-color',
			'fields' => array(
				array(
						'name' => $name, 'class' => 'input-sm search'

				)
			)
		);
	}

	public function validate($param)
	{
		$color = ORM::factory('Pet_Colour')
			->where('pet_colour.name', '=', $param)
			->find();
		return $color->loaded();
	}

	public function perform($item, $param, $pet = null)
	{
		$colour = ORM::factory('Pet_Colour')
			->where('pet_colour.name', '=', $param)
			->find();

		if ($pet->specie->has('colours', $colour))
		{
			$pet->colour_id = $colour->id;
			$pet->save();
			return Item_Result::factory($pet->name . ' changed into ' . $colour->name);
		}
		else if ($pet->colour_id == $colour->id)
		{
			return Item_Result::factory($pet->name . ' is already ' . $colour->name, 'warning');
		}
		else
		{
			return Item_Result::factory($pet->name . ' can\'t change into that color', 'error');
		}
	}
}
