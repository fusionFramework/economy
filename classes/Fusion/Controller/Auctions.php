<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Auctions controller
 *
 * @package    fusionFramework/economy
 * @category   Controller
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
class Fusion_Controller_Auctions extends Controller_Fusion_Site {

	/**
	 * List auctions
	 */
	public function action_index()
	{
		$this->_tpl = new View_Safe;

		$config = Kohana::$config->load('items.safe');

		$items = Item::location('safe');

		$paginate = Paginate::factory($items, $config['pagination'], $this->request)->execute();

		$this->_tpl->pagination = $paginate->render();
		$this->_tpl->items = $paginate->result();

		$this->_tpl->process_url = Route::url('safe.move');
		$this->_tpl->shop = DB::select([DB::expr('COUNT(id)'), 'total'])
			->from('user_shops')
			->where('user_id', '=', Fusion::$user->id)
			->execute()
			->get('total');
	}

	/**
	 * View a single auction
	 */
	public function action_view()
	{

	}

	/**
	 * Create an auction
	 */
	public function action_create()
	{

	}

	/**
	 * Process an auction
	 */
	public function action_create_process()
	{

	}

	/**
	 * Bid on an auction
	 */
	public function action_bid()
	{

	}

	/**
	 * Process a bid on an auction
	 */
	public function action_bid_process()
	{

	}

	/**
	 * Show user's bids
	 */
	public function action_bids()
	{

	}

	/**
	 * Show user's created auctions
	 */
	public function action_list()
	{

	}

	/**
	 * Search for an item in auction
	 */
	public function action_search()
	{

	}
}
