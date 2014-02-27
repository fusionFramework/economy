<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Auction search view data
 *
 * Search lots for an item
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_Search extends Views
{

	public $title = 'Search auctions';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the found items
	 * @var Model_User_Item
	 */
	public $items = array();

	/**
	 * Holds the search term
	 * @var string
	 */
	public $term = FALSE;

	/**
	 * Stores the navigation
	 * @var string
	 */
	public $menu = false;

	/**
	 * Simplify lot data and add linked items
	 * @return array
	 */
	public function lots()
	{
		$list = array();

		if (count($this->auctions) > 0)
		{
			foreach ($this->auctions as $lot)
			{
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
						'name' => $lot->name,
						'img'  => URL::site('m/items/' . $lot->img_dir . $lot->image, true)
					],
                    'until' => $until,
					'username'     => $lot->username,
					'user_profile' => Route::url('user.profile', array('name' => $lot->username), true),
					'auto_buy'     => ($lot->auto_buy > 0),
				);
			}
		}

		return $list;
	}
}
