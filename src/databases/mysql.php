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
  * MySQL specific class
  *
  * @extends DB_PDO
  */
class MySQL extends DB_PDO {

	/**
	 * Connect to MySQL Database
	 * 
	 * @param string $dsn
	 * @param string $username=null
	 * @param string $password=null
	 * @param array $options=array()
	 */
	public function __construct($dsn, $username=null, $password=null, $options=array())
	{
		$options = array_merge(array(
			PDO::MYSQL_ATTR_FOUND_ROWS => true
		),
		$options);

		parent::__construct("mysql:$dsn", $username, $password, $options);

		$class = __CLASS__.'_manip';
		$this->manip = new $class;
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	public function truncate($table)
	{
		$this->query("TRUNCATE `{$table}`");
	}

	/**
	 * Get databases for the current connection
	 * 
	 * @return array
	 */
	public function get_dbs()
	{
		$res = $this->query("SHOW DATABASES");
		return $this->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Returns the tables available in the current database
	 * 
	 * @return array
	 */
	public function get_tables()
	{
		$res = $this->query("SHOW TABLES");
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Returns system tables for the current database
	 * 
	 * @return array
	 */
	public function get_system_tables()
	{
		//MySQL doesn't have system tables
		return array();
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	public function num_rows()
	{
		return isset($this->statement) ? $this->statement->rowCount() : FALSE;
	}
}
//End of mysql.php