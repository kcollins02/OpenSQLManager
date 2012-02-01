<?php
/**
 * OpenSQLManager
 *
 * Free Database manager for Open Source Databases
 *
 * @author 		Timothy J. Warren
 * @copyright	Copyright (c) 2012
 * @link 		https://github.com/aviat4ion/OpenSQLManager
 * @license 	http://philsturgeon.co.uk/code/dbad-license 
 */

// --------------------------------------------------------------------------

/**
 * Class for manipulating datbase connections, and program settings
 * 
 * Use JSON for compatibility
 */
class Settings {

	protected $current;
	
	/**
	 * Load the settings file
	 */
	function __construct()
	{
		$path = BASE_DIR.'/settings.json'; 

		if( ! is_file($path))
		{
			//Create the file!
			touch($path);
			$this->current = new stdClass();
		}
		else
		{
			$this->current = json_decode(file_get_contents($path));
		}

	}

	// --------------------------------------------------------------------------

	/**
	 * Save the settings file on close, just to be safe
	 */
	function __destruct()
	{
		file_put_contents(json_encode($this->current), BASE_DIR.'/settings.json');
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a database connection
	 * 
	 * @param string $type
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 */
	function add_db($type, $host, $user, $pass)
	{
		
	}
}
// End of settings.php