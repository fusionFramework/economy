<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Items
 */
class Migration_Economy_20131114171126 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `items` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `type_id` int(11) unsigned NOT NULL,
		  `name` varchar(50) NOT NULL,
		  `description` text NOT NULL,
		  `image` varchar(200) NOT NULL,
		  `status` enum('draft','released','retired') NOT NULL DEFAULT 'draft',
		  `unique` tinyint(1) NOT NULL,
		  `transferable` tinyint(1) NOT NULL,
		  `commands` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `item_types` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL,
		  `action` varchar(200) NOT NULL,
		  `default_command` varchar(100) NOT NULL,
		  `img_dir` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `user_items` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `item_id` int(11) unsigned NOT NULL,
		  `user_id` int(11) unsigned NOT NULL,
		  `amount` int(11) NOT NULL,
		  `location` varchar(60) NOT NULL,
		  `parameter` varchar(255) NOT NULL COMMENT 'e.g. when location is usershop this would contain its price',
		  `parameter_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `k_item` (`item_id`),
		  KEY `k_user` (`user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;");

		$db->query(NULL, "ALTER TABLE `user_items`
		  ADD CONSTRAINT `user_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
		  ADD CONSTRAINT `user_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `items`;');
		$db->query(NULL, 'DROP TABLE `item_types`;');
		$db->query(NULL, 'DROP TABLE `user_items`;');
	}

}
