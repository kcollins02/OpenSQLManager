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
define('TEST_DIR', dirname(__FILE__).'/');
define('BASE_DIR', str_replace(basename(TEST_DIR), '', TEST_DIR).'/sys/');
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------------------

// Include simpletest
// it has to be set in your php path, or put in the tests folder
require_once('simpletest/autorun.php');

// Bulk loading wrapper workaround for PHP < 5.4
function do_include($path)
{
	require_once($path);
}

// Include core tests
array_map('do_include', glob(TEST_DIR . 'core/*.php'));

// Include required methods
array_map('do_include', glob(BASE_DIR . 'common/*.php'));
array_map('do_include', glob(BASE_DIR . 'db/*.php'));


// Include db tests
// Load db classes based on capability
$src_path = BASE_DIR.'db/drivers/';
$test_path = TEST_DIR.'databases/';

foreach(pdo_drivers() as $d)
{
	// PDO firebird isn't stable enough to
	// bother, so skip it.
	if ($d === 'firebird')
	{
		continue;
	}

	// Load by driver folder
	$src_dir = "{$src_path}{$d}";

	if(is_dir($src_dir))
	{
		array_map('do_include', glob($src_path.$d.'/*.php'));
		require_once("{$test_path}{$d}/{$d}.php");
		require_once("{$test_path}{$d}/{$d}-qb.php");
	}
}

// Load Firebird if there is support
if(function_exists('fbird_connect'))
{
	array_map('do_include', glob($src_path.'firebird/*.php'));
	require_once("{$test_path}firebird/firebird.php");
	require_once("{$test_path}firebird/firebird-qb.php");
}