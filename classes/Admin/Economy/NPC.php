<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * NCP admin
 *
 * @package    fusionFramework/economy
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Admin_Economy_NPC extends Admin
{
	public  $resource = "npc";
	public $icon = 'fa fa-male';
	public $primary_key = 'npc.id';
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
		$table->add_column('name', array('head' => 'Name', 'class' => 'col-lg-4'));
		$table->add_column('type', array('head' => 'Type', 'class' => 'col-lg-2'));

		return $table;
	}

	protected function _setup()
	{
		$this->model = ORM::factory('NPC');

		// a wider modal is needed for managing the item commands
		$this->modal['width'] = 650;
		$this->modal['height'] = 400;

		$this->_assets['set'][] = 'uploadify';

		$this->_assets['js'][] = 'admin/npc.js';

		$this->images = [
			'image' => [
				'web' => function($model){
						return URL::site('m/npc/' . $model->type . DIRECTORY_SEPARATOR . $model->image, true, false);
					},
				'move' => function($record, $image) {
						$record->image = strtolower(Inflector::underscore($record->name)).'.png';
						$path = WEBPATH . 'm' . DIRECTORY_SEPARATOR . 'npc' . DIRECTORY_SEPARATOR . $record->type;

						if(!file_exists($path))
						{
							mkdir($path);
						}
						rename($image, $path . DIRECTORY_SEPARATOR.$record->image);
					}
			]
		];
	}

	public function modal(Array $data)
	{
		$types = NPC::list_types();
		$data['messages'] = [];

		foreach($types as $ind => $type)
		{
			$npc = NPC::factory($type);
			$data['messages'][$ind] = $npc->messages;
		}

		return View::factory('admin/modal/npc', $data);
	}
}