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
 * Firebird Database class
 * 
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ public functions.
 */
class firebird extends DB_PDO {

	protected $conn, $statement, $trans, $count, $result;
	
	/**
	 * Open the link to the database
	 * 
	 * @param string $db
	 * @param string $user 
	 * @param string $pass
	 */
	public function __construct($dbpath, $user="sysdba", $pass="masterkey")
	{
		$this->conn = ibase_connect($dbpath, $user, $pass);
		
		$class = __CLASS__."_manip";
		$this->manip = new $class;
	}

	/**
	 * Close the link to the database
	 */
	public function __destruct()
	{
		@ibase_close($this->conn);
		@ibase_free_result($this->statement);
	}

	/**
	 * Empty a database table
	 * 
	 * @param string $table
	 */
	public function truncate($table)
	{
		// Firebird lacka a truncate command
		$sql = 'DELETE FROM '.$table.'"';
		$this->query($sql);
	}
	
	/**
	 * Wrapper public function to better match PDO
	 *
	 * @param string $sql
	 * @param  array $params
	 * @return resource
	 */
	public function query($sql)
	{
		$this->count = 0;
		$this->statement = ibase_query($this->conn, $sql);
		return $this->statement;
	}

	/**
	 * Emulate PDO fetch public function
	 * 
	 * @param  int $fetch_style
	 * @return mixed
	 */
	public function fetch($fetch_style=PDO::FETCH_ASSOC)
	{
		switch($fetch_style)
		{
			case PDO::FETCH_OBJ:
				return ibase_fetch_object($this->statement, IBASE_FETCH_BLOBS);
			break;

			case PDO::FETCH_NUM:
				return ibase_fetch_row($this->statement, IBASE_FETCH_BLOBS);
			break;

			default:
				return ibase_fetch_assoc($this->statement, IBASE_FETCH_BLOBS);
			break;
		}
	}

	/**
	 * Emulate PDO fetchAll public function
	 * 
	 * @param  int  $fetch_style
	 * @return mixed
	 */
	public function fetchAll($fetch_style=PDO::FETCH_ASSOC)
	{
		$all = array();

		while($row = $this->fetch($fetch_style))
		{
			$all[] = $row;
		}
		
		$this->result = $all;

		return $all;
	}

	/**
	 * Emulate PDO prepare
	 *
	 * @param string $query
	 * @return resource
	 */
	public function prepare($query)
	{
		$this->statement = ibase_prepare($this->conn, $query);
		return $this->statement;
	}

	/**
	 * List tables for the current database
	 * 
	 * @return array
	 */
	public function get_tables()
	{	
		$sql = <<<SQL
			SELECT "RDB\$RELATION_NAME" FROM "RDB\$RELATIONS" 
			WHERE "RDB\$RELATION_NAME" NOT LIKE 'RDB$%'
			AND "RDB\$RELATION_NAME" NOT LIKE 'MON$%'
SQL;

		$this->statement = $this->query($sql);
		
		$tables = array();
		
		while($row = $this->fetch(PDO::FETCH_ASSOC))
		{
			$tables[] = $row['RDB$RELATION_NAME'];
		}
		
		return $tables;
	}

	/**
	 * List system tables for the current database
	 * 
	 * @return array
	 */
	public function get_system_tables()
	{
		$sql = <<<SQL
			SELECT "RDB\$RELATION_NAME" FROM "RDB\$RELATIONS"
			WHERE "RDB\$RELATION_NAME" LIKE 'RDB$%'
			OR "RDB\$RELATION_NAME" LIKE 'MON$%';
SQL;

		$this->statement = $this->query($sql);

		$tables = array();

		while($row = $this->fetch(PDO::FETCH_ASSOC))
		{
			$tables[] = $row['RDB$RELATION_NAME'];
		}
		
		return $tables;
	}

	/**
	 * Return the number of rows affected by the previous query
	 * 
	 * @return int
	 */
	public function affected_rows()
	{
		return ibase_affected_rows($this->conn);
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	public function num_rows()
	{
		// @todo: Redo this similar to the codeigniter driver
		if(isset($this->result))
		{
			return count($this->result);
		}

		//Fetch all the rows for the result
		$this->result = $this->fetchAll();

		return count($this->result);
	}
	
	/**
	 * Start a database transaction
	 * 
	 * @return bool
	 */
	public function beginTransaction()
	{
		if(($this->trans = ibase_trans($this->conn)) !== NULL)
		{
			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * Commit a database transaction
	 * 
	 * @return bool
	 */
	public function commit()
	{
		return ibase_commit($this->trans);
	}
	
	/**
	 * Rollback a transaction
	 * 
	 * @return bool
	 */
	public function rollBack()
	{
		return ibase_rollback($this->trans);
	}
	
	/**
	 * Run a prepared statement query
	 * 
	 * @param  array $args
	 * @return bool
	 */
	public function execute($args)
	{
		//Add the prepared statement as the first parameter
		array_unshift($args, $this->statement);
		
		// Let php do all the hard stuff in converting 
		// the array of arguments into a list of arguments
		return call_user_func_array('ibase_execute', $args);
	}
	
	/**
	 * Prepare and execute a query
	 *
	 * @param string $sql
	 * @param array $args
	 * @return resource
	 */
	public function prepare_execute($sql, $args)
	{
		$query = $this->prepare($sql);
		
		// Set the statement in the class variable for easy later access
		$this->statement =& $query;
		
		return $this->execute($args);
	}
	
	/**
	 * Bind a prepared query with arguments for executing
	 *
	 * @param string $sql
	 * @param mixed $args
	 * @return FALSE
	 */
	public function prepare_query($sql, $args)
	{
		// You can't bind query statements before execution with
		// the firebird database
		return FALSE;
	}
}
// End of firebird.php