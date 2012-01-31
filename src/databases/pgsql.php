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
if( ! in_array('pgsql', pdo_drivers()))
{
	return FALSE;
}

/**
 * PostgreSQL specifc class
 *
 * @extends DB_PDO
 */
class pgSQL extends DB_PDO {

	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct($dsn, $username, $password, $options);
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		
	}

}