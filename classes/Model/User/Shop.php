<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User shop model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Model_User_Shop extends ORM {

	protected $_table_columns = array(
		'id' => NULL,
		'user_id' => NULL,
		'title' => NULL,
		'description' => NULL,
		'size' => NULL,
		'till' => NULL
	);

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id'
		),
	);

	protected $_load_with = array('user');

	public function rules()
	{
		return array(
			'user_id' => array(
				array('not_empty'),
				array('digit'),
			),
			'title' => array(
				array('not_empty'),
				array('min_length', array(':value', 5)),
				array('max_length', array(':value', 70)),
			),
			'description' => array(
				array('max_length', array(':value', Kohana::$config->load('items.shop.description_char_limit')))
			)
		);
	}

	public function filters()
	{
		return array(
			'description' => array(
				array('Security::xss_clean'),
			),
		);
	}

	/**
	 * Count how many item stacks there are in the inventory
	 * @return integer
	 */
	public function inventory_count()
	{
		return DB::select(array(DB::expr('COUNT(*)'), 'total'))
			->from('user_items')
			->where('user_id', '=', $this->user_id)
			->where('location', '=', 'shop')
			->execute()
			->get('total');
	}

	/**
	 * Check if there's space left in the inventory
	 * @return boolean
	 */
	public function space_left()
	{
		if (Kohana::$config->load('items.user_shop.size.active'))
		{
			return (($this->size * Kohana::$config->load('items.user_shop.size.unit_size')) < $this->inventory_count());
		}
		else
		{
			return TRUE;
		}
	}

} // End User_Shop Model
