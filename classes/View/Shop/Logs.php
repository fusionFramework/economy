<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * User shop logs view data
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class View_Shop_Logs extends Views
{

	public $title = 'Shop';

	/**
	 * @var array Containing logs
	 */
	public $logs = array();

	/**
	 * @var int How much earnings the shop has made
	 */
	public $earnings = 0;

	public $menu = false;

	/**
	 * Parse log data
	 *
	 * @return array
	 */
	public function logs()
	{
		$return = array();

		if (count($this->logs) > 0)
		{
			foreach ($this->logs as $log)
			{
				$return[] = array(
					'time' => Date::fuzzy_span($log->time),
					'username' => $log->param[':username'],
					'profile' => Route::url('user.profile', ['name' => $log->param[':username']], true),
					'name' => $log->param[':item_name'],
					'price' => $log->param[':price']
				);
			}
		}

		return $return;
	}
}
