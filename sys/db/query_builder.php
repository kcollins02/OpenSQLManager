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

	private $table, 
		$sql, 
		$select_string, 
		$from_string,
		$where_array, 
		$where_string,
		$limit,
		$offset;

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
		// Set the table
		if ( ! empty($table))
		{
			$this->from_string = $this->db->quote_ident($table);
		}

		// Set the limit, if it exists
		if ($limit !== FALSE)
		{
			$this->limit($limit, $offset);
		}
		
		$sql = $this->_compile('select');
		
		//echo $sql."<br />";

		// Do prepared statements for anything involving a "where" clause
		if ( ! empty($this->where_string))
		{
			$result =  $this->db->prepare_execute($sql, array_values($this->where_array));
		}
		else
		{	
			// Otherwise, a simple query will do.
			$result =  $this->db->query($sql);
		}

		// Reset for next query
		$this->_reset();
		
		return $result;
	}

	// --------------------------------------------------------------------------

	/**
	 * Sets values for inserts / updates / deletes
	 *
	 * @param mixed $key
	 * @param mixed $val
	 * @return $this
	 */
	public function set($key, $val)
	{
		// @todo Implement set method
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
		$this->where_string = ' WHERE '.implode(' AND ', $kv_array);
		
		unset($kv_array);
		unset($fields);

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Where clause prefixed with "OR"
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return $this
	 */
	public function or_where($field, $value)
	{
		// @todo Implement or_where method
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Where clause with 'IN' statement
	 *
	 * @param mixed $field
	 * @param mixed $val
	 * @return $this
	 */
	public function where_in($field, $val)
	{
		// @todo Implement Where_in method
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Where in statement prefixed with "or"
	 *
	 * @param string $field
	 * @param mixed $val
	 * @return $this
	 */
	public function or_where_in($field, $val)
	{
		// @todo Implement or_where_in method
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * WHERE NOT IN (FOO) clause
	 *
	 * @param string $field
	 * @param mixed $val
	 * @return $this
	 */
	public function where_not_in($field, $val)
	{
		// @todo Implement where_not_in method
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * OR WHERE NOT IN (FOO) clause
	 * 
	 * @param string $field
	 * @param mixed $val
	 * @return $this
	 */
	public function or_where_not_in($field, $val)
	{
		// @tood Implement or_where_not_in method
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
		// Split identifiers on spaces
		$ident_array = explode(' ', trim($dbname));
		$ident_array = array_map('trim', $ident_array);
		
		// Quote the identifiers 
		$ident_array = array_map(array($this->db, 'quote_ident'), $ident_array);
		
		// Paste it back together
		$this->from_string = implode(' ', $ident_array);		
		
		return $this;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Set a limit on the current sql statement
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	public function limit($limit, $offset=FALSE)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Order the results by the selected field(s)
	 *
	 * @param string $field
	 * @param string $type
	 * @return $this
	 */
	public function order_by($field, $type="")
	{
		// @todo implement order_by method
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates an insert clause, and executes it
	 *
	 * @param string $table
	 * @param mixed $data
	 * @return
	 */
	public function insert($table, $data=array())
	{
		// @todo implement insert method
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates an update clause, and executes it
	 *
	 * @param string $table
	 * @param mixed $data
	 * @return
	 */
	public function update($table, $data=array())
	{
		// @todo implement update method
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Deletes data from a table
	 *
	 * @param string $table
	 * @param mixed $where
	 * @return
	 */
	public function delete($table, $where='')
	{
		// @todo implement delete method
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Clear out the class variables, so the next query can be run
	 */
	private function _reset()
	{
		unset($this->table);
		unset($this->where_array);
		unset($this->where_string);
		unset($this->select_string);
		unset($this->from_string);
		unset($this->limit);
		unset($this->offset);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * String together the sql statements for sending to the db
	 *
	 * @param $type
	 * @return $string
	 */
	private function _compile($type="select")
	{
		$sql = '';
	
		switch($type)
		{
			default:
			case "select":
				$sql = 'SELECT * FROM '.$this->from_string;
				
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
				
				// Set the limit via the class variables
				if (is_numeric($this->limit))
				{
					$sql = $this->sql->limit($sql, $this->limit, $this->offset);
				}
			break;
			
			case "insert":
				// @todo Implement insert statements
			break;
			
			case "update":
				// @todo Implement update statements
			break;
			
			case "delete":
				// @todo Implement delete statements
			break;
		}
		
		return $sql;
	}
}
// End of query_builder.php