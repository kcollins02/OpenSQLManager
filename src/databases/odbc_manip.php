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

	function create_table()
	{
		//ODBC can't know how to create a table
		return FALSE;
	}
}
// End of odbc_manip.php