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
 * SQLiteTest class.
 * 
 * @extends UnitTestCase
 */
class SQLiteTest extends UnitTestCase {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->db = new SQLite(dirname(__FILE__)."/../test_dbs/test_sqlite.db");
	}

	function TestConnection()
	{
		$this->assertIsA($this->db, 'SQLite');
	}

	function TestGetTables()
	{
		$tables = $this->db->get_tables();
		$this->assertEqual($tables[0]['name'], 'test');
	}
}