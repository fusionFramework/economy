<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Item type model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_Item_Type extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'name' => NULL,
		'action' => NULL,
		'default_command' => NULL,
		'img_dir' => NULL
	);


	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
			),
			'action' => array(
				array('not_empty'),
				array('max_length', array(':value', 200)),
			),
			'default_command' => array(
				array('not_empty'),
				array('max_length', array(':value', 100)),
			),
			'img_dir' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
			),
		);
	}

	public function filters()
	{
		return array(
			'img_dir' => array(
				array('Item::filter_type_dir')
			)
		);
	}

	use Formo_ORM;
	protected $_primary_val = 'name';

	/**
	 * Define form fields based on model properties.
	 *
	 * @param Formo $form
	 */
	public function formo(Formo $form)
	{
		if($form->find('name') != null)
		{
			$form->name->set('label', 'Name')
				->set('driver', 'input')
				->set('attr.class', 'form-control');
		}

		if($form->find('action') != null)
		{
			$form->action->set('label', 'Action text')
				->set('driver', 'textarea')
				->set('attr.class', 'form-control')
				->set('message', 'This is the text that\'s shown before consuming an item.<br /><b>Available parameters:</b><br />- :item_name<br />- :pet_name<br /> e.g. open :item_name');
		}

		if($form->find('default_command') != null)
		{
			$underscore = function($value){
				return str_replace(DIRECTORY_SEPARATOR, '_', $value);
			};
			$commands = array_map($underscore, Item::list_commands());

			$form->default_command->set('label', 'Default command')
				->set('driver', 'select')
				->set('opts', $commands)
				->set('attr.class', 'form-control');
		}

		if($form->find('img_dir') != null)
		{
			$form->img_dir->set('label', 'Image dir')
				->set('driver', 'input')
				->set('attr.class', 'form-control');
		}
	}

	/**
	 * Used to represent in belongs_to relations when changes are tracked
	 * @return bool|string
	 */
	public function candidate_key()
	{
		if (!$this->loaded()) return FALSE;
		return $this->name;
	}

} // End Item Type Model
