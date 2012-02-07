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
 * PostgreSQL DB Structure manipulation class
 *
 * @extends PgSQL
 */
class pgSQL_manip extends pgSQL {
	
	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct($dsn, $username, $password, $options);
	}

}
//End of pgsql_manip.php