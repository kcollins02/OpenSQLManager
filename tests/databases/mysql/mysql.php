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
 * MySQLTest class.
 *
 * @extends UnitTestCase
 */
class MySQLTest extends DBTest {

	function setUp()
	{
		// Attempt to connect, if there is a test config file
		if (is_file("../test_config.json"))
		{
			$params = json_decode(file_get_contents("../test_config.json"));
			$params = $params->mysql;

			$this->db = new MySQL("host={$params->host};port={$params->port};dbname={$params->conn_db}", $params->user, $params->pass);
		}
		elseif (($var = getenv('CI')))
		{
			$this->db = new MySQL('host=127.0.0.1;port=3306;dbname=test', 'root');
		}
	}

	function TestExists()
	{
		$this->assertTrue(in_array('mysql', pdo_drivers()));
	}

	function TestConnection()
	{
		if (empty($this->db))  return;

		$this->assertIsA($this->db, 'MySQL');
	}

	function TestCreateTable()
	{
		if (empty($this->db))  return;

		//Attempt to create the table
		$sql = $this->db->sql->create_table('create_test',
			array(
				'id' => 'int(10)',
				'key' => 'TEXT',
				'val' => 'TEXT',
			),
			array(
				'id' => 'PRIMARY KEY'
			)
		);

		$this->db->query($sql);

		//Attempt to create the table
		$sql = $this->db->sql->create_table('create_join',
			array(
				'id' => 'int(10)',
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

		$this->assertTrue(in_array('create_test', $dbs));

	}
	
	function TestTruncate()
	{
		$this->db->truncate('create_test');
		$this->db->truncate('create_join');
		
		$ct_query = $this->db->query('SELECT * FROM create_test');
		$cj_query = $this->db->query('SELECT * FROM create_join');
	}
	
	function TestPreparedStatements()
	{
		if (empty($this->db))  return;

		$sql = <<<SQL
			INSERT INTO `create_test` (`id`, `key`, `val`)
			VALUES (?,?,?)
SQL;
		$statement = $this->db->prepare_query($sql, array(1,"boogers", "Gross"));

		$statement->execute();

	}

	function TestPrepareExecute()
	{
		if (empty($this->db))  return;

		$sql = <<<SQL
			INSERT INTO `create_test` (`id`, `key`, `val`)
			VALUES (?,?,?)
SQL;
		$this->db->prepare_execute($sql, array(
			2, "works", 'also?'
		));

	}

	function TestCommitTransaction()
	{
		if (empty($this->db))  return;

		$res = $this->db->beginTransaction();

		$sql = 'INSERT INTO `create_test` (`id`, `key`, `val`) VALUES (10, 12, 14)';
		$this->db->query($sql);

		$res = $this->db->commit();
		$this->assertTrue($res);
	}

	function TestRollbackTransaction()
	{
		if (empty($this->db))  return;

		$res = $this->db->beginTransaction();

		$sql = 'INSERT INTO `create_test` (`id`, `key`, `val`) VALUES (182, 96, 43)';
		$this->db->query($sql);

		$res = $this->db->rollback();
		$this->assertTrue($res);
	}
	
	function TestGetSchemas()
	{
		$this->assertFalse($this->db->get_schemas());
	}
	
	function TestGetsProcedures()
	{
		$this->assertTrue(is_array($this->db->get_procedures()));
	}
	
	function TestGetTriggers()
	{
		$this->assertTrue(is_array($this->db->get_triggers()));
	}
	
	function TestGetSequences()
	{
		$this->assertFalse($this->db->get_sequences());
	}
}

