<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Store model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_Store extends ORM {

	protected $_table_columns = array(
		'id' => NULL,
		'title' => NULL,
		'npc_id' => NULL,
		'stock_type' => NULL,
		'stock_cap' => NULL,
		'status' => NULL
	);

	protected $_belongs_to = array(
		'npc' => array(
			'model' => 'NPC',
			'foreign_key' => 'npc_id'
		),
	);

	protected $_has_many = array(
		'items' => array(
			'model' => 'Store_Inventory',
			'foreign_key' => 'store_id'
		),
		'restocks' => array(
			'model' => 'Store_Restock',
			'foreign_key' => 'store_id'
		)
	);

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 60)),
			),
			'npc_id' => array(
				array('not_empty'),
			),
			'stock_type' => array(
				array('not_empty'),
				array('in_array', array(':value', array('restock', 'steady'))),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array('closed', 'open'))),
			),
			'stock_cap' => array(
				array('not_empty'),
				array('digit')
			)
		);
	}

	use Formo_ORM;
	protected $_primary_val = 'title';

	/**
	 * Define form fields based on model properties.
	 *
	 * @param Formo $form
	 */
	public function formo(Formo $form)
	{
		if($form->find('title') != null)
		{
			$form->title->set('label', 'Title')
				->set('driver', 'input')
				->set('attr.class', 'form-control');
		}

		if($form->find('stock_cap') != null)
		{
			$form->stock_cap->set('label', 'Stock cap')
				->set('driver', 'input')
				->set('attr.type', 'number')
				->set('attr.class', 'form-control');
		}

		if($form->find('status') != null)
		{
			$form->status->set('label', 'Status')
				->set('driver', 'select')
				->set('opts', ['closed' => 'Closed', 'open' => 'Open'])
				->set('attr.class', 'form-control');
		}

		if($form->find('stock_type') != null)
		{
			$form->stock_type->set('label', 'Stock type')
				->set('driver', 'select')
				->set('opts', ['restock' => 'Restock', 'steady' => 'Steady'])
				->set('attr.class', 'form-control');
		}

		if($form->find('npc_id') != null)
		{
			$ids = [];
			$store_npcs = $this->where('npc_id', '!=', '')->find_all();

			foreach($store_npcs as $store)
			{
				$ids[] = $store->npc_id;
			}

			$list = [];
			$npcs = ORM::factory('NPC')
				->where('type', '=', 'store')
				->find_all();

			//only get available NPCs
			foreach($npcs as $npc)
			{
				if(!in_array($npc->id, $ids))
				{
					$list[$npc->id] = $npc->name;
				}
			}

			$form->npc_id->set('label', 'NPC')
				->set('driver', 'select')
				->set('opts', $list)
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
		return $this->title;
	}

} // End Store Model
