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
  * ODBC Database Driver
  *
  * For general database access for databases not specified by the main drivers
  *
  * @extends DB_PDO
  */
class ODBC extends DB_PDO {

	public function __construct($dsn, $username=null, $password=null, $options=array())
	{
		parent::__construct("odbc:$dsn", $username, $password, $options);

		$class = __CLASS__.'_sql';
		$this->sql = new $class;
	}

	// --------------------------------------------------------------------------

	/**
	 * Doesn't apply to ODBC
	 */
	public function switch_db($name)
	{
		return FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Empty the current database
	 *
	 * @return void
	 */
	public function truncate($table)
	{
		$sql = "DELETE FROM {$table}";
		$this->query($sql);
	}

	// --------------------------------------------------------------------------

	/**
	 * Return the number of rows returned for a SELECT query
	 *
	 * @return int
	 */
	public function num_rows()
	{
		// @TODO: Implement
	}
}
// End of odbc_driver.php