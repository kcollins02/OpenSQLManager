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
		$this->assertTrue( ! empty($tables));
	}
	
	function TestGetSystemTables()
	{
		$tables = $this->db->get_system_tables();
		
		$this->assertTrue(is_array($tables));
	}

	function TestCreateTable()
	{
		//Attempt to create the table
		$sql = $this->db->manip->create_table('create_test', 
			array(
				'id' => 'INTEGER',
				'key' => 'TEXT',
				'val' => 'TEXT',
			), 
			array(
				'id' => 'PRIMARY KEY'
			)
		);
		$this->db->query($sql);

		//Check
		$dbs = $this->db->get_tables();
		$this->assertEqual($dbs['create_test'], 'CREATE TABLE "create_test" (id INTEGER PRIMARY KEY, key TEXT , val TEXT )');
	}
	
	function TestPreparedStatements()
	{
		$sql = <<<SQL
			INSERT INTO "create_test" ("id", "key", "val") 
			VALUES (?,?,?)
SQL;
		$statement =& $this->db->prepare_query($sql, array(1,"boogers", "Gross"));
		
		$statement->execute();

	}
	
	function TestPrepareExecute()
	{
		$sql = <<<SQL
			INSERT INTO "create_test" ("id", "key", "val") 
			VALUES (?,?,?)
SQL;
		$this->db->prepare_execute($sql, array(
			2, "works", 'also?'
		));
	
	}
	
	function TestDeleteTable()
	{
		//Make sure the table exists to delete
		$dbs = $this->db->get_tables();
		$this->assertTrue(isset($dbs['create_test']));

		//Attempt to delete the table
		$sql = $this->db->manip->delete_table('create_test');
		$this->db->query($sql);

		//Check
		$dbs = $this->db->get_tables();
		$this->assertTrue(empty($dbs['create_test']));
	}

}