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
  * ODBC Database Manipulation class
  *
  * @extends ODBC
  */
class ODBC_manip extends db_manip {

	public function create_table($name, $columns, $constraints=array(), $indexes=array())
	{
		//ODBC can't know how to create a table
		return FALSE;
	}

	public function delete_table($name)
	{
		return "DROP TABLE {$name}";
	}
}
// End of odbc_manip.php