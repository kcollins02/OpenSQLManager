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
  * MySQL Database manipulation class 
  */
 class MySQL_manip extends MySQL {

	function __construct($dsn, $user=null, $pass=null, $opt=array())
	{
		parent::__construct($dsn, $user, $pass, $opt);
	}	
}
//End of mysqlL_manip.php