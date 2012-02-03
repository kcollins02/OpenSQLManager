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
 * Bootstrap file
 *
 * Initializes parent window and starts the GTK event loop
 */

// --------------------------------------------------------------------------

// Suppress errors that php-gtk puts out
error_reporting(-1 & ~(E_STRICT | E_DEPRECATED));

// Set the stupid timezone so PHP shuts up.
date_default_timezone_set('GMT');

// Set the current directory as the base for included files
define('BASE_DIR', dirname(__FILE__));

// --------------------------------------------------------------------------

/**
 * Log fatal errors
 */
function log_fatal()
{
	// Catch the last error
	$error = error_get_last();

	// types of errors that are fatal
	$fatal = array(E_ERROR, E_PARSE, E_RECOVERABLE_ERROR);

	// Log error.
	if(in_array($error['type'], $fatal))
	{
		file_put_contents('errors.txt', print_r($error, TRUE), FILE_APPEND);
	}
}

register_shutdown_function('log_fatal');

// --------------------------------------------------------------------------

// Make sure php-gtk works
if ( ! class_exists('gtk')) 
{
	trigger_error("PHP-gtk not found. Please load the php-gtk2 module in your php.ini", E_USER_ERROR);
	die();
}

// Make sure pdo exists
if( ! class_exists('pdo'))
{
	trigger_error("PHP support for PDO is required.", E_USER_ERROR);
	die();
}

// --------------------------------------------------------------------------

// Bulk loading wrapper workaround for PHP < 5.4
function do_include($path)
{
	require_once($path);
}

// Load everything so that we don't have to do requires later
{
	array_map('do_include', glob(BASE_DIR . "/common/*.php"));
	array_map('do_include',  glob(BASE_DIR . "/windows/*.php"));
}

// --------------------------------------------------------------------------

// Load db classes based on capability
$path = BASE_DIR . "/databases/";

foreach(pdo_drivers() as $d)
{
	$file = "{$path}{$d}.php";
	
	if(is_file($file))
	{
		require_once($file);
	}
}

// Load Firebird if there is support
if(function_exists('ibase_connect'))
{
	require_once("{$path}firebird.php");
}

// --------------------------------------------------------------------------

// Create the main window
new Main();

// Start the GTK event loop
GTK::main();

// End of index.php
