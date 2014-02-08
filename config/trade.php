<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'lots' => [
		'pagination' => [
			'total_items' => 20
		],
		'max_items' => '10',
		'count_amount' => true,
	],
	'bids' => [
		'pagination' => [
			'total_items' => 20
		],
		'max_items' => '10',
		'count_amount' => true,
		'max_in_stack' => '10',
	],
];