<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Store restock model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_Store_Restock extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'store_id' => NULL,
		'item_id' => NULL,
		'frequency' => NULL,
		'next_restock' => NULL,
		'min_price' => NULL,
		'max_price' => NULL,
		'min_amount' => NULL,
		'max_amount' => NULL,
		'cap_amount' => NULL
	);
	protected $_belongs_to = array(
		'item' => array(
			'model' => 'Item',
			'foreign_key' => 'item_id'
		),
		'store' => array(
			'model' => 'Store',
			'foreign_key' => 'store_id'
		)
	);

	protected $_load_with = array('item');

	public function rules()
	{
		return array(
			'min_price' => array(
				array('not_empty'),
				array('digit')
			),
			'max_price' => array(
				array('not_empty'),
				array('digit')
			),
			'min_amount' => array(
				array('not_empty'),
				array('max_length', array(':value', 3)),
				array('digit')
			),
			'max_amount' => array(
				array('not_empty'),
				array('max_length', array(':value', 3)),
				array('digit')
			),
			'cap_amount' => array(
				array('not_empty'),
				array('max_length', array(':value', 3)),
				array('digit')
			)
		);
	}

} // End Store Restock Model
