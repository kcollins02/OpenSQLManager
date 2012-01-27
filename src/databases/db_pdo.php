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
 * Base Database class
 *
 * Extends PDO to simplify cross-database issues
 */
class DB_PDO extends PDO {

	function __construct($dsn, $username=NULL, $password=NULL, $driver_options=array())
	{
		parent::__construct($dsn, $username, $password, $driver_options);
	}

}

// End of db_pdo.php