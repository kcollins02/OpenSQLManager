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
 * SQLite specific class 
 *
 * @extends DB_PDO
 */
class SQLite extends DB_PDO {

	/**
	 * Static function to simply creating dsn for the current database driver
	 * 
	 * @return SQLite object
	 */
	static function connect()
	{
		
	}

	/**
	 * Open SQLite Database
	 * 
	 * @param string $dsn 
	 */
	function __construct($dsn)
	{
		// DSN is simply `sqlite:/path/to/db`
		parent::__construct("sqlite:{$dsn}");
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		// SQLite has a TRUNCATE optimization,
		// but no support for the actual command.
		$sql = "DELETE FROM {$table}";
		$this->query($sql);
	}

	/**
	 * Create an sqlite database file
	 * 
	 * @param  $path
	 */
	function create_db($path)
	{
		// Create the file if it doesn't exist
		if( ! file_exists($path))
		{
			touch($path);
		}
	}
}