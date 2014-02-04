<?php defined('SYSPATH' OR die('No direct access allowed.'));
/**
 * Minimalistic menu config example.
 * Renders a simple list (<li>) of links.
 *
 * @see https://github.com/anroots/kohana-menu/wiki/Configuration-files
 * @author Ando Roots <ando@sqroot.eu>
 */
return [
	'active_item_class' => 'disabled',
	'items'             => [
		[
			'route'   => 'shop.index',
			'title'   => 'Manage shop',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'shop.stock',
			'title'   => 'Stock',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'shop.logs',
			'title'   => 'Logs',
			'classes'   => ['btn', 'btn-default']
		],
		[
			'route'   => 'shop.view',
			'route_param' => ['id'],
			'title'   => 'View your shop',
			'classes'   => ['btn', 'btn-default']
		],
	]
];