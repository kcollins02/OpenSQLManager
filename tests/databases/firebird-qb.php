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
 * Firebird Query Builder Tests
 */
class FirebirdQBTest extends UnitTestCase {

	function __construct()
	{
		parent::__construct();
	}
	
	function setUp()
	{
		$dbpath = TEST_DIR.DS.'test_dbs'.DS.'FB_TEST_DB.FDB';

		// Test the query builder
		$params = new Stdclass();
		$params->type = 'firebird';
		$params->file = $dbpath;
		$params->host = 'localhost';
		$params->user = 'sysdba';
		$params->pass = 'masterkey';
		$this->qb = new Query_Builder($params);
	}
	
	function tearDown()
	{
		unset($this->qb);
	}

	function TestQBGet()
	{
		$query = $this->qb->get('create_test');
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestQBGetLimit()
	{
		$query = $this->qb->get('create_test', 2);
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestQBGetLimitSkip()
	{
		$query = $this->qb->get('create_test', 2, 1);
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestQBSelectWhereGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->where('id >', 1)
			->where('id <', 800)
			->get('create_test', 2, 1);

		$this->assertTrue(is_resource($query));
	}
	
	function TestQBSelectWhereGet2()
	{
		$query = $this->qb->select('id, key as k, val')
			->where(' id ', 1)
			
			->get('create_test', 2, 1);

		$this->assertTrue(is_resource($query));
	}

	
	function TestQBSelectGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->get('create_test', 2, 1);

		$this->assertTrue(is_resource($query));
	}
	
	function TestSelectFromGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->get();
			
		$this->assertTrue(is_resource($query));
	}
	
	function TestSelectFromLimitGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->limit(3)
			->get();
			
		$this->assertTrue(is_resource($query));
	}
}