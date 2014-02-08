<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item safe controller
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Controller_Safe extends Controller_Fusion_Site {

	/**
	 * Display a list of items that are stored in the safebox
	 */
	public function action_index()
	{
		$this->_tpl = new View_Safe;

		$config = Kohana::$config->load('items.safe');

		$items = Item::location('safe');

		$paginate = Paginate::factory($items, $config['pagination'], $this->request)->execute();

		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->items = $paginate->result();

		$this->_tpl->process_url = Route::url('safe.move');
		$this->_tpl->shop = DB::select([DB::expr('COUNT(id)'), 'total'])
			->from('user_shops')
			->where('user_id', '=', Fusion::$user->id)
			->execute()
			->get('total');
	}

	/**
	 * Move posted items from the safebox
	 */
	public function action_move()
	{
		$items = $this->request->post('items');
		
		$shop = false;
		if (count($items) > 0)
		{
			foreach ($items as $id => $item)
			{
				if ($item['amount'] > 0)
				{
					$i = ORM::factory('User_Item', $id);

					if ($i->loaded() && $i->location == 'safe' && $i->user_id == Fusion::$user->id)
					{
						if ($item['amount'] > $i->amount)
						{
							RD::error('You can\'t move :name, you only have :amount.',
								[
									':amount' => $i->amount,
									':name' => $i->item->name($item['amount'])
								],
								['item_id' => $i->id]
							);
						}
						else if ($item['location'] == 'shop')
						{
							//load the shop if it hasn't already
							if($shop == false)
							{
								$shop = ORM::factory('User_Shop')
									->where('user_id', '=', Fusion::$user->id)
									->find();
							}

							if (!$shop->loaded())
							{
								RD::error('You don\'t have a shop yet.');
							}
							else if($i->item->transferable == false)
							{
								RD::error(':item_name is not transferable', [':item_name' => $i->item->name]);
							}
							//If there's no space left in the shop and we're adding a new stack
							else if (!$shop->space_left() &&
								DB::select([DB::expr('COUNT(id)'), 'total'])->from('user_items')
									->where('user_id', '=', Fusion::$user->id)->where('location', '=', 'shop')
									->where('item_id', '=', $i->item_id)->execute()->get('total') == 0)
							{
								RD::error('Your shop is already full.');
							}
							// Fine, let's move the item
							else
							{
								$i->move('shop', $item['amount']);

								RD::success('You\'ve moved :items to your shop.', [':items' => $i->item->name($item['amount'])],
									[
										'item_id' => $id,
										'amount' => $item['amount']
									]);
							}
						}
						else if ($item['location'] == 'inventory')
						{
							$i->move('inventory', $item['amount']);
							
							RD::success('You\'ve moved :items to your inventory.', 
								[
									':items' => $i->item->name($item['amount'])
								],
								[
									'item_id' => $id,
									'amount' => $item['amount']
								]);
						}
					}
				}
			}
		}

		//if we're not sending an ajax request
		if(!$this->request->is_ajax())
		{
			$success = RD::get_current(RD::SUCCESS, true);

			// if the user has successfully moved multiple items
			if(count($success) > 1)
			{
				// Just show how many were moved
				RD::success('You have moved :amount items', [':amount' => count($success)]);
			}
			// Otherwise show the original success message
			else if(count($success) == 1)
			{
				RD::success($success[0]['value']);
			}
		}

		$this->redirect(Route::url('safe.index', null, true));
	}
}
