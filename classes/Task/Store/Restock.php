<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item Restock Task (run this as a cron job)
 *
 * @package    fusionFramework/economy
 * @category   task
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Task_Store_Restock extends Minion_Task {

	protected $_options = array();

	/**
	 * Restock stores
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		$stores = array();

		// find items that need to be restocked
		$restocks = ORM::factory('Store_Restock')
			->where('next_restock', '<=', time())
			->order_by(DB::expr('RAND()'))
			->with('item')
			->find_all();

		foreach ($restocks as $restock) {
			// If the item isn't in circulation skip
			if($restock->item->status != 'released')
				continue;

			// cache the store's cap limit and stock total
			if ( ! array_key_exists($restock->store_id, $stores))
			{
				$stores[$restock->store_id] = array(
					'cap' => $restock->store->stock_cap,
					'stock' => ORM::factory('Store_Inventory')
							->where('store_id', '=', $restock->store_id)
							->count_all()
				);
			}

			// only restock if we haven't reached the store's item stack cap
			if ($stores[$restock->store_id]['cap'] != $stores[$restock->store_id]['stock'])
			{
				// get randomised values for price and amount
				$price = mt_rand($restock->min_price, $restock->max_price);
				$amount = mt_rand($restock->min_amount, $restock->max_amount);

				$inventory = ORM::factory('Store_Inventory')
					->where('store_id', '=', $restock->store_id)
					->where('item_id', '=', $restock->item_id)
					->find();

				// the item was still in stock, just update it
				if ($inventory->loaded())
				{
					$new = false;
					$amount = ($amount + $inventory->stock > $restock->cap_amount) ? $restock->cap_amount : $amount + $inventory->stock;
				}
				else
				{
					// add 1 to the store's stock
					$stores[$restock->store_id]['stock']++;

					// prepare the new item
					$inventory = ORM::factory('Store_Inventory')
						->values(array('store_id' => $restock->store_id, 'item_id' => $restock->item_id));

					$new = true;
				}

				// update stock & price
				$inventory->price = $price;
				$inventory->stock = $amount;
				$inventory->save();

				// fire the restock event
				$param = ['store_id' => $inventory->store_id, 'item_id' => $inventory->item_id, 'amount' => $amount, 'price' => $price, 'new' => $new];
				Plug::fire('store.restock', $param);

				// set the next restock
				$restock->next_restock = time() + $restock->frequency;
				$restock->save();
			}
		}
	}
}