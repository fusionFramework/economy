<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * View a single auction
 *
 * @package    fusionFramework/economy
 * @category   View
 * @author     Maxim Kerstens
 * @copyright  (c) Maxim Kerstens
 */
class Fusion_View_Auctions_View extends Views
{

	public $title = 'Auction';

    /**
     * Stores the auction
     * @var Model_User_Auction
     */
    public $auction = false;

    /**
     * Stores the last bid
     * @var Model_User_Auction_Bid
     */
    public $bid = false;

    /**
     * Stores the auction's item
     * @var Model_User_Item
     */
    public $item = false;

    /**
     * @var integer|false Stores the auction's auto_buy
     */
    public $auto_buy = false;

	/**
	 * Stores the navigation
	 * @var string
	 */
	public $menu = false;

	/**
	 * Simplify lot data
	 * @return array
	 */
	public function lot()
	{
        if($this->auction->until > time())
        {
            $span = Date::span(time(), $this->auction->until, 'days,hours,minutes,seconds');
            $until = '';

            foreach($span as $type => $val)
            {

                if($val > 0)
                {
                    $type = ($val > 1) ? $type : Inflector::singular($type);
                    $until .= $val . ' ' . $type . ' ';
                }
            }
        }
        else
        {
            $until = 'ended';
        }

		return array(
            'id'           => $this->auction->id,
            'item'         => [
                'name' => $this->item->item->name,
                'img'  => $this->item->img()
            ],
            'username'     => $this->auction->user->username,
            'user_profile' => Route::url('user.profile', array('name' => $this->auction->user->username)),
            'until'        => $until,
            'start_bid'    => $this->auction->start_bid,
            'increment'    => $this->auction->min_increment,
            'auto_buy'    => $this->auction->auto_buy,
            'bid_link'     => Route::url('auctions.bid', ['auction_id' => $this->auction->id])
        );
	}

    /**
     * Simplify bid data
     * @return array|bool
     */
    public function bid()
    {
        if($this->has_bid())
        {
            return array (
                'points' => $this->bid->points,
                'next_bid' => $this->auction->min_increment + $this->bid->points,
                'username' => $this->bid->user->username,
                'user_profile' => Route::url('user.profile', array('name' => $this->bid->user->username)),
            );
        }

        return false;
    }

    /**
     * Check if the auction has a last made bid
     * @return bool
     */
    public function has_bid()
    {
        return $this->bid != false && $this->bid->loaded();
    }

    /**
     * Count all the bids that were made on this auction
     * @return integer
     */
    public function total_bids()
    {
        return DB::select([DB::expr('COUNT(id)', 'total')])
            ->from('logs')
            ->where('alias_id', '=', $this->auction->id)
            ->where('alias', '=', 'auction.bid')
            ->execute()
            ->get('total');
    }

    /**
     * Check if the user can make a bid (if he's not the owner or last bidder)
     * @return bool
     */
    public function can_bid()
    {
        $users = [$this->auction->user_id];

        if($this->has_bid())
        {
            $users[] = $this->bid->user_id;
        }

        return !in_array(Fusion::$user->id, $users);
    }

    /**
     * Check if there's an auto buy option
     * @return bool
     */
    public function auto_buy()
    {
        return $this->auction->auto_buy > 0;
    }
}
