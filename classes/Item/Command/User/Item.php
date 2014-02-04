<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Give the user an item
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_User_Item extends Item_Command {

	protected function _build($name)
	{
		return array(
			'title' => 'Item',
			'search' => 'item',
			'multiple' => 1,
			'fields' => array(
				array(
					'name' => $name, 'class' => 'input-sm search'
				)
			)
		);
	}

	public function validate($param)
	{
		$item = ORM::factory('Item')
			->where('item.name', '=', $param)
			->find();

		return $item->loaded();
	}

	public function perform($item, $param, $data = null)
	{
		$item = ORM::factory('Item')
			->where('item.name', '=', $param)
			->find();

		Item::factory($item)->to_user(Fusion::$user->id);

		return Item_Result::factory('You\'ve received a' . $item->name);
	}
}
