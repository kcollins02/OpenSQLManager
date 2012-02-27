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
 * SQLite Database manipulation class
 */
class SQLite_manip extends db_manip {

	/**
	 * Convenience public function to create a new table
	 * 
	 * @param string $name //Name of the table
	 * @param array $columns //columns as straight array and/or column => type pairs
	 * @param array $constraints // column => constraint pairs
	 * @param array $indexes // column => index pairs
	 * @return  string
	 */
	public function create_table($name, $columns, $constraints=array(), $indexes=array())
	{
		$column_array = array();
		
		// Reorganize into an array indexed with column information
		// Eg $column_array[$colname] = array(
		// 		'type' => ...,
		// 		'constraint' => ...,
		// 		'index' => ...,
		// )
		foreach($columns as $colname => $type)
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
			$str .= (isset($props['constraint'])) ? $props['constraint'] : "";

			$columns[] = $str;
		}

		// Generate the sql for the creation of the table
		$sql = "CREATE TABLE \"{$name}\" (";
		$sql .= implode(",", $columns);
		$sql .= ")";

		return $sql;
	}

	/**
	 * SQL to drop the specified table
	 * 
	 * @param string $name
	 * @return string
	 */
	public function delete_table($name)
	{
		return 'DROP TABLE IF EXISTS "'.$name.'"';
	}

	/**
	 * Create an sqlite database file
	 * 
	 * @param  $path
	 */
	public function create_db($path)
	{
		// Create the file if it doesn't exist
		if( ! file_exists($path))
		{
			touch($path);
		}
	}
}
//End of sqlite_manip.php