<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * NPC helper
 *
 * A collection of useful functions that relate to items.
 *
 * @package    fusionFramework/economy
 * @category   NPC
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class NPC_Store extends NPC {

	public $messages = [
			'introduction' => [
				'params' => [
					[
						'name' => ':store_name',
						'value' => 'display the store\'s name'
					],
					[
						'name' => ':name',
						'value' => 'display the NPC\'s name'
					]
				],
				'help' => 'Shown in the store\'s index'
			],
			'sale_success' => [
				'params' => [
					[
						'name' => ':item',
						'value' => 'display the item\'s name'
					],
					[
						'name' => ':price',
						'value' => 'display the price that the user offered'
					]
				],
				'help' => 'Shown when an item was bought successfully'
			],
			'sold_out' => [
				'params' => [
					[
						'name' => ':item',
						'value' => 'display the item\'s name'
					]
				],
				'help' => 'shown when the user attempts to buy an item that\'s been sold out'
			],
			'price_low' => [
				'params' => [
					[
						'name' => ':item',
						'value' => 'display the item\'s name'
					],
					[
						'name' => ':price',
						'value' => 'display the price that the user offered'
					]
				],
				'help' => 'shown when a user haggles for a price that\'s too low'
			]
	];

	/**
	 * Define multiple messages per type
	 */
	public $multi_msg = true;

	/**
	 * Save the NPC image in the stores folder
	 */
	public $image = 'stores';

	/**
	 * Should the NPC of this type have a name?
	 * @var bool
	 */
	public $name_required = true;
}
