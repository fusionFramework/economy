<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Auction index view data
 *
 * View active lots
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_Index extends Views
{

	public $title = 'Auction list';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the action lots
	 * @var unknown_type
	 */
	public $lots = array();

	/**
	 * Stores the navigation
	 * @var string
	 */
	public $menu = false;

	/**
	 * Simplify lot data and add linked item
	 * @return array
	 */
	public function lots()
	{
		$list = array();

		if (count($this->lots) > 0)
		{
			foreach ($this->lots as $lot)
			{
				$item = ORM::factory('User_Item')
					->where('location', '=', 'auction')
					->where('parameter_id', '=', $lot->id)
					->find();

                $span = Date::span(time(), $lot->until, 'days,hours,minutes,seconds');
                $until = '';

                foreach($span as $type => $val)
                {

                    if($val > 0)
                    {
                        $type = ($val > 1) ? $type : Inflector::singular($type);
                        $until .= $val . ' ' . $type . ' ';
                    }
                }

				$list[] = array(
					'id'           => $lot->id,
					'lot_link'     => Route::url('auctions.view', array('id' => $lot->id)),
					'item'         => [
						'name' => $item->item->name,
						'img'  => $item->img()
					],
                    'until'         => $until,
					'username'     => $lot->user->username,
					'user_profile' => Route::url('user.profile', array('name' => $lot->user->username)),
					'auto_buy'     => ($lot->auto_buy > 0)
				);
			}
		}

		return $list;
	}
}
