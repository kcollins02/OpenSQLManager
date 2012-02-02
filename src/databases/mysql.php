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
	function __construct($dsn, $username=null, $password=null, $options=array())
	{
		$options = array_merge(array(

		),
		$options);

		parent::__construct("mysql:$dsn", $username, $password, $options);
	}

	/**
	 * Empty a table
	 *
	 * @param string $table
	 */
	function truncate($table)
	{
		$sql = "TRUNCATE `{$table}`";
		$this->query($sql);
	}

	/**
	 * Returns the datbases available for the current connection
	 * 
	 * @return array
	 */
	function get_dbs()
	{
		$sql = "SHOW TABLES";
		$res = $this->query($sql);

		return $res->fetchAll(PDO::FETCH_ASSOC);
	}

}

class MySQL_manip extends MySQL {

	function __construct($dsn, $user=null, $pass=null, $opt=array())
	{
		parent::__construct($dsn, $user, $pass, $opt);
	}	
}