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
 * PostgreSQL specifc SQL
 */
class pgSQL_SQL extends DB_SQL {

	public function create_table($name, $columns, array $constraints=array(), array $indexes=array())
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
		$sql .= implode(", ", $columns);
		$sql .= ")";

		return $sql;
	}

	// --------------------------------------------------------------------------

	public function delete_table($name)
	{
		return 'DROP TABLE "'.$name.'"';
	}

	// --------------------------------------------------------------------------

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
		$sql .= " LIMIT {$limit}";

		if(is_numeric($offset))
		{
			$sql .= " OFFSET {$offset}";
		}

		return $sql;
	}

	// --------------------------------------------------------------------------

	/**
	 * Random ordering keyword
	 *
	 * @return string
	 */
	public function random()
	{
		return ' RANDOM()';
	}

	// --------------------------------------------------------------------------

	/**
	 * Create an SQL backup file for the current database's structure
	 *
	 * @return string
	 */
	public function backup_structure()
	{
		// @todo Implement Backup function
		return '';
	}

	// --------------------------------------------------------------------------

	/**
	 * Create an SQL backup file for the current database's data
	 *
	 * @return string
	 */
	public function backup_data()
	{
		// @todo Implement Backup function
		return '';
	}

}
//End of pgsql_manip.php