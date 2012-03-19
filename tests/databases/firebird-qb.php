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
class FirebirdQBTest extends QBTest {

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
		$this->db = new Query_Builder($params);
		
		// echo '<hr /> Firebird Queries <hr />';
	}

	function TestGet()
	{
		$query = $this->db->get('create_test ct');
		
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestGetLimit()
	{
		$query = $this->db->get('create_test', 2);
		
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestGetLimitSkip()
	{
		$query = $this->db->get('create_test', 2, 1);
		
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestSelectWhereGet()
	{
		$query = $this->db->select('id, key as k, val')
			->where('id >', 1)
			->where('id <', 800)
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestSelectWhereGet2()
	{
		$query = $this->db->select('id, key as k, val')
			->where(' id ', 1)
			
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestSelectGet()
	{
		$query = $this->db->select('id, key as k, val')
			->get('create_test', 2, 1);

		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestSelectFromGet()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestSelectFromLimitGet()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test ct')
			->where('id >', 1)
			->limit(3)
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestOrderBy()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->order_by('id', 'DESC')
			->order_by('k', 'ASC')
			->limit(5,2)
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestOrderByRandom()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->order_by('id', 'rand')
			->limit(5,2)
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestOrWhere()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test')
			->where(' id ', 1)
			->or_where('key >', 0)
			->limit(2, 1)
			->get();
		
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestGroupBy()
	{
	
	}
	
	/*function TestGroupBy()
	{
		$query = $this->db->select('id, key as k, val')
			->from('create_test')
			->where('id >', 0)
			->where('id <', 9000)
			->group_by('k')
			->group_by('val')
			->order_by('id', 'DESC')
			->order_by('k', 'ASC')
			->limit(5,2)
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}*/
	
	function TestLike()
	{
		$query = $this->db->from('create_test')
			->like('key', 'og')
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestWhereIn()
	{
		$query = $this->db->from('create_test')
			->where_in('key', array(12, 96, "works"))
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestJoin()
	{
		$query = $this->db->from('create_test')
			->join('create_join cj', 'cj.id = create_test.id')
			->get();
			
		$this->assertIsA($query, 'Firebird_Result');
	}
	
	function TestInsert()
	{
		$query = $this->db->set('id', 4)
			->set('key', 4)
			->set('val', 5)
			->insert('create_test');
			
		$this->assertTrue($query);
	}
	
	function TestUpdate()
	{
		$query = $this->db->set('id', 4)
			->set('key', 'gogle')
			->set('val', 'non-word')
			->where('id', 4)
			->update('create_test');
			
		$this->assertTrue($query);
	}
	
	function TestDelete()
	{
		$query = $this->db->where('id', 4)->delete('create_test');
			
		$this->assertTrue($query);
	}
	
	
}