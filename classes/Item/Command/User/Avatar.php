<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item command class
 *
 * Give the player an avatar
 *
 * @package    fusionFramework/economy
 * @category   Commands
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Command_User_Avatar extends Item_Command {

	protected function _build($name)
	{
		return array(
			'title' => 'Avatar',
			'search' => 'avatar',
			'fields' => array(
				array(
						'name' => $name, 'class' => 'input-sm search'

				)
			)
		);
	}

	public function validate($param)
	{
		$avatar = ORM::factory('Avatar')
			->where('name', '=', $param)
			->find();

		return $avatar->loaded();
	}

	public function perform($item, $param, $data = null)
	{
		$avatar = ORM::factory('Avatar')
			->where('name', '=', $param)
			->find();

		$user = Fusion::$user;

		if ($user->has('avatars', $avatar))
		{
			return Item_Result::factory(null, 'error');
		}
		else
		{
			$user->add('avatars', $avatar);
			$user->save();
			return Item_Result::factory('You have received the "' . $avatar->title . '" avatar! <img src="' . $avatar->img() . '" width="32" height="32" class="pull-left" />');
		}
	}
}
