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
class DB_PDO extends PDO {

	protected $statement;

	function __construct($dsn, $username=NULL, $password=NULL, $driver_options=array())
	{
		parent::__construct($dsn, $username, $password, $driver_options);
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

}

// End of db_pdo.php