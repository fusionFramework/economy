<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Item helper
 *
 * A collection of useful functions that relate to items.
 *
 * @package    fusionFramework/economy
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Item {

	/**
	 * Contains a Model_Item instance
	 * @var Model_Item
	 */
	protected $_item = NULL;

	/**
	 * Load up the class by assigning a Model_Item
	 *
	 * @param integer|Model_Item $item Either an id or a model instance
	 *
	 * @throws Item_Exception
	 */
	public function __construct($item)
	{
		if (Valid::digit($item))
		{
			$id = $item;
			$item = ORM::factory('Item', $id);

			if ($item->loaded())
			{
				$this->_item = $item;
			}
			else
			{
				throw new Item_Exception_Load('Item ":id" could not be loaded', array(':id' => $id));
			}
		}
		elseif ($item->loaded())
		{
			if (is_a($item, 'Model_Item'))
			{
				$this->_item = $item;
			}
			else
			{
				throw new Item_Exception_Load('The supplied item\'s resource does not come from a model.');
			}
		}
		else
		{
			throw new Item_Exception_Load('Item ":name" could not be loaded', array(':name' => $item->name));
		}
	}

	/**
	 * @var array Track user's inventory count
	 */
	protected $_inventory_count = [];

	/**
	 * Return the total amount of item stacks the user has in its inventory
	 *
	 * @param Model_User $user
	 * @return integer
	 */
	protected function _inventory_count(Model_User $user)
	{
		if(!isset($this->_inventory_count[$user->id]))
		{
			$q = DB::select(array(DB::expr('COUNT(id)'), 'total'))
				->from('user_items')
				->where('user_id', '=', $user->id)
				->where('location', '=', 'inventory')
				->execute();

			$this->_inventory_count[$user->id] = $q->get('total');
		}

		return $this->_inventory_count[$user->id];
	}

	/**
	 * Send x copies of the registered item to a user.
	 *
	 * @param integer|Model_User $user          User to send the item to (defaults to logged in user)
	 * @param integer            $amount        Amount of items to send (defaults to 1)
	 * @param string             $location      Where to send the items to (defaults to inventory)
	 * @param boolean            $ignore_limit  Ignore inventory limit? (useful for game rewards)
	 * @throws Item_Exception
	 */
	public function to_user($user=null, $origin = "app", $amount = 1, $extra_param=[], $location = 'inventory', $ignore_limit=false)
	{
		if (!Valid::digit($amount))
		{
			throw new Item_Exception_Amount('The supplied amount should be a number.');
		}

		if (Valid::digit($user))
		{
			$user = ORM::factory('User', $user);
		}
		else if (!is_a($user, 'Model_User'))
		{
			throw new Item_Exception_User('The supplied user does not come from a model.');
		}
		else if($user == null && Fusion::$user->loaded())
		{
			$user = Fusion::$user;
		}


		if (!$user->loaded())
		{
			throw new Item_Exception_User('The supplied user does not exist.');
		}
		else
		{
			if($location == 'inventory' && $ignore_limit == false)
			{
				$stored = $this->_inventory_count($user);

				if($stored >= Kohana::$config->load('items.inventory.limit'))
				{
					Throw new Item_Exception_Space(':location is full', [':location' => $location]);
				}
			}

			$user_item = false;

			if($this->_item->unique == 0)
			{
				$user_item = ORM::factory('User_Item')
					->where('user_id', '=', $user->id)
					->where('item_id', '=', $this->_item->id)
					->where('location', '=', $location)
					->find();
			}

			$action = ($amount > 0) ? '+' : '-';

			if ($user_item != false && $user_item->loaded() && $action == '+')
			{
				//update item amount
				$user_item->amount('+', $amount);
			}
			else if ($action == '+')
			{
				$id = $this->_item->id;

				if($this->_item->unique == 0)
				{
					//create new copy
					$user_item = ORM::factory('User_Item')
						->values(array('user_id' => $user->id, 'item_id' => $id, 'location' => $location, 'amount' => $amount))
						->save();

					$this->_inventory_count[$user->id]++;
				}
				else
				{
					for($i=0;$i<$amount;$i++)
					{
						ORM::factory('User_Item')
							->values(array('user_id' => $user->id, 'item_id' => $id, 'location' => $location, 'amount' => 1))
							->save();

						$this->_inventory_count[$user->id]++;
					}
				}
			}
			else
			{
				Throw new Item_Exception_Amount('You can\'t take away items with Item::to_user()');
			}

            $param = array(
                ':amount' => $amount,
                ':item_name' => $user_item->item->name,
                ':origin' => str_replace('.', ' ', $origin)
            ) + $extra_param;

			return Fusion::$log->create('item.in.' . $origin, 'item', 'Player received :amount :item_name @ :origin', $param);

		}
	}

	/**
	 * Check if the user has this item in location x.
	 *
	 * Optionally check if the user has at least $amount.
	 *
	 * @param string  $location [optional] Where the item should be located (defaults to inventory)
	 * @param integer $amount   [optional] The amount that should be present in that location
	 * @param Model_User $user  [optional] Which user should have this item
	 *
	 * @return Model_User_Item|false
	 */
	public function user_has($location = 'inventory', $amount = FALSE, $user = NULL)
	{
		if($amount != FALSE && !Valid::digit($amount))
		{
			Throw new Item_Exception_Amount('The supplied amount should be a number.');
		}
		else if($amount != FALSE && $amount <= 0)
		{
			Throw new Item_Exception_Amount('The supplied amount should be atleast 1.');
		}

		if ($user == NULL)
		{
			$user = Fusion::$user;
		}

		if(!is_a($user, 'Model_User'))
		{
			Throw new Item_Exception_User('The supplied user is invalid.');
		}

		$user_item = ORM::factory('User_Item')
			->where('item_id', '=', $this->_item->id)
			->where('location', '=', $location)
			->where('user_id', '=', $user->id)
			->find();

		if ($user_item->loaded() && $amount == FALSE)
		{
			return $user_item;
		}
		else if ($user_item->loaded() AND $user_item->amount >= $amount)
		{
			return $user_item;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Return the item's instance
	 * @return Model_Item
	 */
	public function item()
	{
		return $this->_item;
	}

	/**
	 * Retrieve a user's items.
	 *
	 * By default the logged in player is used to retrieve items from.
	 * Optionally limit to only transferable items
	 * Optionally look for a relation through Item->parameter_id
	 *
	 * @param string  $location           The location to look for items
	 * @param boolean $transferable_check Check if we need to load only transferable items
	 * @param integer $parameter_id       Look for a specific linked id
	 * @param User    $user               Provide a user whose item's we'll be looking up
	 *
	 * @return Model_User_Item
	 */
	static public function location($location = 'inventory', $transferable_check = FALSE, $parameter_id = NULL, $user = NULL)
	{
		if ($user == NULL)
		{
			$user = Fusion::$user;
		}

		if(!is_a($user, 'Model_User'))
		{
			Throw new Item_Exception_User('The supplied user is invalid.');
		}

		$items = ORM::factory('User_Item')
			->where('user_id', '=', $user->id)
			->where('location', '=', $location);

		if ($transferable_check == TRUE)
		{
			$items = $items->where('transferable', '=', 1);
		}
		if ($parameter_id != NULL)
		{
			$items = $items->where('parameter_id', '=', $parameter_id);
		}

		return $items;
	}

	/**
	 * Retrieve a specific set of items if the user has them.
	 * There's no promise that the user has them all.
	 *
	 * An array gets returned where the keys represent the item's item_id.
	 *
	 * @param array $item_ids   Item ids to look for
	 * @param null  $location   Location to look through (defaults to inventory)
	 * @param null  $user       User that owns the items (defaults to logged in user)
	 *
	 * @return Database_Result
	 */
	static public function retrieve(Array $item_ids, $location=null, $user=null)
	{
		if($location == null)
		{
			$location = 'inventory';
		}

		if($user == null)
		{
			$user = Fusion::$user;
		}

		if(!is_a($user, 'Model_User'))
		{
			Throw new Item_Exception_User;
		}

		$items = ORM::factory('User_Item')
			->where('user_id', '=', $user->id)
			->where('location', '=', $location)
			->where('item_id', 'IN', $item_ids)
			->find_all();

		$return = [];

		if($items->count() > 0)
		{
			foreach($items as $item)
			{
				$return[$item->item_id] = $item;
			}
		}

		return $return;
	}


	/**
	 * Build an Item instance
	 *
	 * @param Model_Item|integer $item Could be an item id or an item model instance
	 *
	 * @return Item
	 */
	static public function factory($item)
	{
		return new Item($item);
	}

	/**
	 * Parse item commands before saving
	 *
	 * @param array $input User command definition
	 *
	 * @return array Formatted commands array
	 */
	static public function parse_commands($input)
	{
		$commands = array();

		foreach ($input as $k => $c)
		{
			//if we're dealing string as parameter or an assoc array
			if (!is_array($c) OR count(array_filter(array_keys($c), 'is_string')) > 0)
			{
				$commands[] = array('name' => $k, 'param' => $c);
			}
			else
			{
				//if multiple command instances were defined (non-assoc array)
				foreach ($c as $p)
				{
					$commands[] = array('name' => $k, 'param' => $p);
				}
			}
		}

		return $commands;
	}

	/**
	 * Load all item command classes
	 * @return array
	 */
	static public function list_commands()
	{
		static $commands = NULL;

		if ($commands == NULL)
		{
			// Include paths must be searched in reverse
			$paths = array_reverse(Kohana::list_files('classes/Item/Command/'));

			// Array of class names that have been found
			$found = array();

			foreach ($paths as $files)
			{
				$replacements = array_merge(Kohana::include_paths(), array('classes' . DIRECTORY_SEPARATOR . 'Item' . DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR, '.php'));

				if (is_array($files))
				{
					foreach ($files as $file)
					{
						foreach ($replacements as $replace)
						{
							$file = str_replace($replace, '', $file);
						}

						$found[] = $file;
					}
				}
			}
			$commands = $found;
		}

		return $commands;
	}

	/**
	 * Make sure directories have a trailing slash
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	static public function filter_type_dir($value)
	{
		return (substr($value, -1) != '/') ? $value . '/' : $value;
	}

	/**
	 * Validate item command input when creating an item
	 *
	 * @param Validation $validation Validation object
	 * @param JSON       $value      Command to validate
	 */
	static public function validate_commands($validation, $value)
	{
		$values = json_decode($value, TRUE);

		foreach ($values as $command => $val)
		{
			$cmd = Item_Command::factory($command);

			if (!$cmd->validate($val))
			{
				$validation->error('commands', $command);
			}
		}
	}
}
