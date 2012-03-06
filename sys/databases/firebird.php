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

class Firebird extends DB_PDO {

	protected $statement, $trans, $result;

	/**
	 * Constructor
	 */
	public function __construct($dbpath, $user='sysdba', $pass='masterkey')
	{
		parent::__construct("firebird:{$dbpath}", $user, $pass);

		$class = __CLASS__."_sql";
		$this->sql = new $class;
	}

	// --------------------------------------------------------------------------

	/**
	 * Empty a database table
	 * 
	 * @param string $table
	 */
	public function truncate($table)
	{
		// Firebird lacka a truncate command
		$sql = 'DELETE FROM "'.$table.'"';
		$this->query($sql);
	}

	// --------------------------------------------------------------------------

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

	// --------------------------------------------------------------------------

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
	
	// --------------------------------------------------------------------------

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

	// --------------------------------------------------------------------------

	/**
	 * Create an SQL backup file for the current database's structure
	 *
	 * @return string
	 */
	public function backup_structure()
	{
		// @todo Implement Backup function
		return '';	
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Create an SQL backup file for the current database's data
	 *
	 * @param array $exclude
	 * @param bool $system_tables
	 * @return string
	 */
	public function backup_data($exclude=array(), $system_tables=FALSE)
	{
		// Determine which tables to use
		if($system_tables == TRUE)
		{
			$tables = array_merge($this->get_system_tables(), $this->get_tables());
		}
		else
		{
			$tables = $this->get_tables();
		}
		
		// Filter out the tables you don't want
		if( ! empty($exclude))
		{
			$tables = array_diff($tables, $exclude);
		}
		
		$output_sql = '';
		
		// Get the data for each object
		foreach($tables as $t)
		{
			$sql = 'SELECT * FROM "'.trim($t).'"';
			$res = $this->query($sql);
			$obj_res = $this->fetchAll(PDO::FETCH_ASSOC);
			
			unset($res);
			
			// Nab the column names by getting the keys of the first row
			$columns = @array_keys($obj_res[0]);
			
			$insert_rows = array();
			
			// Create the insert statements
			foreach($obj_res as $row)
			{
				$row = array_values($row);
			
				// Quote values as needed by type
				if(stripos($t, 'RDB$') === FALSE)
				{
					$row = array_map(array(&$this, 'quote'), $row);
					$row = array_map('trim', $row);
				}
				
				$row_string = 'INSERT INTO "'.trim($t).'" ("'.implode('","', $columns).'") VALUES ('.implode(',', $row).');';
				
				unset($row);
				
				$insert_rows[] = $row_string;
			}
			
			unset($obj_res);
			
			$output_sql .= "\n\nSET TRANSACTION;\n".implode("\n", $insert_rows)."\nCOMMIT;";
		}
		
		return $output_sql;
	}

}

// End of firebird.php