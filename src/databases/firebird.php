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

// Test for support
if( ! function_exists('ibase_connect'))
{
	return FALSE;
}

/**
 * Firebird Database class
 * 
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ functions.
 */
class firebird {

	protected $conn;
	
	/**
	 * Open the link to the database
	 * 
	 * @param string $db
	 * @param string $user 
	 * @param string $pass
	 */
	function __construct($db, $user, $pass)
	{
		$this->conn = @ibase_connect($db, $user, $pass);
	}

	/**
	 * Close the link to the database
	 */
	function __destruct()
	{
		@ibase_close($this->conn);
	}
}
// End of firebird.php