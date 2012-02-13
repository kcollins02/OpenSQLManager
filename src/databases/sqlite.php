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
	 * Open SQLite Database
	 * 
	 * @param string $dsn 
	 */
	function __construct($dsn)
	{
		// DSN is simply `sqlite:/path/to/db`
		parent::__construct("sqlite:{$dsn}");

		$class = __CLASS__."_manip";
		$this->manip = new $class;
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
		$sql = <<<SQL
			DELETE FROM "{$table}"
SQL;

		$this->query($sql);
	}

	/**
	 * List tables for the current database
	 * 
	 * @return mixed
	 */
	function get_tables()
	{	
		$tables = array();
		$sql = <<<SQL
			SELECT "name", "sql" FROM "sqlite_master" WHERE "type"='table'
SQL;

		$res = $this->query($sql);
		$result = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $r)
		{
			$tables[$r['name']] = $r['sql'];
		}

		return $tables;
	}

	/**
	 * List system tables for the current database
	 * 
	 * @return array
	 */
	function get_system_tables()
	{
		//SQLite only has the sqlite_master table
		// that is of any importance.
		return array('sqlite_master');
	}

	/**
	 * Load a database for the current connection
	 * 
	 * @param string $db
	 * @param string $name 
	 */
	function load_database($db, $name)
	{
		$sql = <<<SQL
			ATTACH DATABASE '{$db}' AS "{$name}"
SQL;
		$this->query($sql);
	}

	/**
	 * Unload a database from the current connection
	 * 
	 * @param string $name
	 */
	function unload_database($name)
	{
		$sql = <<<SQL
			DETACH DATABASE "{$name}"
SQL;
		$this->query($sql);
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	function num_rows()
	{
		// TODO: Implement
	}
}
//End of sqlite.php