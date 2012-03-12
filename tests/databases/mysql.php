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
class MySQLTest extends UnitTestCase {

	function __construct()
	{

	}
	
	function TestExists()
	{
		$this->assertTrue(in_array('mysql', pdo_drivers()));
	}
}
 
