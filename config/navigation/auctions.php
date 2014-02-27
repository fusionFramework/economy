<?php defined('SYSPATH' OR die('No direct access allowed.'));
/**
 * Auctions navigation menu
 */
return [
	'active_item_class' => 'disabled',
	'items'             => [
		[
			'route'   => 'auctions.index',
			'title'   => 'All auctions',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'auctions.list',
			'title'   => 'Your auctions',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'auctions.bids',
			'title'   => 'Your bids',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'auctions.create',
			'title'   => 'Create an auction',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'auctions.search',
			'title'   => 'Search',
			'classes'   => ['btn', 'btn-info']
		],
	]
];