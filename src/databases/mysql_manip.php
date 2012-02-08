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
 class MySQL_manip extends db_manip{

 	/**
 	 * Convienience function for creating a new MySQL table
 	 * 
 	 * @param [type] $name [description]
 	 * @param [type] $columns [description]
 	 * @param array $constraints=array() [description]
 	 * @param array $indexes=array() [description]
 	 * 
 	 * @return [type]
 	 */
	function create_table($name, $columns, $constraints=array(), $indexes=array())
	{
		//TODO: implement
	}
	
	/**
	 * Convience function for droping a MySQL table
	 * 
	 * @param string $name
	 * @return  string
	 */
	function delete_table($name)
	{
		return "DROP TABLE `{$name}`";
	}	
}
//End of mysql_manip.php