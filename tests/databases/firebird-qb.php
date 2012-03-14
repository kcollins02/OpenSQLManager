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
		
		$dbpath = TEST_DIR.DS.'test_dbs'.DS.'FB_TEST_DB.FDB';

		// Test the query builder
		$params = new Stdclass();
		$params->type = 'firebird';
		$params->file = $dbpath;
		$params->host = 'localhost';
		$params->user = 'sysdba';
		$params->pass = 'masterkey';
		$this->qb = new Query_Builder($params);
		
		//echo '<hr /> Firebird Queries <hr />';
	}

	function TestGet()
	{
		$query = $this->qb->get('create_test');
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestGetLimit()
	{
		$query = $this->qb->get('create_test', 2);
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestGetLimitSkip()
	{
		$query = $this->qb->get('create_test', 2, 1);
		
		$this->assertTrue(is_resource($query));
	}
	
	function TestSelectWhereGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->where('id >', 1)
			->where('id <', 800)
			->get('create_test', 2, 1);

		$this->assertTrue(is_resource($query));
	}
	
	function TestSelectWhereGet2()
	{
		$query = $this->qb->select('id, key as k, val')
			->where(' id ', 1)
			
			->get('create_test', 2, 1);

		$this->assertTrue(is_resource($query));
	}

	
	function TestSelectGet()
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
	
	function TestOrderBy()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->order_by('id', 'DESC')
			->order_by('k', 'ASC')
			->limit(5,2)
			->get();
			
		$this->assertTrue(is_resource($query));
	}
	
	function TestOrderByRand()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->order_by('id', 'rand')
			->limit(5,2)
			->get();
			
		$this->assertTrue(is_resource($query));
	}
	
	/*function TestGroupBy()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->group_by('k')
			->group_by('val')
			->order_by('id', 'DESC')
			->order_by('k', 'ASC')
			->limit(5,2)
			->get();
			
		$this->assertTrue(is_resource($query));
	}*/
	
	function TestInsert()
	{
		$query = $this->qb->set('id', 4)
			->set('key', 4)
			->set('val', 5)
			->insert('create_test');
			
		$this->assertTrue($query);
	}
	
	function TestUpdate()
	{
		$query = $this->qb->set('id', 4)
			->set('key', 'gogle')
			->set('val', 'non-word')
			->where('id', 4)
			->update('create_test');
			
		$this->assertTrue($query);
	}
	
	function TestDelete()
	{
		$query = $this->qb->where('id', 4)->delete('create_test');
			
		$this->assertTrue($query);
	}
	
	
}