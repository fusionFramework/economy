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