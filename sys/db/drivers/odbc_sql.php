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
  * ODBC SQL Class
  */
class ODBC_SQL extends DB_SQL {

	public function create_table($name, $columns, array $constraints=array(), array $indexes=array())
	{
		//ODBC can't know how to create a table
		return FALSE;
	}

	public function delete_table($name)
	{
		return "DROP TABLE {$name}";
	}

	/**
	 * Limit clause
	 *
	 * @param string $sql
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	public function limit($sql, $limit, $offset=FALSE)
	{
		
	}
}
// End of odbc_sql.php