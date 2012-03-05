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
require_once("../sys/common/db_pdo.php");
require_once("../sys/common/query_builder.php");


// Include db tests
// Load db classes based on capability
$src_path = "../sys/databases/";
$test_path = "./databases/";

foreach(pdo_drivers() as $d)
{
	$src_file = "{$src_path}{$d}.php";
	
	if(is_file($src_file))
	{
		require_once("{$src_path}{$d}.php");
		require_once("{$src_path}{$d}_sql.php");
		require_once("{$test_path}{$d}.php");
	}
}

// Load Firebird if there is support
if(function_exists('ibase_connect') && ! in_array('firebird', pdo_drivers()))
{
	require_once("{$src_path}firebird-base.php");
	require_once("{$src_path}firebird_sql.php");
	require_once("{$test_path}firebird.php");
}