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

		// Add the DB object under the settings if it doesn't already exist
		if( ! isset($this->current->dbs))
		{
			$this->current->dbs = new stdClass();
		}

	}

	// --------------------------------------------------------------------------

	/**
	 * Save the settings file on close, just to be safe
	 */
	function __destruct()
	{
		file_put_contents(BASE_DIR.'/settings.json', json_encode($this->current));
	}

	// --------------------------------------------------------------------------

	/**
	 * Magic method to simplify isset checking for config options
	 * 
	 * @param string $key
	 * @return $mixed
	 */
	function __get($key)
	{
		return (isset($this->current->{$key})) ? $this->current->{$key} : NULL;
	}

	// --------------------------------------------------------------------------

	/**
	 * Magic method to simplify setting config options
	 * 
	 * @param string $key
	 * @param mixed $val
	 */
	function __set($key, $val)
	{
		//Don't allow direct db config changes
		if($key == "dbs")
		{
			return FALSE;
		}

		$this->current->{$key} = $val;
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a database connection
	 *
	 * @param string $name
	 * @param array $params
	 */
	function add_db($name, $params)
	{
		if(! isset($this->current->dbs->{$name}))
		{
			$this->current->dbs->{$name} = array();
			$this->current->dbs->{$name} = $params;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Remove a database connection
	 * 
	 * @param  string $name
	 */
	function remove_db($name)
	{
		if( ! isset($this->current->dbs->{$name}))
		{
			return FALSE;
		}

		// Remove the db name from the object
		unset($this->current->dbs->{$name});
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Retreive all db connections
	 * 
	 * @return  array 
	 */
	 function get_dbs()
	 {
	 	return $this->current->dbs;
	 }
}
// End of settings.php