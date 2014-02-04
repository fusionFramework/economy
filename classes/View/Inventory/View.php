<?php defined('SYSPATH') OR die('No direct script access.');

class View_Inventory_View extends Views {
	/**
	 * Store the item's information
	 * @var User_Item
	 */
	public $item = FALSE;

	/**
	 * Contains the item's action list
	 * @var array
	 */
	public $action_list = FALSE;

	/**
	 * Build the item template data, along with the action list.
	 * @return array
	 */
	public function item()
	{
		$return = array(
			'image'  => $this->item->img(),
			'amount' => $this->item->amount,
			'name'   => $this->item->item->name,
			'menu'   => array()
		);

		$url = Route::url('inventory.consume', array('id' => $this->item->id), true);

		foreach ($this->action_list as $type => $action)
		{
			if ($action['extra'] == NULL)
			{
				$return['menu'][] = array(
					'normal' => array(
						'url'    => $url,
						'action' => $type,
						'crsf'   => $this->csrf(),
						'text'   => $action['item']
					)
				);
			}
			else
			{
				$return['menu'][] = array(
					'extra' => array(
						'url'           => $url,
						'action'        => $type,
						'crsf'          => $this->csrf(),
						'text'          => $action['item'],
						'field_type'    => $action['extra']['field']['type'],
						'field_name'    => $action['extra']['field']['name'],
						'field_classes' => $action['extra']['field']['classes'],
						'field_button'  => $action['extra']['field']['button']
					)
				);
			}
		}

		return $return;
	}
}
