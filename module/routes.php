<?php 

/**
 *	Item types admin routes
 */
//set the js file route
Route::set('admin.item.types.js', 'admin/item/types/table.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js',
		'master'     => 'Admin_Economy_Item_Types'
	)
);

//set the actions js file route
Route::set('admin.item.types.actions.js', 'admin/item/types/actions.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js_actions',
		'master'     => 'Admin_Economy_Item_Types',
	)
);

//set the fill table route
Route::set('admin.item.types.fill', 'admin/item/types/fill(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'fill_table',
		'id' => 0,
		'master' => 'Admin_Economy_Item_Types'
	)
);

//set the delete record route
Route::set('admin.item.types.remove', 'admin/item/types/<id>/remove', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'remove',
		'master' => 'Admin_Economy_Item_Types'
	)
);


//set the record history route
Route::set('admin.item.types.history', 'admin/item/types/<id>/history', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'history',
		'master' => 'Admin_Economy_Item_Types'
	)
);


//set the load record route
Route::set('admin.item.types.modal', 'admin/item/types/<id>/load', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'modal',
		'master' => 'Admin_Economy_Item_Types'
	)
);

//set the save record route
Route::set('admin.item.types.save', 'admin/item/types/save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'save',
		'master' => 'Admin_Economy_Item_Types'
	)
);


//set the index route
Route::set('admin.item.types.index', 'admin/item/types(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'table',
		'id' => null,
		'master' => 'Admin_Economy_Item_Types'
	)
);

/**
 *	Items admin routes
 */
//set the js file route
Route::set('admin.items.js', 'admin/items/table.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js',
		'master'     => 'Admin_Economy_Items'
	)
);

//set the actions js file route
Route::set('admin.items.actions.js', 'admin/items/actions.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js_actions',
		'master'     => 'Admin_Economy_Items',
	)
);

//set the fill table route
Route::set('admin.items.fill', 'admin/items/fill(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'fill_table',
		'id' => 0,
		'master' => 'Admin_Economy_Items'
	)
);

//set the delete record route
Route::set('admin.items.remove', 'admin/items/<id>/remove', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'remove',
		'master' => 'Admin_Economy_Items'
	)
);


//set the record history route
Route::set('admin.items.history', 'admin/items/<id>/history', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'history',
		'master' => 'Admin_Economy_Items'
	)
);

//set the upload route
Route::set('admin.items.upload', 'admin/items/upload')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'upload',
		'master' => 'Admin_Economy_Items'
	)
);

//set the load record route
Route::set('admin.items.modal', 'admin/items/<id>/load', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'modal',
		'master' => 'Admin_Economy_Items'
	)
);

//set the save record route
Route::set('admin.items.save', 'admin/items/save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'save',
		'master' => 'Admin_Economy_Items'
	)
);


//set the index route
Route::set('admin.items.index', 'admin/items(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'table',
		'id' => null,
		'master' => 'Admin_Economy_Items'
	)
);

/**
 *	Npc admin routes
 */
//set the js file route
Route::set('admin.npc.js', 'admin/npc/table.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js',
		'master'     => 'Admin_Economy_NPC'
	)
);

//set the actions js file route
Route::set('admin.npc.actions.js', 'admin/npc/actions.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js_actions',
		'master'     => 'Admin_Economy_NPC',
	)
);

//set the fill table route
Route::set('admin.npc.fill', 'admin/npc/fill(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'fill_table',
		'id' => 0,
		'master' => 'Admin_Economy_NPC'
	)
);

//set the delete record route
Route::set('admin.npc.remove', 'admin/npc/<id>/remove', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'remove',
		'master' => 'Admin_Economy_NPC'
	)
);


//set the record history route
Route::set('admin.npc.history', 'admin/npc/<id>/history', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'history',
		'master' => 'Admin_Economy_NPC'
	)
);

//set the upload route
Route::set('admin.npc.upload', 'admin/npc/upload')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'upload',
		'master' => 'Admin_Economy_NPC'
	)
);

//set the load record route
Route::set('admin.npc.modal', 'admin/npc/<id>/load', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'modal',
		'master' => 'Admin_Economy_NPC'
	)
);

//set the save record route
Route::set('admin.npc.save', 'admin/npc/save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'save',
		'master' => 'Admin_Economy_NPC'
	)
);


//set the index route
Route::set('admin.npc.index', 'admin/npc(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'table',
		'id' => null,
		'master' => 'Admin_Economy_NPC'
	)
);

/**
 *	Store restock admin routes
 */
//set the js file route
Route::set('admin.store.restock.js', 'admin/store/restock/table.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js',
		'master'     => 'Admin_Economy_Store_Restock'
	)
);

//set the actions js file route
Route::set('admin.store.restock.actions.js', 'admin/store/restock/actions.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js_actions',
		'master'     => 'Admin_Economy_Store_Restock',
	)
);

//set the fill table route
Route::set('admin.store.restock.fill', 'admin/store/restock/fill(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'fill_table',
		'id' => 0,
		'master' => 'Admin_Economy_Store_Restock'
	)
);

//set the delete record route
Route::set('admin.store.restock.remove', 'admin/store/restock/<id>/remove', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'remove',
		'master' => 'Admin_Economy_Store_Restock'
	)
);


//set the record history route
Route::set('admin.store.restock.history', 'admin/store/restock/<id>/history', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'history',
		'master' => 'Admin_Economy_Store_Restock'
	)
);


//set the load record route
Route::set('admin.store.restock.modal', 'admin/store/restock/<id>/load', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'modal',
		'master' => 'Admin_Economy_Store_Restock'
	)
);

//set the save record route
Route::set('admin.store.restock.save', 'admin/store/restock/save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'save',
		'master' => 'Admin_Economy_Store_Restock'
	)
);


//set the index route
Route::set('admin.store.restock.index', 'admin/store/restock(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'table',
		'id' => null,
		'master' => 'Admin_Economy_Store_Restock'
	)
);

/**
 *	Stores admin routes
 */
//set the js file route
Route::set('admin.stores.js', 'admin/stores/table.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js',
		'master'     => 'Admin_Economy_Stores'
	)
);

//set the actions js file route
Route::set('admin.stores.actions.js', 'admin/stores/actions.js')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action'     => 'js_actions',
		'master'     => 'Admin_Economy_Stores',
	)
);

//set the fill table route
Route::set('admin.stores.fill', 'admin/stores/fill(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'fill_table',
		'id' => 0,
		'master' => 'Admin_Economy_Stores'
	)
);

//set the delete record route
Route::set('admin.stores.remove', 'admin/stores/<id>/remove', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'remove',
		'master' => 'Admin_Economy_Stores'
	)
);


//set the record history route
Route::set('admin.stores.history', 'admin/stores/<id>/history', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'history',
		'master' => 'Admin_Economy_Stores'
	)
);


//set the load record route
Route::set('admin.stores.modal', 'admin/stores/<id>/load', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'modal',
		'master' => 'Admin_Economy_Stores'
	)
);

//set the save record route
Route::set('admin.stores.save', 'admin/stores/save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'save',
		'master' => 'Admin_Economy_Stores'
	)
);

//set the save record route
Route::set('admin.stores.item_save', 'admin/stores/item_save')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'proxy',
		'admin_action' => 'item_save',
		'master' => 'Admin_Economy_Stores'
	)
);
//set the save record route
Route::set('admin.stores.item_remove', 'admin/stores/item_remove')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'proxy',
		'admin_action' => 'item_remove',
		'master' => 'Admin_Economy_Stores'
	)
);
//set the save record route
Route::set('admin.stores.item_load', 'admin/stores/item_load')
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'proxy',
		'admin_action' => 'item_load',
		'master' => 'Admin_Economy_Stores'
	)
);

//set the index route
Route::set('admin.stores.index', 'admin/stores(/<id>)', array('id' => '([0-9]*)'))
	->defaults(array(
		'controller' => 'Fusion_CRUD',
		'action' => 'table',
		'id' => null,
		'master' => 'Admin_Economy_Stores'
	)
);