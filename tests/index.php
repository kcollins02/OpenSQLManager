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
 * Unit test bootstrap - Using php simpletest
 */

define('BASE_DIR', '../src');

// Include simpletest
// it has to be set in your php path, or put in the tests folder
require_once('simpletest/autorun.php');


// Bulk loading wrapper workaround for PHP < 5.4
function do_include($path)
{
	require_once($path);
}

// Include core tests
require_once("core.php");

// Include db classes
array_map('do_include', glob("../src/databases/*.php"));

// Include db tests
array_map('do_include', glob("./databases/*.php"));