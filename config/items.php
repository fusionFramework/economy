<?php defined('SYSPATH') OR die('No direct script access.');

return array (
	'image' => [
		'width' => '80',
		'height' => '80',
		'formats' => ['png'],
		'tmp_dir' => WEBPATH.'m'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR,
	],
	'inventory' => [
		'limit' => 50, // The maximum amount of items a user can have in his inventory (false means no limit)
		'pagination' => [
			'total_items' => 25
		],
		'consume_show_results' => 'first' // first or multiple
	],
	'stores' => [
		'haggle' => true
	],
	'safe' => [
		'pagination' => [
			'total_items' => 30
		]
	]
);