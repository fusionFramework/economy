<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * NPC helper
 *
 * A collection of useful functions that relate to items.
 *
 * @package    fusionFramework/economy
 * @author     Maxim Kerstens
 * @copyright  (c) happydemon.org
 */
abstract class NPC {

	/**
	 * Define messages the NPC would use in the game
	 * @var false|array
	 */
	public $messages = false;

	/**
	 * Can we define multiple messages per type in the admin?
	 * @var bool
	 */
	public $multi_msg = true;

	/**
	 * If the NPC will have an image define the folder's name, otherwise, false.
	 * @var false|string
	 */
	public $image = false;

	/**
	 * @param $type
	 *
	 * @return NPC
	 * @throws Kohana_Exception
	 */
	public static function factory($type)
	{
		$class = 'NPC_'.ucfirst($type);

		if(class_exists($class))
			return new $class();
		else
			throw new Kohana_Exception('No ":type" NPC found.', [':type' => $type]);
	}

	public static function list_types()
	{
		static $types = NULL;

		if ($types == NULL)
		{
			// Include paths must be searched in reverse
			$paths = array_reverse(Kohana::list_files('classes'.DIRECTORY_SEPARATOR.'NPC'));

			// Array of class names that have been found
			$found = array();

			foreach ($paths as $files)
			{
				$replacements = array_merge(Kohana::include_paths(), array('classes' . DIRECTORY_SEPARATOR . 'NPC' . DIRECTORY_SEPARATOR, '.php'));

				foreach ((array)$files as $file)
				{
					foreach ($replacements as $replace)
					{
						$file = str_replace($replace, '', $file);
					}

					$found[strtolower($file)] = $file;
				}
			}
			$types = $found;
		}

		return $types;
	}

	public $type = null;

	public function __construct()
	{
		$this->type = str_replace('NPC_', '', get_class($this));
	}
}
