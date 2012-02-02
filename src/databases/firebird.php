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
	 
}
// End of firebird.php