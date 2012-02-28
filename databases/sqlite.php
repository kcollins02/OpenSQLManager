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

	protected $statement;

	/**
	 * Open SQLite Database
	 * 
	 * @param string $dsn 
	 */
	public function __construct($dsn, $user=NULL, $pass=NULL)
	{
		// DSN is simply `sqlite:/path/to/db`
		parent::__construct("sqlite:{$dsn}", $user, $pass);

		$class = __CLASS__."_manip";
		$this->manip = new $class;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	public function truncate($table)
	{
		// SQLite has a TRUNCATE optimization,
		// but no support for the actual command.
		$sql = 'DELETE FROM :table';

		$this->prepare_query($sql, array(
			':table' => $table
		));

		$this->statement->execute();
	}
	
	// --------------------------------------------------------------------------

	/**
	 * List tables for the current database
	 * 
	 * @return mixed
	 */
	public function get_tables()
	{	
		$tables = array();
		$sql = <<<SQL
			SELECT "name", "sql" 
			FROM "sqlite_master" 
			WHERE "type"='table'
SQL;

		$res = $this->query($sql);
		$result = $res->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($result as $r)
		{
			$tables[$r['name']] = $r['sql'];
		}

		return $tables;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * List system tables for the current database
	 * 
	 * @return array
	 */
	public function get_system_tables()
	{
		//SQLite only has the sqlite_master table
		// that is of any importance.
		return array('sqlite_master');
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Load a database for the current connection
	 * 
	 * @param string $db
	 * @param string $name 
	 */
	public function load_database($db, $name)
	{
		$sql = "ATTACH DATABASE '{$db}' AS \"{$name}\"";
		$this->query($sql);
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Unload a database from the current connection
	 * 
	 * @param string $name
	 */
	public function unload_database($name)
	{
		$sql = 'DETACH DATABASE ":name"';
		
		$this->prepare_query($sql, array(
			':name' => $name,
		));

		$this->statement->execute();
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	public function num_rows()
	{
		return (isset($this->statement)) ? $this->statement->rowCount : FALSE;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Create an SQL backup file for the current database's structure
	 *
	 * @return string
	 */
	public function backup_structure()
	{
		// Fairly easy for SQLite...just query the master table
		$sql = 'SELECT "sql" FROM "sqlite_master"';
		$res = $this->query($sql);
		$result = $res->fetchAll(PDO::FETCH_ASSOC);

		$sql_array = array();

		foreach($result as $r)
		{
			$sql_array[] = $r['sql'];
		}

		$sql_structure = implode("\n\n", $sql_array);
		
		return $sql_structure;	
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Create an SQL backup file for the current database's data
	 *
	 * @return string
	 */
	public function backup_data()
	{
		// @todo Implement Backup function
		return '';
	}
}
//End of sqlite.php