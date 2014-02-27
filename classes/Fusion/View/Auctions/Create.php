<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Trade create view data
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_Create extends Views
{

	public $title = 'Create auction';

	/**
	 * transferable items that are located in the player's inventory
	 * @var array
	 */
	public $items = array();


	/**
	 * Simplify item data
	 * @return array
	 */
	public function items()
	{
		$list = array();

		if (count($this->items) > 0)
		{
			foreach ($this->items as $item)
			{
				$list[] = array(
					'id'   => $item->id,
					'name' => $item->name(),
					'img'  => $item->img(),
				);
			}
		}

		return $list;
	}

    public function until()
    {
        $out = [];

        foreach(Kohana::$config->load('auctions.creation.lengths') as $id => $val)
        {
            $out[] = [
                'key' => $id,
                'value' => $val
            ];
        }

        return $out;
    }
}
