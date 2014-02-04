<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * NPC model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Model_NPC extends ORM {

	protected $_table_columns = array(
		'id' => NULL,
		'name' => NULL,
		'type' => NULL,
		'image' => NULL,
		'messages' => NULL
	);

	protected $_serialize_columns = ['messages'];

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 60)),
			),
			'type' => array(
				array('not_empty'),
				array('max_length', array(':value', 30)),
			),
			'image' => array(
				array('max_length', array(':value', 70)),
			),
			'messages' => array(
				array('not_empty'),
			)
		);
	}

	/**
	 * Create the url to the npc's image
	 * @return string
	 */
	public function img()
	{
		return URL::site('m/npc/'.$this->type.'/' . $this->image, true);
	}

	/**
	 * Return one of the defined messages for $type.
	 *
	 * @param $type string message type
	 * @return string
	 * @throws Kohana_Exception
	 */
	public function message($type)
	{
		if(!array_key_exists($type, $this->messages))
		{
			throw new Kohana_Exception('No ":type" message found', [':type' => $type]);
		}

		return $this->messages[$type][array_rand($this->messages[$type])];
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

		if($form->find('image') != null)
		{
			$form->image->set('label', 'Image')
				->set('driver', 'image')
				->set('attr.class', 'image-upload');
		}

		if($form->find('type') != null)
		{
			$form->type->set('label', 'Type')
				->set('driver', 'select')
				->set('opts', NPC::list_types())
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

} // End Shop Model
