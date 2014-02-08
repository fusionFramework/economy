<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'description_char_limit' => '500',
	'creation_cost' => '200',
	'log_retention' => '30 days',
	'log_limit' => '35',
	'notify_sale' => true,
	'stock' => [
		'pagination' => [
			'total_items' => 25
		]
	],
	'size' => [
		'active' => true,
		'unit_cost' => '100',
		'unit_size' => '10',
	]
];