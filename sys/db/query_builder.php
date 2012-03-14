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
 *
 * @todo Implement query queue to better match user meaning on queries
 */
class Query_Builder {

	// Compiled query component strings
	private $select_string,
		$from_string,
		$where_string,
		$insert_string,
		$update_string,
		$set_string,
		$order_string,
		$group_string;
		
	// Key value pairs
	private $where_array, 
		$like_array,
		$set_array,
		$set_array_keys,
		$order_array,
		$group_array;
		
	// Query-global components
	private $limit, 
		$offset;
	
	// Alias to $this->db->sql	
	private $sql;
	
	// Query component order mapping
	private $query_map;

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
	// ! Select Queries
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
	 * Creates a Like clause in the sql statement
	 *
	 * @param string $field
	 * @param mixed $val
	 * @param string $pos
	 * @return $this
	 */
	public function like($field, $val, $pos='both')
	{
		$field = $this->db->quote_ident($field);
	
		$this->like_array[$field] = array(
			'value' => $val,
			'pos' => $post
		);
		
		// Add the like string into the order map
		$l = $field. ' LIKE ';
			
		if ($pos == 'before')
		{
			$l .= '%?';
		}
		elseif ($pos == 'after')
		{
			$l .= '?%';
		}
		else
		{
			$l .= '%?%';
		}
		
		$this->query_map[] = array(
			'type' => 'like',
			'conjunction' => (empty($this->query_map)) ? 'WHERE ' : ' AND ',
			'string' => $l,
			'value' => $val
		);
		
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
	 * @param mixed $val
	 * @return $this
	 */
	public function or_where($field, $val=array())
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
	public function where_in($field, $val=array())
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
	public function or_where_in($field, $val=array())
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
	public function where_not_in($field, $val=array())
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
	public function or_where_not_in($field, $val=array())
	{
		// @todo Implement or_where_not_in method
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
	 * Group the results by the selected field(s)
	 *
	 * @param mixed $field
	 * @return $this
	 */
	public function group_by($field)
	{
		if ( ! is_scalar($field))
		{
			$this->group_array = array_map(array($this->db, 'quote_ident'), $field);
		}
		else
		{
			$this->group_array[] = $this->db->quote_ident($field);
		}
		
		$this->group_string = ' GROUP BY '.implode(', ', $this->group_array);
		
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
		// Random case
		if (stripos($type, 'rand') !== FALSE)
		{
			$type = (($rand = $this->sql->random()) !== FALSE ) ? $rand : 'ASC';
		}
	
		// Set fields for later manipulation
		$field = $this->db->quote_ident($field);
		$this->order_array[$field] = $type;
		
		$order_clauses = array();
		
		// Flatten key/val pairs into an array of space-separated pairs
		foreach($this->order_array as $k => $v)
		{
			$order_clauses[] = $k . ' ' . strtoupper($v);
		}
		
		// Set the final string
		$this->order_string = (empty($rand)) 
			? ' ORDER BY '.implode(',', $order_clauses)
			: ' ORDER BY'.$rand;
		
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
	// ! Insert/Update/Delete Queries
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
		// Plain key, value pair
		if (is_scalar($key) && is_scalar($val))
		{
			$this->set_array[$key] = $val;
		}
		// Object or array
		elseif ( ! is_scalar($key))
		{
			foreach($key as $k => $v)
			{
				$this->set_array[$k] = $v;
			}
		}
		
		// Use the keys of the array to make the insert/update string
		// Escape the field names
		$this->set_array_keys = array_map(array($this->db, 'quote_ident'), array_keys($this->set_array));
		
		// Generate the "set" string
		$this->set_string = implode('=?, ', $this->set_array_keys);
		$this->set_string .= '=?';
		
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Creates an insert clause, and executes it
	 *
	 * @param string $table
	 * @param mixed $data
	 * @return mixed
	 */
	public function insert($table, $data=array())
	{
		// No use duplicating logic!
		if ( ! empty($data))
		{
			$this->set($data);
		}
		
		$params = array_values($this->set_array);
		
		$sql = $this->_compile("insert", $table);
		
		return $this->db->prepare_execute($sql, $params);
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates an update clause, and executes it
	 *
	 * @param string $table
	 * @param mixed $data
	 * @return mixed
	 */
	public function update($table, $data=array())
	{
		// No use duplicating logic!
		if ( ! empty($data))
		{
			$this->set($data);
		}
	
		$sql = $this->_compile('update', $table);

		$params = array_values($this->set_array);

		// Do a linear array merge if there is a where string. 
		// We need all the parameters to line up, even when
		// there are placeholders in the where string and 
		// the set string
		if ( ! empty($this->where_string))
		{
			$where_params = array_values($this->where_array);

			foreach($where_params as $w)
			{
				$params[] = $w;
			}
		}

		// Run the query
		return $this->db->prepare_execute($sql, $params);
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Deletes data from a table
	 *
	 * @param string $table
	 * @param mixed $where
	 * @return mixed
	 */
	public function delete($table, $where='')
	{
		// Set the where clause
		if ( ! empty($where))
		{
			$this->where($where);
		}

		// Create the SQL and parameters
		$sql = $this->_compile("delete", $table);
		$params = array_values($this->where_array);

		// Delete the table, and return the result
		return $this->db->prepare_execute($sql, $params);
	}

	// --------------------------------------------------------------------------
	// ! Miscellaneous Methods
	// --------------------------------------------------------------------------
	
	/**
	 * Clear out the class variables, so the next query can be run
	 */
	private function _reset()
	{
		// Only unset class variables that
		// are not callable. Otherwise, we'll 
		// delete class methods!
		foreach($this as $name => $var)
		{
			// Skip properties that are needed for every query
			$save_properties = array(
				'db',
				'sql'
			);
		
			if (in_array($name, $save_properties))
			{
				continue;
			}
		
			// Nothing query-generation related is safe!
			if ( ! is_callable($this->$name))
			{
				unset($this->$name);
			}
		}
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * String together the sql statements for sending to the db
	 *
	 * @param string $type
	 * @param string $table
	 * @return $string
	 */
	private function _compile($type="select", $table="")
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
				
				// Set the group_by string
				if ( ! empty($this->group_string))
				{
					$sql .= $this->group_string;
				}
				
				// Set the order_by string
				if ( ! empty($this->order_string))
				{
					$sql .= $this->order_string;
				}
				
				// Set the limit via the class variables
				if (isset($this->limit) && is_numeric($this->limit))
				{
					$sql = $this->sql->limit($sql, $this->limit, $this->offset);
				}
			break;
			
			case "insert":
				$param_count = count($this->set_array);
				$params = array_fill(0, $param_count, '?');
				$sql = 'INSERT INTO '. $this->db->quote_ident($table) . 
					' (' . implode(', ', $this->set_array_keys) . 
					') VALUES ('.implode(', ', $params).')';
			break;
			
			case "update":
				$sql = 'UPDATE '.$this->db->quote_ident($table). ' SET '. $this->set_string;
				
				if ( ! empty($this->where_string))
				{
					$sql .= $this->where_string;
				}
			break;
			
			case "delete":
				$sql = 'DELETE FROM '.$this->db->quote_ident($table);

				if ( ! empty($this->where_string))
				{
					$sql .= $this->where_string;
				}	

			break;
		}
		
		//echo $sql.'<br />';
		
		return $sql;
	}
}
// End of query_builder.php