<?php defined('SYSPATH') OR die('No direct script access.');

Plug::listen('admin.task.stats', function($cache){
	$total_users = ORM::factory('User')->count_all();
	$total_points = DB::select(array(DB::expr('SUM(`points`)'), 'total_points'))->from('users')->get('total_points');
	$total_items = DB::select(array(DB::expr('SUM(`amount`)'), 'total_items'))->from('user_items')->get('total_items');
	$cache->set('stats.economy', [
			'total_points' => $total_points,
			'points_per_user' => $total_points / $total_users,
			'items_circulating' => $total_items,
			'items_per_user' => $total_items / $total_users
		], 60*60*24);

	Minion_CLI::write('Economy stats were cached.');
});