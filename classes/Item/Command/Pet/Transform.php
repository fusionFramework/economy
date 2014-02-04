<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Change a pet's specie
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_Pet_Transform extends Item_Command_Pet {

	protected function _build($name)
	{
		return array(
			'title' => 'Pet specie',
			'search' => 'pet-specie',
			'fields' => array(
				array(
						'name' => $name, 'class' => 'input-sm search'

				)
			)
		);
	}

	public function validate($param)
	{
		$specie = ORM::factory('Pet_Specie')
			->where('pet_specie.name', '=', $param)
			->find();

		return $specie->loaded();
	}

	public function perform($item, $param, $pet = null)
	{
		$specie = ORM::factory('Pet_Specie')
			->where('pet_specie.name', '=', $param)
			->find();

		if ($specie->id == $pet->specie_id)
		{
			return Item_Result::factory($pet->name . ' is already a ' . $specie->name, 'warning');
		}

		$pet->specie_id = $specie->id;
		$pet->save();

		return Item_Result::factory('Your ' . $pet->name . ' has changed in to a ' . $specie->name);
	}
}
