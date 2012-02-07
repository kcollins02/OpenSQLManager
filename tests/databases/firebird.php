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

	function TestConnection()
	{
		$this->firebird = new Firebird("../test_dbs/FB_TEST_DB.FDB");
		$this->assertIsA($this->firebird, 'Firebird');
	}
}