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
 * Firebird Database Manipulation class
 * 
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ functions.
 */
class firebird_manip extends firebird {
	
	function __construct($db, $user="sysdba", $pass="masterkey")
	{
		parent::__construct($db, $user, $pass);
	}

	function create_table($name, $fields, $constraints=array())
	{
		$sql = "CREATE TABLE {$name}";
	}
	
}
//End of firebird_manip.php