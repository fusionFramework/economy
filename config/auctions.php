<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'enabled' => TRUE,
	'list' => [
        'length' => '30 days', // how far the user can look back in its created auctions
		'pagination' => [
			'total_items' => 20
		]
	],
	'creation' => [
		'increment' => [
			'min' => 5,
			'max' => 5000
		],
		'lengths' => [
			'30 minutes',
			'1 hour',
			'5 hours',
			'12 hours',
			'1 day',
			'2 days',
			'3 days'
		],
		'max_auctions' => 5 // max auctions per user
	]
];