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
 * MySQL specific class
 *
 * @extends DB_PDO
 */
class MySQL extends DB_PDO {

	/**
	 * Connect to MySQL Database
	 *
	 * @param string $dsn
	 * @param string $username=null
	 * @param string $password=null
	 * @param array $options=array()
	 */
	public function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct("mysql:$dsn", $username, $password, $options);

		$class = __CLASS__.'_sql';
		$this->sql = new $class;
	}

	// --------------------------------------------------------------------------

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	public function truncate($table)
	{
		$this->query("TRUNCATE `{$table}`");
	}

	// --------------------------------------------------------------------------

	/**
	 * Get databases for the current connection
	 *
	 * @return array
	 */
	public function get_dbs()
	{
		$res = $this->query("SHOW DATABASES WHERE `Database` !='information_schema'");
		return db_filter(array_values($res->fetchAll(PDO::FETCH_ASSOC)), 'Database');
	}

	// --------------------------------------------------------------------------

	/**
	 * Returns the tables available in the current database
	 *
	 * @return array
	 */
	public function get_tables()
	{
		$res = $this->query('SHOW TABLES');
		return db_filter($res->fetchAll(PDO::FETCH_NUM), 0);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get list of views for the current database
	 *
	 * @return array
	 */
	public function get_views()
	{
		$res = $this->query('SELECT `table_name` FROM `information_schema`.`views`');
		return db_filter($res->fetchAll(PDO::FETCH_NUM), 'table_name');
	}

	// --------------------------------------------------------------------------

	/**
	 * Not applicable to MySQL
	 *
	 * @return FALSE
	 */
	public function get_sequences()
	{
		return FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Return list of custom functions for the current database
	 *
	 * @return array
	 */
	public function get_functions()
	{
		// @todo Implement
		return FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Return list of triggers for the current database
	 *
	 * @return array
	 */
	public function get_triggers()
	{
		// @todo Implement
		return FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Returns system tables for the current database
	 *
	 * @return array
	 */
	public function get_system_tables()
	{
		//MySQL doesn't have system tables
		return array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Return the number of rows returned for a SELECT query
	 *
	 * @return int
	 */
	public function num_rows()
	{
		return isset($this->statement) ? $this->statement->rowCount() : FALSE;
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
	 * @return string
	 */
	public function backup_data()
	{
		// @todo Implement Backup function
		return '';
	}

	// --------------------------------------------------------------------------

	/**
	 * Surrounds the string with the databases identifier escape characters
	 *
	 * @param string $ident
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

		return '`'.implode('`.`', $hiers).'`';
	}
}
//End of mysql.php