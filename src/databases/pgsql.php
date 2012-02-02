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
	 * Get the list of databases for the current db connection
	 * 
	 * @return array
	 */
	function get_dbs()
	{
		$sql = 'SELECT "tablename" FROM "pg_tables" 
			WHERE "tablename" NOT LIKE pg\_%
			AND "tablename" NOT LIKE sql\%';

		$res = $this->query($sql);

		$dbs = $res->fetchAll(PDO::FETCH_ASSOC);

		return $dbs;
	}

	/**
	 * Get a list of views for the current db connection
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

}

/**
 * PostgreSQL DB Structure manipulation class
 */
class pgSQL_manip extends pgSQL {
	
	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct($dsn, $username, $password, $options);
	}

}