<?php defined('SYSPATH') OR die('No direct script access.');

// Inventory routes
Route::set('inventory.view', 'inventory/view/<id>', array('id' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Inventory',
			'action'     => 'view'
		)
	);
Route::set('inventory.consume', 'inventory/consume/<id>', array('id' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Inventory',
			'action'     => 'consume'
		)
	);
Route::set('inventory.index', 'inventory(/<page>)', array('page' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Inventory',
			'action'     => 'index',
			'page'       => 1
		)
	);

// Stores
Route::set('stores.buy', 'stores/<store_id>/<item_id>', array('store_id' => '([0-9]*)', 'item_id' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Stores',
			'action'     => 'buy',
		)
	);
Route::set('stores.view', 'stores/<id>', array('id' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Stores',
			'action'     => 'view',
		)
	);
Route::set('stores.index', 'stores')
	->defaults(array(
			'controller' => 'Stores',
			'action'     => 'index'
		)
	);

// Safety deposit box
Route::set('safe.index', 'safe(/<page>)', array('page' => '([0-9]*)'))
	->defaults(array(
			'controller' => 'Safe',
			'action'     => 'index',
			'page'       => 1
		)
	);
Route::set('safe.move', 'safe/move')
	->defaults(array(
			'controller' => 'Safe',
			'action'     => 'move'
		)
	);

//  User shops
Route::set('shop.index', 'shop')
	->defaults(array(
			'controller' => 'Shop',
			'action'     => 'index',
		)
	);
Route::set('shop.create', 'shop/create')
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'create',
	)
);
Route::set('shop.upgrade', 'shop/upgrade')
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'upgrade',
	)
);
Route::set('shop.update', 'shop/update')
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'update',
	));
Route::set('shop.stock', 'shop/stock(/<page>)', array('page' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'stock',
		'page'       => 1
	));
Route::set('shop.inventory', 'shop/inventory(/<page>)', array('page' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'inventory',
		'page'       => 1
	));
Route::set('shop.logs', 'shop/logs')
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'logs',
	));
Route::set('shop.collect', 'shop/collect')
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'collect',
	));
Route::set('shop.buy', 'shop/<id>/buy/<item_id>', array('id' => '[0-9]+', 'item_id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'buy',
	));
Route::set('shop.view', 'shop/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Shop',
		'action'     => 'view',
	));

//  Trades
Route::set('trades.index', 'trades')
	->defaults(array(
			'controller' => 'Safe',
			'action'     => 'index',
			'page'       => 1
		)
	);

//  Auctions
Route::set('auctions.index', 'auctions')
	->defaults(array(
			'controller' => 'Safe',
			'action'     => 'index',
			'page'       => 1
		)
	);
