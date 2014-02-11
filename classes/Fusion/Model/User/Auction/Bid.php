<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User auction model
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_User_Auction_Bid extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'user_id' => NULL,
		'user_auction_id' => NULL,
		'created_at' => NULL,
		'points' => NULL
	);

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id'
		),
		'auction' => array(
			'model' => 'User_Auction',
			'foreign_key' => 'user_auction_id'
		)
	);

	protected $_created_column = [
		'column' => 'created_at',
		'format' => true
	];

	protected $_load_with = array('user');

	public function rules()
	{
		return array(
			'points' => array(
				array('not_empty'),
				array('digit'),
			)
		);
	}

} // End User_Auction_Bid Model
