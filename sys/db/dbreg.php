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
 * Connection registry
 *
 * Decouples the Settings class from the query builder
 * and organizes database connections
 */
class DB_Reg {

	private static $instance;

	/**
	 * Registry access method
	 *
	 * @param string $key
	 * @return object
	 */
	public static function &get_db($key)
	{
		if ( ! isset(self::$instance[$key]))
		{
			// The constructor sets the instance
			new DBReg($key);
		}

		return self::$instance[$key];
	}

	// --------------------------------------------------------------------------

	/**
	 * Private constructor
	 *
	 * @param string $key
	 */
	private function __construct($key)
	{
		// Get the db connection parameters for the current database
		$db_params = Settings::get_instance()->get_db($key);

		// Set the current key in the registry
		self::$instance[$key] = new Query_Builder($db_params);
	}

	// --------------------------------------------------------------------------

	/**
	 * Return exiting connections
	 *
	 * @return array
	 */
	public static function get_connections()
	{
		return array_keys(self::$instance);
	}
}
// End of dbreg.php