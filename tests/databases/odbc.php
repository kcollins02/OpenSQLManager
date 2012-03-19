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
 * ODBCTest class.
 * 
 * @extends UnitTestCase
 */
class ODBCTest extends UnitTestCase {
	
	function TestExists()
	{
		$this->assertTrue(in_array('odbc', pdo_drivers()));
	}
}