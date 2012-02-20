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
 * PDO-firebird isn't stable, so this is a wrapper of the ibase_ functions.
 */
class firebird {

	protected $conn, $statement, $trans;
	private $esc_char = "''";
	
	/**
	 * Open the link to the database
	 * 
	 * @param string $db
	 * @param string $user 
	 * @param string $pass
	 */
	function __construct($dbpath, $user="sysdba", $pass="masterkey")
	{
		$this->conn = ibase_connect($dbpath, $user, $pass);
		
		$class = __CLASS__."_manip";
		$this->manip = new $class;
	}

	/**
	 * Close the link to the database
	 */
	function __destruct()
	{
		@ibase_close($this->conn);
		@ibase_free_result($this->statement);
	}

	/**
	 * Empty a database table
	 * 
	 * @param string $table
	 */
	function truncate($table)
	{
		// Firebird lacka a truncate command
		$sql = 'DELETE FROM '.$table.'"';
		$this->query($sql);
	}
	
	/**
	 * Wrapper function to better match PDO
	 *
	 * @param string $sql
	 * @return resource
	 */
	function query($sql)
	{
		$this->statement = ibase_query($this->conn, $sql);
		return $this->statement;
	}

	/**
	 * Emulate PDO fetch function
	 * 
	 * @param  int $fetch_style
	 * @return mixed
	 */
	function fetch($fetch_style=PDO::FETCH_ASSOC)
	{
		switch($fetch_style)
		{
			case PDO::FETCH_OBJ:
				return ibase_fetch_object($this->statement);
			break;

			case PDO::FETCH_NUM:
				return ibase_fetch_row($this->statement);
			break;

			case PDO::FETCH_BOTH:
				return array_merge(
					ibase_fetch_row($this->statement),
					ibase_fetch_assoc($this->statement)
				);
			break;

			default:
				return ibase_fetch_assoc($this->statement);
			break;
		}
	}

	/**
	 * Emulate PDO fetchAll function
	 * 
	 * @param  int  $fetch_style
	 * @return mixed
	 */
	function fetchAll($fetch_style=PDO::FETCH_ASSOC)
	{
		$all = array();

		while($row = $this->fetch($fetch_style))
		{
			$all[] = $row;
		}

		return $all;
	}

	/**
	 * Emulate PDO prepare
	 *
	 * @return resource
	 */
	function prepare()
	{
		$this->statement = ibase_prepare($this->conn, $query);
		return $this->statement;
	}

	/**
	 * List tables for the current database
	 * 
	 * @return array
	 */
	function get_tables()
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
	function get_system_tables()
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
	function affected_rows()
	{
		return ibase_affected_rows($this->conn);
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	function num_rows()
	{
		$count = 0;

		if(isset($this->statement))
		{
			while($row = $this->fetch())
			{
				$count++;
			}
		}
		else
		{
			return FALSE;
		}

		return $count;
	}
	
	/**
	 * Start a database transaction
	 * 
	 * @return resource
	 */
	function beingTransaction()
	{
		if($this->trans = ibase_trans($this->conn) !== null)
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
	function commit()
	{
		return ibase_commit($this->trans);
	}
	
	/**
	 * Rollback a transaction
	 * 
	 * @return bool
	 */
	function rollBack()
	{
		return ibase_rollback($this->trans);
	}	 
}
// End of firebird.php