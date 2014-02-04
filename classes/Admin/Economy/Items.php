<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * Item admin
 *
 * @package    fusionFramework/economy
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Admin_Economy_Items extends Admin
{
	public  $resource = "items";
	public $icon = 'fa fa-lemon-o';
	public $primary_key = 'item.id';
	public $track_changes = TRUE;

	/**
	 * Set up the dataTable definition for this controller.
	 *
	 * @see Table
	 *
	 * @param Table $table
	 *
	 * @return Table A fully configured dataTable definition
	 */
	public function setup_table($table)
	{
		$table->add_column('image', array('head' => 'Image', 'retrieve' => function(Model_Item $item){
				return $item->img();
			}, 'format' => 'image', 'param' => [30,30], 'class' => 'col-lg-1'), false, false);

		$table->add_column('name', array('head' => 'Name', 'class' => 'col-lg-3'));
		$table->add_column('type', array('head' => 'Type', 'retrieve' => 'type.name'));
		$table->add_column('status', array('head' => 'Status', 'class' => 'col-lg-1', 'format' => function(){
				return "var icon = '';
				switch(data){
					default:
					case 'draft':
						icon = 'fa fa-wrench';
					break;
					case 'released':
						icon = 'fa fa-check';
					break;
					case 'retired':
						icon = 'fa fa-suitcase';
					break;
				}
				return '<p class=\"text-center\"><a href=\"#\" class=\"nolink tt\" title=\"'+data+'\"><i class=\"'+icon+'\"></i></p>';
				";
			}), true, false);

		return $table;
	}

	protected function _setup()
	{
		$this->model = ORM::factory('Item');

		// a wider modal is needed for managing the item commands
		$this->modal['width'] = 650;
		$this->modal['height'] = 400;

		$this->_assets['set'][] = 'uploadify';
		$this->_assets['set'][] = 'typeahead';
		$this->_assets['css'][] = 'bootstrap-submenu.css';
		$this->_assets['js'][] = 'admin/items.js';


		$this->images = [
			'image' => [
				'web' => function($model){
						return URL::site('m/items/' . $model->type->img_dir . $model->image, true, false);
					},
				'move' => function($record, $image) {
						$record->image = strtolower(Inflector::underscore($record->name)).'.png';
						$destination = WEBPATH . 'm' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . $record->type->img_dir;

						if(!file_exists($destination))
						{
							mkdir($destination);
						}
						copy($image, $destination .$record->image);
					}
			]
		];
	}

	public function modal(Array $data)
	{
		$commands = Item::list_commands();
		$input_c = [];
		$menu_c = [];
		$def_c = [];
		$searches = [];

		foreach ($commands as $cmd)
		{
			$name = str_replace(DIRECTORY_SEPARATOR, '_', $cmd);
			$class = 'Item_Command_' . $name;
			$command = new $class;

			if ($command->is_default() == FALSE)
			{
				$struct = explode('_', $name);
				$admin = $command->build_admin($name);
				$input_c[$name] = array('title' => $admin['title'], 'fields' => $admin['fields']);

				if($admin['search'] != '0')
				{
					$searches[] = $admin['search'];
				}

				$def_c[$name] = array(
					'multiple' => $admin['multiple'],
					'pets' => $admin['pets'],
					'search' => $admin['search'],
					'only' => (!$command->allow_more)
				);

				if(!isset($menu_c[$struct[0]]))
				{
					$menu_c[$struct[0]] = ['name' => $struct[0], 'commands' => []];
				}

				$menu_c[$struct[0]]['commands'][] = array(
					'name' => $struct[1],
					'cmd' => $name
				);
			}
		}
		$types = ORM::factory('Item_Type')->find_all();
		foreach ($types as $t)
		{
			$data['type_map'][$t->name] = $t->default_command;
		}
		$data['input_commands'] = $input_c;
		$data['menu_commands'] = $menu_c;
		$data['command_definitions'] = $def_c;
		$data['searches'] = $searches;
		return View::factory('admin/modal/items', $data);
	}
}