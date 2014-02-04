<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item inventory controller
 *
 * List and consume items
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Controller_Inventory extends Controller_Fusion_Site {

	/**
	 * Show all the items the user has in their inventory
	 */
	public function action_index()
	{
		Fusion::$assets->add_set('modals');
		Fusion::$assets->add_js('inventory/index.js');

		$this->_tpl = new View_Inventory_Index;

		$max_items = Kohana::$config->load('items.inventory.items_per_page');

		$items = Item::location('inventory');

		$paginate = Paginate::factory($items, ['total_items' => $max_items])->execute();

		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->items = $paginate->result();
		$this->_tpl->total_items = count($this->_tpl->items);
		$this->_tpl->limit = Kohana::$config->load('items.inventory.limit');
	}

	/**
	 * View what the user can do with an item.
	 */
	public function action_view()
	{
		$id = $this->request->param('id');

		$item = ORM::factory('User_Item', $id);

		if (!$item->loaded())
		{
			RD::error('Item could not be found');
		}
		else if ($item->user_id != Fusion::$user->id)
		{
			RD::error(':item_name does not seem to be yours', [':item_name' => $item->item->name]);
		}
		else if ($item->location != 'inventory')
		{
			RD::error(':item_name is not located in your inventory.', [':item_name' => $item->item->name]);
		}
		else
		{
			//generate action list
			$actions = [];

			$default_command = Item_Command::factory($item->item->type->default_command);

			if ($default_command->pets_required() == TRUE)
			{
				$pets = ORM::factory('User_Pet')
					->where('user_id', '=', Fusion::$user->id)
					->find_all();

				if (count($pets) > 0)
				{
					foreach ($pets as $pet)
					{
						$actions[$pet->id] = [
							'item'  => __($item->item->type->action, [':pet_name' => $pet->name]),
							'extra' => $default_command->inventory()
						];
					}
				}
			}
			else
			{
				$actions['consume'] = [
					'item'  => $item->item->type->action,
					'extra' => $default_command->inventory()
				];
			}

			$actions['move_safe'] = [
				'item'  => 'Move to safe',
				'extra' => Item_Command::factory('Move_Safe')->inventory()
			];

			$user_shop = ORM::factory('User_Shop')
				->where('user_id', '=', Fusion::$user->id)
				->find();

			// Let's see if there's room for an extra item stack in the user shopif the user has one
			if ($user_shop->loaded() && $user_shop->inventory_space() == TRUE)
			{
				$actions['move_shop'] = [
					'item'  => 'Move to your shop',
					'extra' => Item_Command::factory('Move_Shop')->inventory()
				];
			}
			else
			{
				// Let's see if there's already a stack with this item
				$shop_item = ORM::factory('User_Item')
					->where('user_id', '=', Fusion::$user->id)
					->where('location', '=', 'shop')
					->where('item_id', '=', $item->item_id)
					->find();

				if($user_shop->inventory_space() == FALSE && $shop_item->loaded())
				{
					$actions['move_shop'] = [
						'item'  => 'Move to your shop',
						'extra' => Item_Command::factory('Move_Shop')->inventory()
					];
				}
			}

			if ($item->item->transferable == TRUE)
			{
				$actions['gift'] = [
					'item'  => 'Send as gift',
					'extra' => Item_Command::factory('General_Gift')->inventory()
				];
			}

			$actions['remove'] = [
				'item'  => 'Remove item',
				'extra' => Item_Command::factory('General_Remove')->inventory()
			];

			if (!$this->request->is_ajax())
			{
				// Redirect back to the inventory's index if there are any errors
				if (count(RD::get_current(RD::ERROR)) > 0)
				{
					$this->redirect(Route::url('inventory.index', null, true));
				}

				//otherwise render the page
				$this->_tpl = new View_Inventory_View;
				$this->_tpl->item = $item;
				$this->_tpl->action_list = $actions;
				Fusion::$assets->add_js('inventory/view.js');
			}
			else
			{
				RD::success('Item is consumable', null, ['actions' => $actions, 'name' => $item->name()]);
			}
		}
	}

	/**
	 * Consume the item
	 */
	public function action_consume()
	{
		$item = ORM::factory('User_Item', $this->request->param('id'));
		$action = $this->request->post('action');

		if (!$item->loaded())
		{
			RD::error('You can\'t use an item that does not exist');
		}
		else if ($item->user_id != Fusion::$user->id)
		{
			RD::error('You can\'t access another player\'s item');
		}
		else if ($item->location != 'inventory')
		{
			RD::error('The item you want to consume is not located in your inventory');
		}
		else if ($action == NULL)
		{
			RD::error('No action to perform has been specified');
		}
		else
		{
			$def_cmd = Item_Command::factory($item->item->type->default_command);

			if (Valid::digit($action))
			{
				//we'll want to perform an action on a pet
				$pet = ORM::factory('User_Pet', $action);

				if (!$pet->loaded())
				{
					RD::error('No existing pet has been specified');
				}
				else if ($pet->user_id != Fusion::$user->id)
				{
					RD::error('You can\'t let a pet consume this item if it\'s not yours');
				}
				else if ($def_cmd->pets_required() == FALSE)
				{
					RD::error('can\'t perform this item action on a pet');
				}
				else
				{
					$commands = $item->item->commands;
					$results = [];

					$db = Database::instance();
					$db->begin();
					$error = FALSE;
					foreach ($commands as $command)
					{
						$cmd = Item_Command::factory($command['name']);
						$res = $cmd->perform($item, $command['param'], $pet);

						if ($res->status == FALSE)
						{
							//the command couldn't be performed, spit out error, rollback changes and break the loop
							RD::error($res->text, [':item_name' => $item->item->name, ':pet_name' => $pet->name]);
							$error = TRUE;
							$db->rollback();
							break;
						}
						else
						{
							$results[] = $res->text;
						}
					}

					if ($error == FALSE)
					{
						Fusion::$log->create('consume.'.$item->item_id, 'economy', ':item_name consumed', [
							':item_name' => $item->item->name
						]);

						if ($def_cmd->delete_after_consume == TRUE)
						{
							$item->amount('-', 1);
						}

						$db->commit();
					}
				}
			}
			else
			{
				$results = [];

				switch ($action)
				{
					case 'consume' :
						$commands = $item->item->commands;

						$db = Database::instance();
						$db->begin();
						$error = FALSE;
						foreach ($commands as $command)
						{
							$cmd = Item_Command::factory($command['name']);
							$res = $cmd->perform($item, $command['param']);

							if ($res->status == FALSE)
							{
								//the command couldn't be performed, spit out error, rollback changes and break the loop
								RD::error($res->text, [':item_name' => $item->name]);
								$db->rollback();
								$error = TRUE;
								break;
							}
							else
							{
								$results[] = $res->text;
							}
						}

						if ($error == FALSE)
						{
							Fusion::$log->create('consume.'.$item->item_id, 'economy', ':item_name consumed', [
								'alias_id' => $item->item_id,
								':item_name' => $item->item->name
							]);

							if ($def_cmd->delete_after_consume == TRUE)
							{
								$item->amount('-', 1);
							}

							$db->commit();
						}

						break;
					case 'remove' : //takes an amount
						$amount = $this->request->post('amount');

						if ($amount == NULL)
						{
							$amount = 1;
						}

						if (!Valid::digit($amount))
						{
							RD::error('The amount you submitted isn\'t a number.');
						}
						else if ($amount <= 0 OR $amount > $item->amount)
						{
							RD::error('You only have :item_name, not :amount', [':item_name' => $item->name(), 'amount' => $amount]);
						}
						else
						{
							if ($amount > 1)
							{
								$name = Inflector::plural($item->item->name, $amount);
								$verb = 'were';
							}
							else
							{
								$name = $item->item->name(1);
								$verb = 'was';
							}

							$item->amount('-', $amount);
							Fusion::$log->create('remove.'.$item->id, 'economy', ':item_name removed', [
								'alias_id' => $item->item_id,
								':item_name' => $name
							]);

							$results[] = __(':item :verb deleted successfully', [
								':verb' => $verb, ':item' => $name
							]);
						}
						break;
					case 'gift' : //takes a username
						$username = $this->request->post('username');

						if (Fusion::$user->username == $username)
						{
							RD::error('You can\'t send a gift to yourself');
						}
						else
						{
							$user = ORM::factory('User')
								->where('username', '=', $username)
								->find();

							if ($user->loaded())
							{
								$log = $item->transfer($user);

								$log->notify($user, 'item.gift', [':item_name' => $item->item->name(1)]);

								$results[] = __('You\'ve successfully sent :item to :username', [
									':item' => $item->item->name, ':username' => $user->username
								]);
							}
							else
							{
								RD::error('Couldn\'t find a user named ":username"', [':username' => $username]);
							}
						}
						break;
					default :
						if (Text::starts_with($action, 'move_')) //Moving items can take an amount
						{
							$location = substr($action, 5);
							$cmd = Item_Command::factory('Move_' . ucfirst($location));

							$amount = $this->request->post('amount');

							if ($amount == NULL)
							{
								$amount = 1;
							}

							if (!Valid::digit($amount))
							{
								RD::error('The amount you submitted isn\'t a number.');
							}
							else if ($amount <= 0 OR $amount > $item->amount)
							{
								RD::error('You only have ' . $item->name() . ', not ' . $amount);
							}
							else
							{
								Kohana::$config->load('plugins.load_routes');
								$results[] = $cmd->perform($item, $amount)->text;
							}

						}
						else //fallback for any item actions that don't exist
						{
							RD::error('The action you want to perform with this item does not exist');
						}
						break;
				}
			}
		}

		$show = Kohana::$config->load('items.inventory.consume_show_results');
		$output = [];

		if ($show == 'first')
		{
			$output[] = $results[0];
		}
		else
		{
			$output = $results;
		}

		if (count(RD::get_current(RD::ERROR)) == 0 && $this->request->is_ajax())
		{
			// Add the new amount and item_id if there were no errors and it's an ajax request
			RD::success($output[0], null, [
				'new_amount' => ($item->loaded()) ? $item->amount : 0,
				'item_id' => $this->request->param('id')
				]);
			unset($output[0]);
		}

		// If more result messages were defined
		if (count($output) > 0)
		{
			foreach ($output as $result)
			{
				RD::success($result);
			}
		}

		$this->redirect(Route::url('inventory.index', null, true));
	}
}

