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
 * Firebird Database class
 * 
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ functions.
 */
class firebird {

	protected $conn, $statement;
	private $esc_char = "''";
	
	/**
	 * Open the link to the database
	 * 
	 * @param string $db
	 * @param string $user 
	 * @param string $pass
	 */
	function __construct($db, $user="sysdba", $pass="masterkey")
	{
		$this->conn = ibase_connect($db, $user, $pass);
	}

	/**
	 * Close the link to the database
	 */
	function __destruct()
	{
		ibase_close($this->conn);
	}

	/**
	 * Empty a database table
	 * 
	 * @param string $table
	 */
	function truncate($table)
	{
		// Firebird lacka a truncate command
		$sql = "DELETE FROM {$table}";
		$this->query($sql);
	}
	
	/**
	 * Wrapper function to better match PDO
	 *
	 * @param string $sql
	 * @return resource
	 */
	function query($sql)
	{
		$this->statement = ibase_query($this->conn, $sql);
		return $this->statement;
	}

	/**
	 * Emulate PDO fetch function
	 * 
	 * @return mixed
	 */
	function fetch()
	{
		//TODO implement
	}

	/**
	 * Emulate PDO fetchAll function
	 * 
	 * @return mixed
	 */
	function fetchAll()
	{
		//TODO implement
	}

	/**
	 * Emulate PDO prepare
	 * 
	 * @return resource
	 */
	function prepare()
	{
		$this->statement = ibase_prepare($this->conn, $query);
		return $this->statement;
	}

	/**
	 * List tables for the current database
	 * 
	 * @return mixed
	 */
	function get_tables()
	{	
		$sql="SELECT rdb\$relation_name FROM rdb\$relations WHERE rdb\$relation_name NOT LIKE 'RDB\$%'";
		$res = $this->query($sql);
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

class firebird_manip extends firebird {
	
	function __construct($db, $user="sysdba", $pass="masterkey")
	{
		parent::__construct($db, $user, $pass);
	}


	
}
// End of firebird.php