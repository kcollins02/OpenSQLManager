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
	
	function __construct()
	{
		parent::__construct();
	}
	
	function setUp()
	{
		$path = dirname(__FILE__)."/../test_dbs/test_sqlite.db";
		$this->db = new SQLite($path);
		
		$params = new Stdclass();
		$params->type = 'sqlite';
		$params->file = $path;
		$params->host = 'localhost';
		$this->qb = new Query_Builder($params);
	}
	
	function tearDown()
	{
		unset($this->db);
	}

	function TestConnection()
	{
		$this->assertIsA($this->db, 'SQLite');
	}
	
	function TestGetTables()
	{
		$tables = $this->db->get_tables();
		$this->assertTrue(is_array($tables));
	}
	
	function TestGetSystemTables()
	{
		$tables = $this->db->get_system_tables();
		
		$this->assertTrue(is_array($tables));
	}
	
	function TestCreateTransaction()
	{
		$res = $this->db->beginTransaction();
		$this->assertTrue($res);
	}

	function TestCreateTable()
	{
		//Attempt to create the table
		$sql = $this->db->sql->create_table('create_test', 
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
		$statement = $this->db->prepare_query($sql, array(1,"boogers", "Gross"));
		
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
	
	function TestCommitTransaction()
	{
		$this->TestCreateTransaction();
		
		$sql = 'INSERT INTO "create_test" ("id", "key", "val") VALUES (10, 12, 14)';
		$this->db->query($sql);
	
		$res = $this->db->commit();
		$this->assertTrue($res);
	}
	
	function TestRollbackTransaction()
	{
		$this->TestCreateTransaction();
		
		$sql = 'INSERT INTO "create_test" ("id", "key", "val") VALUES (182, 96, 43)';
		$this->db->query($sql);
	
		$res = $this->db->rollback();
		$this->assertTrue($res);
	}
	
	function TestQBGet()
	{
		$query = $this->qb->get('create_test');
		
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestQBGetLimit()
	{
		$query = $this->qb->get('create_test', 2);
		
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestQBGetLimitSkip()
	{
		$query = $this->qb->get('create_test', 2, 1);
		
		$this->assertIsA($query, 'PDOStatement');
	}

	function TestQBSelectWhereGet()
	{
		$query = $this->qb->select('id, key as k, val')->where('id >', 1)->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestQBSelectWhereGet2()
	{
		$query = $this->qb->select('id, key as k, val')->where('id', 1)->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');
	}

	function TestQBSelectGet()
	{
		$query = $this->qb->select('id, key as k, val')->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');

	}
	
	function TestDeleteTable()
	{
		//Make sure the table exists to delete
		$dbs = $this->db->get_tables();
		$this->assertTrue(isset($dbs['create_test']));

		//Attempt to delete the table
		$sql = $this->db->sql->delete_table('create_test');
		$this->db->query($sql);

		//Check
		$dbs = $this->db->get_tables();
		$this->assertFalse(in_array('create_test', $dbs));	
	}

}