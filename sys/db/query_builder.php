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

	private $table;

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
	}

	// --------------------------------------------------------------------------

	/**
	 * Shortcut to directly access database class properties
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->db->$key))
		{
			return $this->db->$key;
		}

		return NULL;
	}

	// --------------------------------------------------------------------------

	/**
	 * Shortcut to directly call database methods
	 *
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function __call($name, $params)
	{
		if (isset($this->db->$name))
		{
			if (is_callable($this->db->$name))
			{
				return call_user_func_array($this->db->$name, $params);
			}
		}

		return NULL;
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
		if ( ! empty($table) && $limit === FALSE && $offset === FALSE)
		{
			return $this->query('SELECT * FROM ' . $this->quote_ident($table));
		}
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
		// @todo Implement select method
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
		// @todo Implement where method
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
}