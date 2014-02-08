<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Item model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_Item extends ORM
{
	protected $_table_columns = array(
		'id' => NULL,
		'type_id' => NULL,
		'name' => NULL,
		'description' => NULL,
		'image' => NULL,
		'status' => NULL,
		'unique' => NULL,
		'transferable' => NULL,
		'commands' => NULL,
	);

	protected $_belongs_to = array(
		'type' => array(
			'model' => 'Item_Type',
			'foreign_key' => 'type_id',
			'formo' => true
		),
	);

	protected $_serialize_columns = array('commands');

	protected $_load_with = array('type');

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 50)),
			),
			'description' => array(
				array('not_empty'),
			),
			'image' => array(
				array('not_empty'),
				array('max_length', array(':value', 200)),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array('draft', 'released', 'retired'))),
			),
			'commands' => array(
				array('not_empty'),
				array('Item::validate_commands', array(':validation', ':value'))
			)
		);
	}

	/**
	 * Create the url to the item's image
	 * @return string
	 */
	public function img()
	{
		return URL::site('m/items/' . $this->type->img_dir . $this->image, true);
	}

	/**
	 * Check if the item isn't a draft or retired.
	 * @return boolean
	 */
	public function in_circulation()
	{
		return ($this->status == 'released');
	}

	/**
	 * Get the item's name based on an amount
	 *
	 * @param integer $amount
	 *
	 * @return string
	 */
	public function name($amount)
	{
		if ($amount > 1)
		{
			return $amount . ' ' . Inflector::plural($this->name, $amount);
		}
		else
		{
			return $amount . ' ' . $this->name;
		}
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

		if($form->find('description') != null)
		{
			$form->description->set('label', 'Description')
				->set('driver', 'textarea')
				->set('attr.class', 'form-control');
		}

		if($form->find('status') != null)
		{
			$form->status->set('label', 'Status')
				->set('driver', 'select')
				->set('opts', ['draft' => 'Draft', 'released' => 'Released', 'retired' => 'Retired'])
				->set('attr.class', 'form-control');
		}

		if($form->find('type_id') != null)
		{
			$types = ORM::factory('Item_Type')->find_all();
			$opts = [0 => 'Select'];
			foreach($types as $t)
			{
				$opts[$t->id] = $t->name;
			}

			$form->type_id->set('label', 'Type')
				->set('driver', 'select')
				->set('opts', $opts)
				->set('attr.class', 'form-control');
		}

		if($form->find('image') != null)
		{
			$form->image->set('label', 'Image')
				->set('driver', 'image')
				->set('dim', Arr::extract(Kohana::$config->load('items.image'), ['width', 'height']))
				->set('attr.class', 'image-upload');
		}

		if($form->find('unique') != null)
		{
			$form->unique->set('label', 'Unique?')
				->set('driver', 'radios')
				->set('opts', ['0' => 'No', '1' => 'Yes'])
				->set('attr.class', 'form-control')
				->set('message', 'If the item is unique it won\'t be stackable.');
		}
		if($form->find('transferable') != null)
		{
			$form->transferable->set('label', 'Transferable?')
				->set('driver', 'radios')
				->set('opts', ['0' => 'No', '1' => 'Yes'])
				->set('attr.class', 'form-control')
				->set('message', 'Can users transfer this item to other users?');
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

} // End Item Model
