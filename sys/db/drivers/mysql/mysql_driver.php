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

	protected $escape_char = '`';

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
	 * Connect to a different database
	 *
	 * @param string $name
	 */
	public function switch_db($name)
	{
		// @todo Implement
		return FALSE;
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
	 * Returns system tables for the current database
	 *
	 * @return array
	 */
	public function get_system_tables()
	{
		return array('information_schema');
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
}
//End of mysql_driver.php