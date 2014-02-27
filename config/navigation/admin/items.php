<?php defined('SYSPATH' OR die('No direct access allowed.'));
/**
 * Auctions navigation menu
 */
return [
	'active_item_class' => 'disabled',
	'items'             => [
		[
			'route'   => 'admin.items.index',
			'title'   => 'Items',
			'classes' => ['btn', 'btn-default'],
            'icon'    => 'fa fa-lemon-o'
		],
		[
			'route'   => 'admin.item.types.index',
			'title'   => 'Types',
			'classes'   => ['btn', 'btn-default'],
            'icon'    => 'fa fa-bars'
		]
	]
];