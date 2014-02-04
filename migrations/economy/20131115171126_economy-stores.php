<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Shops
 */
class Migration_Economy_20131115171126 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `stores` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(60) NOT NULL,
		  `npc_id` int(11) unsigned NOT NULL,
		  `stock_type` enum('restock','steady') NOT NULL,
		  `stock_cap` smallint(3) NOT NULL,
		  `status` enum('closed','open') NOT NULL DEFAULT 'closed',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `store_inventories` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `store_id` int(10) unsigned NOT NULL,
		  `item_id` int(10) unsigned NOT NULL,
		  `price` int(10) unsigned NOT NULL,
		  `stock` smallint(3) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `store_restocks` (
		  `id` int(11) NOT NULL,
		  `store_id` int(10) unsigned NOT NULL,
		  `item_id` int(10) unsigned NOT NULL,
		  `frequency` int(10) unsigned NOT NULL,
		  `next_restock` int(10) unsigned NOT NULL,
		  `min_price` int(10) unsigned NOT NULL,
		  `max_price` int(10) unsigned NOT NULL,
		  `min_amount` smallint(3) unsigned NOT NULL COMMENT 'minimum amount to restock',
		  `max_amount` smallint(3) unsigned NOT NULL COMMENT 'maximum amount to restock',
		  `cap_amount` smallint(3) unsigned NOT NULL COMMENT 'the max amount of this item may be present in the shop'
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `user_shops` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) unsigned NOT NULL,
		  `title` varchar(70) NOT NULL,
		  `description` text NOT NULL,
		  `size` int(11) NOT NULL DEFAULT '0',
		  `till` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `user_shops_user` (`user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

		$db->query(NULL, "ALTER TABLE `user_shops`
		  ADD CONSTRAINT `user_shops_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `stores`;');
		$db->query(NULL, 'DROP TABLE `store_inventories`;');
		$db->query(NULL, 'DROP TABLE `store_restocks`;');
		$db->query(NULL, 'DROP TABLE `user_shops`;');
	}

}
