<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Remove auctions and award winners
 *
 * Run this every 3 minutes or so
 *
 * @package    fusionFramework/economy
 * @category   task
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Task_Store_Auctions_Process extends Minion_Task {

	protected $_options = array(
		'runs' => 15,
		'auctions_per_run' => 100
	);

	/**
	 * Restock stores
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		$i = 0;

		while($i < $params['runs'])
		{
			$auctions = ORM::factory('User_Auction')
				->with('bid:user')
				->where('until', '<=', time())
				->or_where('auto_buy', '=', -1)
				->limit($params['auctions_per_run'])
				->find_all();

			foreach($auctions as $auction)
			{
				// Load the item that's up for auction
				$item = Item::location('auction', false, $auction->id)
					->find();

				if($auction->bid->loaded())
				{
					//create the log
					$log = Fusion::$log->create('auction.'.$auction->id.'.complete', 'CLI', ':username won the auction for :points',
						[':points' => $auction->bid->points, ':auto_buy' => false], $auction->bid->user);

					//notify bidder
					$log->notify($auction->bid->user, 'auctions.won', [':item' => $item->item->name]);

				}
				$auctions->delete();
			}
			$i++;
		}

	}
}