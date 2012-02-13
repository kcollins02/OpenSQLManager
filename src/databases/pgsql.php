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
 * PostgreSQL specifc class
 *
 * @extends DB_PDO
 */
class pgSQL extends DB_PDO {

	/**
	 * Connect to a PosgreSQL database
	 * 
	 * @param string $dsn
	 * @param string $username=null
	 * @param string $password=null
	 * @param array  $options=array()
	 */
	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct("pgsql:$dsn", $username, $password, $options);

		//Get db manip class
		$class = __CLASS__.'_manip';
		$this->manip = new $class;
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		$sql = 'TRUNCATE "' . $table . '"';
		$this->query($sql); 
	}

	/**
	 * Get list of databases for the current connection
	 * 
	 * @return array
	 */
	function get_dbs()
	{
		$sql = <<<SQL
			SELECT "datname" FROM "pg_database" 
			WHERE "datname" NOT IN ('template0','template1') 
			ORDER BY 1
SQL;

		$res = $this->query($sql);

		$dbs = $res->fetchAll(PDO::FETCH_ASSOC);

		return $dbs;
	}

	/**
	 * Get the list of tables for the current db
	 * 
	 * @return array
	 */
	function get_tables()
	{
		$sql = 'SELECT "tablename" FROM "pg_tables" 
			WHERE "tablename" NOT LIKE pg\_%
			AND "tablename" NOT LIKE sql\%';

		$res = $this->query($sql);

		$tables = $res->fetchAll(PDO::FETCH_ASSOC);

		return $tables;
	}

	/**
	 * Get the list of system tables
	 * 
	 * @return array
	 */
	function get_system_tables()
	{
		$sql = 'SELECT "tablename" FROM "pg_tables"
			WHERE "tablename" LIKE \'pg\_%\'
			OR "tablename" LIKE \'sql\%\'';
		
		$res = $this->query($sql);

		$tables = $res->fetchAll(PDO::FETCH_ASSOC);

		return $tables;
		
	}

	/**
	 * Get a list of schemas, either for the current connection, or
	 * for the current datbase, if specified.
	 * 
	 * @param string $database=""
	 * @return array
	 */
	function get_schemas($database="")
	{
		if($database === "")
		{
			$sql = <<<SQL
				SELECT DISTINCT "schemaname" FROM "pg_tables" 
				WHERE "schemaname" NOT LIKE 'pg\_%'
SQL;

		}

		$sql = <<<SQL
			SELECT "nspname" FROM pg_namespace
			WHERE "nspname" NOT LIKE 'pg\_%'
SQL;

		$res = $this->query($sql);
		$schemas = $res->fetchAll(PDO::FETCH_ASSOC);

		return $schemas;
	}

	/**
	 * Get a list of views for the current db
	 * 
	 * @return array
	 */
	function get_views()
	{
		$sql = 'SELECT "viewname" FROM "pg_views" 
			WHERE viewname NOT LIKE pg\_%';

		$res = $this->query($sql);

		$views = $res->fetchAll(PDO::FETCH_ASSOC);

		return $views;
	}

	/**
	 * Return the number of rows returned for a SELECT query
	 * 
	 * @return int
	 */
	function num_rows()
	{
		return (isset($this->statement)) ? $this->statement->rowCount : FALSE;
	}
}
//End of pgsql.php