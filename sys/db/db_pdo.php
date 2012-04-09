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
 *
 * @abstract
 */
abstract class DB_PDO extends PDO {

	public $manip;
	protected $statement;

	/**
	 * PDO constructor wrapper
	 */
	public function __construct($dsn, $username=NULL, $password=NULL, $driver_options=array())
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
	public function prepare_query($sql, $data)
	{
		// Prepare the sql
		$query = $this->prepare($sql);

		if( ! (is_object($query) || is_resource($query)))
		{
			$this->get_last_error();
			return FALSE;
		}

		// Set the statement in the class variable for easy later access
		$this->statement =& $query;


		if( ! (is_array($data) || is_object($data)))
		{
			trigger_error("Invalid data argument");
			return FALSE;
		}

		// Bind the parameters
		foreach($data as $k => $value)
		{
			if(is_numeric($k))
			{
				$k++;
			}

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
	 * Create and execute a prepared statement with the provided parameters
	 *
	 * @param string $sql
	 * @param array $params
	 * @return PDOStatement
	 */
	public function prepare_execute($sql, $params)
	{
		$this->statement = $this->prepare_query($sql, $params);
		$this->statement->execute();

		return $this->statement;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retreives the data from a select query
	 *
	 * @param PDOStatement $statement
	 * @return array
	 */
	public function get_query_data($statement)
	{
		$this->statement =& $statement;

		// Execute the query
		$this->statement->execute();

		// Return the data array fetched
		return $this->statement->fetchAll(PDO::FETCH_ASSOC);
	}

	// -------------------------------------------------------------------------

	/**
	 * Returns number of rows affected by an INSERT, UPDATE, DELETE type query
	 *
	 * @param PDOStatement $statement
	 * @return int
	 */
	public function affected_rows($statement='')
	{
		if ( ! empty($statement))
		{
			$this->statement = $statement;
		}

		// Return number of rows affected
		return $this->statement->rowCount();
	}

	// --------------------------------------------------------------------------

	/**
	 * Return the last error for the current database connection
	 *
	 * @return string
	 */
	public function get_last_error()
	{
		$info = $this->errorInfo();

		echo "Error: <pre>{$info[0]}:{$info[1]}\n{$info[2]}</pre>";
	}

	// --------------------------------------------------------------------------

	/**
	 * Surrounds the string with the databases identifier escape characters
	 *
	 * @param mixed $ident
	 * @return string
	 */
	public function quote_ident($ident)
	{
		if (is_array($ident))
		{
			return array_map(array($this, 'quote_ident'), $ident);
		}

		// Split each identifier by the period
		$hiers = explode('.', $ident);

		return '"'.implode('"."', $hiers).'"';
	}

	// -------------------------------------------------------------------------

	/**
	 * Deletes all the rows from a table. Does the same as the truncate
	 * method if the database does not support 'TRUNCATE';
	 *
	 * @param string $table
	 * @return mixed
	 */
	public function empty_table($table)
	{
		$sql = 'DELETE FROM '.$this->quote_ident($table);

		return $this->query($sql);
	}

	// -------------------------------------------------------------------------

	/**
	 * Return schemas for databases that list them
	 *
	 * @return array
	 */
	public function get_schemas()
	{
		return FALSE;
	}

	// -------------------------------------------------------------------------

	/**
	 * Method to simplify retreiving db results for meta-data queries
	 *
	 * @param string $sql
	 * @param bool $filtered_index
	 * @return mixed
	 */
	protected function driver_query($sql, $filtered_index=TRUE)
	{
		if ($sql === FALSE)
		{
			return FALSE;
		}
	
		$res = $this->query($sql);
		
		$flag = ($filtered_index) ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;
		$all = $res->fetchAll($flag);
		
		return ($filtered_index) ? db_filter($all, 0) : $all;
	}
	
	// -------------------------------------------------------------------------
	
	/**
	 * Return list of tables for the current database
	 *
	 * @return array
	 */
	public function get_tables()
	{
		return $this->driver_query($this->sql->table_list());
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of dbs for the current connection, if possible
	 *
	 * @return array
	 */
	public function get_dbs()
	{
		return $this->driver_query($this->sql->db_list());
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of views for the current database
	 *
	 * @return array
	 */
	public function get_views()
	{
		return $this->driver_query($this->sql->view_list());
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of sequences for the current database, if they exist
	 *
	 * @return array
	 */
	public function get_sequences()
	{
		return $this->driver_query($this->sql->sequence_list());
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of function for the current database
	 *
	 * @return array
	 */
	public function get_functions()
	{
		return $this->driver_query($this->sql->function_list(), FALSE);
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of stored procedures for the current database
	 *
	 * @return array
	 */
	public function get_procedures()
	{
		return $this->driver_query($this->sql->procedure_list(), FALSE);
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Return list of triggers for the current database
	 *
	 * @return array
	 */
	public function get_triggers()
	{
		return $this->driver_query($this->sql->trigger_list(), FALSE);
	}
	
	// -------------------------------------------------------------------------
	
	/**
	 * Retreives an array of non-user-created tables for
	 * the connection/database
	 *
	 * @return array
	 */
	public function get_system_tables()
	{
		return $this->driver_query($this->sql->system_table_list());
	}


	// -------------------------------------------------------------------------
	// ! Abstract public functions to override in child classes
	// -------------------------------------------------------------------------

	/**
	 * Empty the passed table
	 *
	 * @param string $table
	 *
	 * @return void
	 */
	abstract public function truncate($table);

	/**
	 * Return the number of rows for the last SELECT query
	 *
	 * @return int
	 */
	abstract public function num_rows();

	/**
	 * Connect to a different database
	 *
	 * @param string $name
	 * @return void
	 */
	abstract public function switch_db($name);
}
// End of db_pdo.php