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
  * ODBC Database Driver
  *
  * For general database access for databases not specified by the main drivers
  *
  * @extends DB_PDO
  */
class ODBC extends DB_PDO {

	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct($dsn, $username, $password, $options);
	}

}

// End of odbc.php