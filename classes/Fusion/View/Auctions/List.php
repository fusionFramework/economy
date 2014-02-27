<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * List the auctions that the user has created.
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_List extends Views
{

	public $title = 'Auctions created';

	/**
	 * Store the pagination HTML.
	 * @var string
	 */
	public $pagination = FALSE;

	/**
	 * Stores the action lots
	 * @var Model_Log
	 */
	public $auctions = array();

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

		if (count($this->auctions) > 0)
		{
			foreach ($this->auctions as $auction)
			{
                if($auction->params[':meta']['until'] > time())
                {
                    $span = Date::span(time(), $auction->params[':meta']['until'], 'days,hours,minutes,seconds');
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
					'auto_buy'     => ($auction->params[':meta']['auto_buy'] > 0)
				);
			}
		}

		return $list;
	}
}
