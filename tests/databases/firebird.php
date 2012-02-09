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
 * FirebirdTest class.
 * 
 * @extends UnitTestCase
 */
class FirebirdTest extends UnitTestCase {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->db = new Firebird(dirname(__FILE__)."/../test_dbs/FB_TEST_DB.FDB");
	}

	function TestConnection()
	{
		$this->assertIsA($this->db, 'Firebird');
	}
	
	function TestGetTables()
	{
		$tables = $this->db->get_tables();
	}

	function TestCreateDatabase()
	{
		//Attempt to create the table
		$sql = $this->db->manip->create_table('create_test', array('id' => 'SMALLINT'));
		$this->db->query($sql);
		
		//Check
		$tables = $this->db->get_tables();
		$table_exists = in_array('create_test', $tables);
		$this->assertTrue($table_exists);
	}

	function TestDeleteDatabase()
	{
		//Attempt to delete the table
		$sql = $this->db->manip->delete_table('create_test');
		$this->db->query($sql);
		
		//Check
		$tables = $this->db->get_tables();
		$table_exists = in_array('create_test', $tables);
		$this->assertFalse($table_exists);
	}
}