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
define('TEST_DIR', dirname(__FILE__));
define('BASE_DIR', str_replace(basename(TEST_DIR), '', TEST_DIR).'/sys/');
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------------------

/**
 * Alias for require_once for array_map
 *
 * @param string $path
 * @return void
 */
function do_include($path)
{
	require_once($path);
}


// Include simpletest
// it has to be set in your php path, or put in the tests folder
require_once('simpletest/autorun.php');

// Require base testing classes
require_once(TEST_DIR.'/parent.php');

// Bulk loading wrapper workaround for PHP < 5.4
function do_include($path)
{
	require_once($path);
}

// Include core tests
require_once("core.php");

// Include required methods
array_map('do_include', glob(BASE_DIR . 'common/*.php'));
array_map('do_include', glob(BASE_DIR . 'db/*.php'));


// Include db tests
// Load db classes based on capability
$src_path = BASE_DIR.'db/drivers/';
$test_path = TEST_DIR.'/databases/';

foreach(pdo_drivers() as $d)
{
	// PDO firebird isn't stable enough to
	// bother, so skip it.
	if ($d === 'firebird')
	{
		continue;
	}

	$src_file = "{$src_path}{$d}.php";

	if(is_file($src_file))
	{
		require_once("{$src_path}{$d}.php");
		require_once("{$src_path}{$d}_sql.php");
		require_once("{$test_path}{$d}.php");
		require_once("{$test_path}{$d}-qb.php");
	}
}

// Load Firebird if there is support
if(function_exists('fbird_connect'))
{
	require_once("{$src_path}firebird.php");
	require_once("{$src_path}firebird_sql.php");
	require_once("{$test_path}firebird.php");
	require_once("{$test_path}firebird-qb.php");
}