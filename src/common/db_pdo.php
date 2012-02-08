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
 * Base Database class
 *
 * Extends PDO to simplify cross-database issues
 */
abstract class DB_PDO extends PDO {

	public $manip;

	protected $statement;

	function __construct($dsn, $username=NULL, $password=NULL, $driver_options=array())
	{
		parent::__construct($dsn, $username, $password, $driver_options);

		if(__CLASS__ !== "DB_PDO")
		{
			$class = __CLASS__.'_manip';
			$this->manip = new $class;
		}
	}
	
	// -------------------------------------------------------------------------
	
	/**
	 * Simplifies prepared statements for database queries
	 *
	 * @param string $sql
	 * @param array $data
	 * @return mixed PDOStatement / FALSE
	 */
	function prepare_query($sql, $data)
	{
		// Prepare the sql
		$query = $this->prepare($sql);
		
		if( ! is_like_array($query))
		{
			$this->get_last_error();
			return FALSE;
		}
		
		// Set the statement in the class variable for easy later access
		$this->statement =& $query;
		
		
		if( ! is_like_array($data))
		{
			trigger_error("Invalid data argument");
			return FALSE;
		}
		
		// Bind the parameters
		foreach($data as $k => $value)
		{
			$res = $query->bindValue($k, $value);
			
			if( ! $res)
			{
				trigger_error("Parameter not successfully bound");
				return FALSE;
			}
		}
		
		return $query; 
		
	}

	// -------------------------------------------------------------------------

	/**
	 * Retreives the data from a select query
	 *
	 * @param PDOStatement $statement
	 * @return array
	 */
	function get_query_data($statement)
	{
		// Execute the query
		$statement->execute();

		// Return the data array fetched
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	// -------------------------------------------------------------------------

	/**
	 * Returns number of rows affected by an INSERT, UPDATE, DELETE type query
	 *
	 * @param PDOStatement $statement
	 * @return int
	 */
	function affected_rows($statement)
	{
		// Execute the query
		$statement->execute();

		// Return number of rows affected
		return $statement->rowCount();
	}

	// -------------------------------------------------------------------------

	/**
	 * Abstract functions to override in child classes
	 */
	
	/**
	 * Return list of tables for the current database
	 * 
	 * @return array
	 */
	abstract function get_tables();

	/**
	 * Empty the passed table
	 * 
	 * @param string $table
	 * 
	 * @return void
	 */
	abstract function truncate($table);

	/**
	 * Return the number of rows for the last SELECT query
	 * 
	 * @return int
	 */
	abstract function num_rows();
}

// -------------------------------------------------------------------------

/**
 * Abstract parent for database manipulation subclasses
 */
abstract class db_manip {
	
	/**
	 * Get database-specific sql to create a new table
	 * 
	 * @param string $name 
	 * @param array $columns 
	 * @param array $constraints 
	 * @param array $indexes 
	 * 
	 * @return string
	 */
	abstract function create_table($name, $columns, $constraints=array(), $indexes=array());

	/**
	 * Get database-specific sql to drop a table
	 * 
	 * @param string $name
	 * 
	 * @return string
	 */
	abstract function delete_table($name);
}
// End of db_pdo.php