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
Route::set('trades.create', 'trades/create')
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'create',
	));
Route::set('trades.create.process', 'trades/create/process')
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'process_create',
	));
Route::set('trades.lots', 'trades/lots')
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'lots',
	));
Route::set('trades.lot', 'trades/lot/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'lot',
	));
Route::set('trades.delete', 'trades/lot/<id>/delete', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'delete',
	));
Route::set('trades.bid', 'trades/lot/<id>/bid', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'bid',
	));
Route::set('trades.bid.process', 'trades/lot/<id>/process', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'process_bid',
	));
Route::set('trades.bids', 'trades/bids')
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'bids',
	));
Route::set('trades.bids.accept', 'trades/bid/<id>/accept', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'accept',
	));
Route::set('trades.bids.reject', 'trades/bid/<id>/reject', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'reject',
	));
Route::set('trades.bids.retract', 'trades/bid/<id>/retract', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'retract',
	));
Route::set('trades.search', 'trades/search(/<term>(/<page>))', array('page' => '[0-9]+', 'term' => '([-a-zA-Z ]+)'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'search',
		'page'       => 1
	));
Route::set('trades.index', 'trades(/<page>)', array('page' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Trades',
		'action'     => 'index',
		'page'       => 1
	));

//  Auctions
if(Kohana::$config->load('auctions')->get('enabled', FALSE) != FALSE)
{
	Route::set('auctions.bids', 'auctions/bids')
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'bids'
			)
		);
	Route::set('auctions.list', 'auctions/list')
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'list'
			)
		);
	Route::set('auctions.create.process', 'auctions/create/process')
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'create_process'
			)
		);
	Route::set('auctions.create', 'auctions/create')
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'create'
			)
		);
	Route::set('auctions.search', 'auctions/search(/<term>(/<page>))', array('page' => '[0-9]+', 'term' => '([-a-zA-Z ]+)'))
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'search',
				'page'       => 1
			)
		);
	Route::set('auctions.view', 'auctions/view/<id>', array('id' => '[0-9]+'))
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'view'
			)
		);
	Route::set('auctions.bid', 'auctions/bid/<auction_id>', array('auction_id' => '[0-9]+'))
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'bid'
			)
		);
	Route::set('auctions.index', 'auctions(/<page>)', array('page' => '[0-9]+'))
		->defaults(array(
				'controller' => 'Auctions',
				'action'     => 'index',
				'page'       => 1
			)
		);
}

