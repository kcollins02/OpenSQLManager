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

	/**
	 * Constructor
	 * 
	 * @param string $conn_name - the name of the connection
	 */
	function __construct($conn_name)
	{
		$this->settings =& Settings::get_instance();

		$params = $this->settings->get_db($conn_name);

		$params->type = strtolower($params->type);
		$dbtype = ($params->type !== 'postgresql') ? $params->type : 'pgsql';

		// Initiate the constructor for the 
		switch($dbtype)
		{
			default:
				$this->db = new $dbtype("host={$params->host};port={$params->port};", $params->user, $params->pass);
			break;

			case "sqlite":
				$this->db = new $dbtype($params->file, $params->user, $params->pass);
			break;

			case "firebird":
				$this->db = new $dbtype("{$params->host}:{$params->file}", $params->user, $params->pass);
			break;
		}
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
		if (is_callable($this->$db->$name))
		{
			return call_user_func_array(array(&$this->db, $name), $params);
		}
		else
		{
			return NULL;
		}
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
}