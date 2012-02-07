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
	function __construct($dbpath, $user="sysdba", $pass="masterkey")
	{
		$this->conn = ibase_connect($dbpath, $user, $pass);
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
	 * @param  int $fetch_style
	 * @return mixed
	 */
	function fetch($fetch_style=PDO::FETCH_ASSOC)
	{
		switch($fetch_style)
		{
			case PDO::FETCH_OBJ:
				return ibase_fetch_object($this->statement);
			break;

			case PDO::FETCH_NUM:
				return ibase_fetch_row($this->statement);
			break;

			case PDO::FETCH_BOTH:
				return array_merge(
					ibase_fetch_row($this->statement),
					ibase_fetch_assoc($this->statement)
				);
			break;

			default:
				return ibase_fetch_assoc($this->statement);
			break;
		}
	}

	/**
	 * Emulate PDO fetchAll function
	 * 
	 * @param  int  $fetch_style
	 * @return mixed
	 */
	function fetchAll($fetch_style=PDO::FETCH_ASSOC)
	{
		$all = array();

		while($row = $this->fetch($fetch_style))
		{
			$all[] = $row;
		}

		return $all;
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
		$this->statement = $this->query($sql);
		
		return $this->fetch(PDO::FETCH_NUM);
	}

	/**
	 * Return the number of rows affected by the previous query
	 * 
	 * @return int
	 */
	function affected_rows()
	{
		return ibase_affected_rows($this->conn);
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
// End of firebird.php