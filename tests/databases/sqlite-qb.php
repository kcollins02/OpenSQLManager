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
 * Class for testing Query Builder with SQLite 
 */
 class SQLiteQBTest extends UnitTestCase {
 
 	function __construct()
 	{
 		parent::__construct();
 	
 		$path = TEST_DIR.DS.'test_dbs'.DS.'test_sqlite.db';
		$params = new Stdclass();
		$params->type = 'sqlite';
		$params->file = $path;
		$params->host = 'localhost';
		$this->qb = new Query_Builder($params);
		
		echo '<hr /> SQLite Queries <hr />';
 	}
	
	function TestGet()
	{
		$query = $this->qb->get('create_test');
		
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestGetLimit()
	{
		$query = $this->qb->get('create_test', 2);
		
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestGetLimitSkip()
	{
		$query = $this->qb->get('create_test', 2, 1);
		
		$this->assertIsA($query, 'PDOStatement');
	}

	function TestSelectWhereGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->where('id >', 1)
			->where('id <', 900)
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestSelectWhereGet2()
	{
		$query = $this->qb->select('id, key as k, val')
			->where('id !=', 1)
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');
	}

	function TestSelectGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestSelectFromGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->get();
			
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestSelectFromLimitGet()
	{
		$query = $this->qb->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->limit(3)
			->get();
			
		$this->assertIsA($query, 'PDOStatement');
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
			
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestInsert()
	{
		$query = $this->qb->set('id', 4)
			->set('key', 4)
			->set('val', 5)
			->insert('create_test');
			
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestUpdate()
	{
		$query = $this->qb->set('id', 4)
			->set('key', 'gogle')
			->set('val', 'non-word')
			->where('id', 4)
			->update('create_test');
			
		$this->assertIsA($query, 'PDOStatement');
	}
	
	function TestDelete()
	{
		$query = $this->qb->where('id', 4)->delete('create_test');
			
		$this->assertIsA($query, 'PDOStatement');
	}
}