<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * List the auctions that the user has created.
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_Bids extends Views
{

	public $title = 'Bids';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the action lots that were bid on
	 * @var Model_Log
	 */
	public $bids = array();

	/**
	 * Stores the navigation
	 * @var string
	 */
	public $menu = false;

	/**
	 * Simplify lot data and add linked item
	 * @return array
	 */
	public function bids()
	{
		$list = array();

		if (count($this->bids) > 0)
		{
			foreach ($this->bids as $auction)
			{
                if($auction->params[':until'] > time())
                {
                    $span = Date::span(time(), $auction->params[':until'], 'days,hours,minutes,seconds');
                    $until = '';

                    foreach($span as $type => $val)
                    {

                        if($val > 0)
                        {
                            $type = ($val > 1) ? $type : Inflector::singular($type);
                            $until .= $val . ' ' . $type . ' ';
                        }
                    }
                }
                else
                {
                    $until = 'ended';
                }


				$list[] = array(
					'id'           => $auction->alias_id,
					'lot_link'     => Route::url('auctions.view', array('id' => $auction->alias_id)),
					'item'         => [
						'name' => $auction->params[':item_name'],
						'img'  => $auction->params[':item_img']
					],
                    'until'         => $until,
                    'username'      => $auction->params[':owner'],
                    'user_profile'  => Route::url('user.profile', ['username' => $auction->params[':owner']], true),
                    'points'        => $auction->params[':points'],
					'auto_buy'     => $auction->params[':auto_buy']
				);
			}
		}

		return $list;
	}
}
