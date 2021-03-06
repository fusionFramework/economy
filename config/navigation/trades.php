<?php defined('SYSPATH' OR die('No direct access allowed.'));
/**
 * Trades navigation menu
 */
return [
	'active_item_class' => 'disabled',
	'items'             => [
		[
			'route'   => 'trades.index',
			'title'   => 'List',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'trades.lots',
			'title'   => 'Your lots',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'trades.bids',
			'title'   => 'Your bids',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'trades.create',
			'title'   => 'Create a trade',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'trades.search',
			'title'   => 'Search',
			'classes'   => ['btn', 'btn-info']
		],
	]
];