<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User auction model
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_User_Auction extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'user_id' => NULL,
		'start_bid' => NULL,
		'min_increment' => NULL,
		'until' => NULL,
		'auto_buy' => NULL
	);

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id'
		),
	);

	protected $_has_one = array(
		'bid' => array(
			'model' => 'User_Auction_Bid',
			'foreign_key' => 'user_auction_id'
		)
	);

	protected $_load_with = array('user');

	public function rules()
	{
		return array(
			'start_bid' => array(
				array('not_empty'),
				array('digit'),
			),
			'min_increment' => array(
				array('not_empty'),
				array('digit'),
			),
			'auto_buy' => array(
				array('digit'),
			),
		);
	}

} // End User_Auction Model
