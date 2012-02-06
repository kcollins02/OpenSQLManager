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
	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		$options = array_merge(array(

		),
		$options);

		parent::__construct("mysql:$dsn", $username, $password, $options);
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		$this->query("TRUNCATE `{$table}`");
	}

	/**
	 * Get databases for the current connection
	 * 
	 * @return array
	 */
	function get_dbs()
	{
		$res = $this->query("SHOW DATABASES");
		return $this->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Returns the tables available in the current database
	 * 
	 * @return array
	 */
	function get_tables()
	{
		$res = $this->query("SHOW TABLES");
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Return the number of rows affected by the previous query
	 * 
	 * @return int
	 */
	function affected_rows()
	{
		// TODO: Implement
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

class MySQL_manip extends MySQL {

	function __construct($dsn, $user=null, $pass=null, $opt=array())
	{
		parent::__construct($dsn, $user, $pass, $opt);
	}	
}