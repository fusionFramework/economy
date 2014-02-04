<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item result class.
 *
 * This is returned by commands,
 * simply sets status and a message.
 *
 * @package    fusionFramework/economy
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Item_Result {
	static public function factory($msg, $status = 'success')
	{
		$result = new Item_Result;

		$result->status = $status;
		$result->text = $msg;

		return $result;
	}

	public $status = null;

	public $text = null;
}