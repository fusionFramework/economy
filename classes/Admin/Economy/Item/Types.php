<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * Item type admin
 *
 * @package    fusionFramework/economy
 * @category   Admin
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Admin_Economy_Item_Types extends Admin
{
	public  $resource = "item.types";
	public $icon = 'fa fa-lemon-o';
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
		$table->add_column('name', array('head' => 'Name', 'class' => 'col-lg-3'));
		$table->add_column('default_command', array('head' => 'Default command'));
		$table->add_column('img_dir', array('head' => 'Image dir'));

		return $table;
	}

	protected function _setup()
	{
		$this->model = ORM::factory('Item_Type');
	}

	public function modal(Array $data)
	{
		$form = $data['model']->get_form(['name', 'action', 'default_command', 'img_dir']);
		return $form;
	}

	public function save(ORM $model, Array $data, $namespace)
	{
		$underscore = function($value){
			return str_replace(DIRECTORY_SEPARATOR, '_', $value);
		};
		$commands = array_map($underscore, Item::list_commands());

		$data[$namespace]['default_command'] = $commands[$data[$namespace]['default_command']];
		return $data;
	}
}