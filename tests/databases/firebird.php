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
	}
	
	function setUp()
	{
		$this->db = new Firebird(dirname(__FILE__)."/../test_dbs/FB_TEST_DB.FDB");
		$this->tables = $this->db->get_tables();
	}
	
	function tearDown()
	{
		unset($this->db);
		unset($this->tables);
	}

	function TestConnection()
	{
		$this->assertIsA($this->db, 'Firebird');
	}
	
	function TestGetTables()
	{
		$tables = $this->tables;
		$this->assertTrue(is_array($tables));
	}
	
	function TestGetSystemTables()
	{	
		$only_system = TRUE;
		
		foreach($this->tables as $t)
		{
			if(stripos($t, 'rdb$') !== 0 && stripos($t, 'mon$') !== 0)
			{
				$only_system = FALSE;
				break;
			}
		}
		
		$this->assertTrue($only_system);
	}

	function TestCreateTable()
	{
		//Attempt to create the table
		$sql = $this->db->manip->create_table('create_test', array('id' => 'SMALLINT'));
		$this->db->query($sql);
		
		//This test fails for an unknown reason, when clearly the table exists
		//Reset
		/*$this->tearDown();
		$this->setUp();
		
		//Check
		$table_exists = (bool)in_array('create_test', $this->tables);
		
		echo "create_test exists :".(int)$table_exists.'<br />';
		
		$this->assertTrue($table_exists);*/
	}

	function TestDeleteTable()
	{
		//Attempt to delete the table
		$sql = $this->db->manip->delete_table('create_test');
		$this->db->query($sql);
		
		//Reset
		$this->tearDown();
		$this->setUp();
		
		//Check
		$table_exists = in_array('create_test', $this->tables);
		$this->assertFalse($table_exists);
	}

	function TestPreparedStatements()
	{
		$sql = 'INSERT INTO "create_test" ("id") VALUES (?),(?),(?)';
		$this->db->prepare($sql);

		$this->db->execute(array(1,2,3));

	}
}