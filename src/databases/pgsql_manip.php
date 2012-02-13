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
class pgSQL_manip extends db_manip {
	
	function create_table($name, $columns, $constraints=array(), $indexes=array())
	{
		//TODO: implement
	}

	function delete_table($name)
	{
		return <<<SQL
			DROP TABLE "{$name}"
SQL;
	}

}
//End of pgsql_manip.php