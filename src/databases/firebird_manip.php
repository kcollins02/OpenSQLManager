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
 * Firebird Database Manipulation class
 * 
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ functions.
 */
class firebird_manip extends db_manip{

	/**
	 * Convienience function to generate sql for creating a db table
	 * 
	 * @param string $name 
	 * @param array $fields
	 * @param array $constraints=array()
	 * @param array $indexes=array()
	 * 
	 * @return string
	 */
	function create_table($name, $fields, $constraints=array(), $indexes=array())
	{
		$column_array = array();
		
		// Reorganize into an array indexed with column information
		// Eg $column_array[$colname] = array(
		// 		'type' => ...,
		// 		'constraint' => ...,
		// 		'index' => ...,
		// )
		foreach($fields as $colname => $type)
		{
			if(is_numeric($colname))
			{
				$colname = $type;
			}

			$column_array[$colname] = array();
			$column_array[$colname]['type'] = ($type !== $colname) ? $type : '';
		}

		if( ! empty($constraints))
		{
			foreach($constraints as $col => $const)
			{
				$column_array[$col]['constraint'] = $const;
			}
		}

		// Join column definitons together 
		$columns = array();
		foreach($column_array as $n => $props)
		{
			$str = "{$n} ";
			$str .= (isset($props['type'])) ? "{$props['type']} " : "";
			$str .= (isset($props['constraint'])) ? "{$props['constraint']} " : "";

			$columns[] = $str;
		}

		// Generate the sql for the creation of the table
		$sql = "CREATE TABLE \"{$name}\" (";
		$sql .= implode(",", $columns);
		$sql .= ")";

		return $sql;
	}

	/**
	 * Drop the selected table
	 * 
	 * @param string $name
	 * @return string
	 */
	function delete_table($name)
	{
		return "DROP TABLE \"{$name}\"";
	}
	
}
//End of firebird_manip.php