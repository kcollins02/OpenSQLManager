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
class ODBC_manip extends ODBC {

	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct("odbc:$dsn", $username, $password, $options);
	}
}
// End of odbc_manip.php