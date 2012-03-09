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
 * Convienience class for creating sql queries - also the class that 
 * instantiates the specific db driver
 */
class Query_Builder {

	private $table, $where_array, $sql, $select_string, $where_string;

	/**
	 * Constructor
	 * 
	 * @param object $conn_name - the name of the connection/parameters
	 */
	public function __construct($params)
	{
		$params->type = strtolower($params->type);
		$dbtype = ($params->type !== 'postgresql') ? $params->type : 'pgsql';

		// Initiate the constructor for the selected database
		switch($dbtype)
		{
			default:
				$this->db = new $dbtype("host={$params->host};port={$params->port};", $params->user, $params->pass);
			break;

			case "sqlite":
				if ( ! empty($params->user) &&  ! empty($params->pass))
				{
					$this->db = new $dbtype($params->file, $params->user, $params->pass);
				}
				else
				{
					$this->db = new $dbtype($params->file);
				}
			break;

			case "firebird":
				$this->db = new $dbtype("{$params->host}:{$params->file}", $params->user, $params->pass);
			break;
		}
		
		// Make things just slightly shorter
		$this->sql =& $this->db->sql;
	}

	// --------------------------------------------------------------------------

	/**
	 * Select and retrieve all records from the current table, and/or
	 * execute current compiled query
	 *
	 * @param $table
	 * @param int $limit
	 * @param int $offset
	 * @return object
	 */
	public function get($table='', $limit=FALSE, $offset=FALSE)
	{
		// @todo Only add in the table name when using the select method
		// @todo Only execute combined query when using other query methods and empty parameters
	
		$sql = 'SELECT * FROM ' . $this->db->quote_ident($table);

		if ( ! empty($table) && $limit === FALSE && $offset === FALSE)
		{
			$result = $this->db->query($sql);
		}
		
		// Set the select string
		if ( ! empty($this->select_string))
		{
			// Replace the star with the selected fields
			$sql = str_replace('*', $this->select_string, $sql);
		}

		// Set the where string
		if ( ! empty($this->where_string))
		{
			$sql .= $this->where_string;
		}
		
		// Set the limit, if it exists
		if ($limit !== FALSE)
		{
			$sql = $this->sql->limit($sql, $limit, $offset);
		}
		
		echo $sql."<br />";

		// Do prepared statements for anything involving a "where" clause
		if ( ! empty($this->where_string))
		{
			return $this->db->prepare_execute($sql, array_values($this->where_array));
		}

		// Otherwise, a simple query will do.
		return $this->db->query($sql);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Specifies rows to select in a query
	 *
	 * @param string $fields
	 * @return $this
	 */
	public function select($fields)
	{
		// Split fields by comma
		$fields_array = explode(",", $fields);
		$fields_array = array_map('trim', $fields_array);

		// Split on 'As'
		foreach ($fields_array as $key => $field)
		{
			if (stripos($field, 'as') !== FALSE)
			{
				$fields_array[$key] = preg_split('`as`i', $field);
				$fields_array[$key] = array_map('trim', $fields_array[$key]);
			}
		}

		// Quote the identifiers
		$safe_array = array_map(array($this->db, 'quote_ident'), $fields_array);
		
		unset($fields_array);

		// Join the strings back together
		for($i = 0, $c = count($safe_array); $i < $c; $i++)
		{
			if (is_array($safe_array[$i]))
			{
				$safe_array[$i] = implode(' AS ', $safe_array[$i]);
			}
		}

		$this->select_string = implode(', ', $safe_array);
		
		unset($safe_array);

		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Specify condition(s) in the where clause of a query
	 * Note: this function works with key / value, or a 
	 * passed array with key / value pairs
	 * 
	 * @param mixed $key 
	 * @param mixed $val
	 * @return $this
	 */
	public function where($key, $val=array())
	{
		// Key and value passed? Add them to the where array
		if (is_scalar($key) && is_scalar($val))
		{
			$this->where_array[$key] = $val;
		}
		// Array or object, loop through and add to the where array
		elseif ( ! is_scalar($key))
		{
			foreach($key as $k => $v)
			{
				$this->where_array[$k] = $v;
			}
		}

		// The values are irrelevant until the query is actually run
		$fields = array_keys($this->where_array);
		
		// Array of conditions
		$kv_array = array();

		// Create key/value placeholders
		foreach($fields as $f)
		{
			// Split each key by spaces, incase there
			// is an operator such as >, <, !=, etc.
			$f_array = explode(' ', trim($f));

			// Simple key = val
			if (count($f_array) === 1)
			{
				$kv_array[] = $this->db->quote_ident($f_array[0]) . '= ?';
			}
			else // Other operators
			{
				$kv_array[] = $this->db->quote_ident($f_array[0]) . " {$f_array[1]} ?";
			}
		}

		// Create the where portion of the string
		$this->where_string = ' WHERE '.implode(', ', $kv_array);
		
		unset($kv_array);
		unset($fields);

		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Creates a join phrase in a compiled query
	 *
	 * @param string $table
	 * @param string $condition
	 * @param string $type
	 * @return $this
	 */
	public function join($table, $condition, $type='inner')
	{
		// @todo Implement join method
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Specify the database table to select from
	 *
	 * @param string $dbname
	 * @return $this
	 */
	public function from($dbname)
	{
		// @todo Implement from method
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * String together the sql statements for sending to the db
	 *
	 * @return $string
	 */
	private function _compile()
	{
		// @todo Implement _compile method
	}
}