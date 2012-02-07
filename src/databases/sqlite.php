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
 * SQLite specific class 
 *
 * @extends DB_PDO
 */
class SQLite extends DB_PDO {

	/**
	 * Open SQLite Database
	 * 
	 * @param string $dsn 
	 */
	function __construct($dsn)
	{
		// DSN is simply `sqlite:/path/to/db`
		parent::__construct("sqlite:{$dsn}");
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		// SQLite has a TRUNCATE optimization,
		// but no support for the actual command.
		$sql = "DELETE FROM {$table}";
		$this->query($sql);
	}

	/**
	 * List tables for the current database
	 * 
	 * @return mixed
	 */
	function get_tables()
	{	
		$res = $this->query("SELECT name FROM sqlite_master WHERE type='table'");
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	function num_rows()
	{
		// TODO: Implement
	}
}

/**
 * Database manipulation class
 */
class SQLite_manip extends SQLite {
	
	function __construct($dsn)
	{
		parent::__construct($dsn);
	}

	/**
	 * Convenience function to create a new table
	 * 
	 * @param string $name //Name of the table
	 * @param array $columns //columns as straight array and/or column => type pairs
	 * @param array $constraints // column => constraint pairs
	 * @param array $indexes // column => index pairs
	 * @return  string
	 */
	function create_table($name, $columns, $constraints, $indexes)
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

		if( ! empty($indexes))
		{
			foreach($indexes as $col => $ind)
			{
				$column_array[$col]['index'] = $ind;
			}
		}

		// Generate the sql for the creation of the table
		$sql = "CREATE TABLE {$name} (";
		$sql .= ")";
	}

	/**
	 * Create an sqlite database file
	 * 
	 * @param  $path
	 */
	function create_db($path)
	{
		// Create the file if it doesn't exist
		if( ! file_exists($path))
		{
			touch($path);
		}
	}
}