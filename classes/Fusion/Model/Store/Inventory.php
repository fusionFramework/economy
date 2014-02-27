<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Store inventory model
 *
 * @package    fusionFramework/economy
 * @category   Model
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Model_Store_Inventory extends ORM
{
    protected $_table_columns = array(
        'id' => NULL,
        'store_id' => NULL,
        'item_id' => NULL,
        'price' => NULL,
        'stock' => NULL
    );

    protected $_belongs_to = array(
        'item' => array(
            'model' => 'Item',
            'foreign_key' => 'item_id'
        ),
        'store' => array(
            'model' => 'Store',
            'foreign_key' => 'store_id'
        )
    );

    public function rules()
    {
        return array(
            'price' => array(
                array('not_empty'),
                array('digit')
            ),
            'stock' => array(
                array('not_empty'),
                array('digit')
            )
        );
    }

    public function buy(Model_User $user, $price=null)
    {
        if($this->stock == 0)
            throw new Kohana_Exception($this->npc->message('sold_out'), [':item' => $this->item->name]);

        if($price == null)
        {
            $price = $this->price;
        }

        $success = $user->points($price, '-');

        if($success)
        {
            Item::factory($this->item)
                ->to_user($user, 'store.'.$this->store_id, 1, [':price' => $price]);

            $this->stock -= 1;
            $this->save();
        }
        else
        {
            throw new Kohana_Exception('You don\'t have enough :currency to buy :item_name', [
                ':currency' => Fusion::$config['currency']['plural'],
                ':item_name' => $this->item->name
            ]);
        }
    }

} // End Store Inventory Model
