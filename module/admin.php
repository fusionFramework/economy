<?php defined('SYSPATH') OR die('No direct script access.');

Plug::listen('admin.nav_list', function() {
	return [
		'title' => 'Economy',
		'link'  => '#',
		'icon'  => 'fa fa-money',
		'items' => array(
			array(
				'title' => 'Items',
				'route' => 'admin.items.index',
				'icon'  => 'fa fa-lemon-o',
			),
			array(
				'title' => 'Stores',
				'route' => 'admin.stores.index',
				'icon'  => 'fa fa-shopping-cart',
			)
		)
	];
});

Plug::listen('admin.search', function($type, $term, $handle){
	$return = array();

	switch($type)
	{
		case 'item':
			if($handle == 'data')
			{
				$items = ORM::factory('Item')
					->with('type')
					->where('item.name', 'LIKE', '%'.$term.'%')
					->find_all();

				if($items->count() == 0)
				{
					$return = null;
				}
				else
				{
					foreach($items as $item)
					{
						$return[] = ['id' => $item->id, 'value' => $item->name, 'type' => $item->type->name, 'image' => $item->img()];
					}
				}
			}
			else
				$return = '<div class="row"><div class="col-sm-2"><img src="<%image%>" width="20" height="20" /></div><div class="col-sm-8">#<%id%> <%value%></div></div>';

			break;
		case 'item-type':
			if($handle == 'data')
			{
				$types = ORM::factory('Item_Type')
					->where('name', 'LIKE', '%'.$term.'%')
					->find_all();

				if($types->count() == 0)
				{
					$return = null;
				}
				else
				{
					foreach($types as $type)
					{
						$return[] = ['id' => $type->id, 'value' => $type->name, 'cmd' => $type->default_command];
					}
				}
			}
			else
				$return = '<p><small>#<%id%></small> <strong>{<%value%></strong> <small>(<%cmd%>)</small></p>';
			break;
		case 'store':
			if($handle == 'data')
			{
				$stores = ORM::factory('Store')
					->where('title', 'LIKE', '%'.$term.'%')
					->find_all();

				if($stores->count() == 0)
				{
					$return = null;
				}
				else
				{
					foreach($stores as $store)
					{
						$return[] = ['id' => $store->id, 'value' => $store->title, 'status' => $store->status, 'type' => $store->type];
					}
				}
			}
			else
				$return = '<p><small>#<%id%></small> <strong><%value%></strong><br /><em>(<%status%>) <small>- <%type%></small></em></p>';
			break;
	}

	if(count($return) == 0)
		return null;

	return $return;
});

Plug::listen('admin.user.tabs', function() {
	return [
		[
			'title' => 'Economy',
			'id' => 'tab-economy'
		]
	];
});

Plug::listen('admin.dashboard.stats', function($cache) {
	$economy = $cache->get('stats.economy', [
		'total_points' => 'n/a',
		'points_per_user' => 'n/a',
		'items_circulating' => 'n/a',
		'items_per_user' => 'n/a'
	]);

	return [
		'title' => 'Economy',
		'icon' => 'fa fa-money',
		'id' => 'stat-economy',
		'items' => [
			[
				'title' => 'Points in circulation',
				'value' => $economy['total_points']
			],
			[
				'title' => 'Points per user',
				'value' => $economy['points_per_user']
			],
			[
				'title' => 'Items in circulation',
				'value' => $economy['items_circulating']
			],
			[
				'title' => 'Items per user',
				'value' => $economy['items_per_user']
			]
		]
	];
});