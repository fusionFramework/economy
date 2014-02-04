<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Items
 */
class Migration_Economy_20131216171126 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "CREATE TABLE IF NOT EXISTS `npcs` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(60) NOT NULL,
		  `type` varchar(30) NOT NULL,
		  `image` varchar(70) NOT NULL,
		  `messages` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");

		$db->query(NULL, "ALTER TABLE `stores`
		  ADD CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `npcs`;');
	}

}
