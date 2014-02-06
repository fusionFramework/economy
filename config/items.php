<?php defined('SYSPATH') OR die('No direct script access.');

return array (
	'image' => 
	array (
		'width' => '80',
		'height' => '80',
		'formats' =>
		array(
			0 => 'png',
			1 => 'jpg',
		),
		'tmp_dir' => WEBPATH.'m'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR,
	),
	'inventory' => [
		'limit' => 50, // The maximum amount of items a user can have in his inventory (false means no limit)
		'items_per_page' => 25,
		'consume_show_results' => 'first' // first or multiple
	],
	'stores' => [
		'haggle' => true
	],
	'safe' => [
		'pagination' => 30
	],
	'shop' => [
		'description_char_limit' => '500',
		'creation_cost' => '200',
		'log_retention' => '30 days',
		'log_limit' => '35',
		'notify_sale' => true,
		'stock' => [
			'pagination' => 25
		],
		'size' => [
			'active' => true,
			'unit_cost' => '100',
			'unit_size' => '10',
		],
	],
	'trade' =>
		array(
			'currency_image' => false,
			'lots' =>
				array(
					'max_results' => '25',
					'max_items' => '10',
					'count_amount' => true,
				),
			'bids' =>
				array(
					'max_results' => '20',
					'max_items' => '10',
					'count_amount' => true,
					'max_in_stack' => '10',
				),
		),
);