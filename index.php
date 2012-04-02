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
error_reporting(-1 & ~(E_STRICT));

// Set the stupid timezone so PHP shuts up.
date_default_timezone_set('GMT');

// Set the current directory as the base for included files
define('BASE_DIR', dirname(__FILE__).'/sys');
define('SETTINGS_DIR', dirname(__FILE__));
define('PROGRAM_NAME', 'OpenSQLManager');

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
	trigger_error("PHP-gtk not found. Please load the php-gtk2 extension in your php.ini", E_USER_ERROR);
	die();
}

// Make sure pdo exists
if( ! class_exists('pdo'))
{
	trigger_error("PHP support for PDO is required.", E_USER_ERROR);
	die();
}

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

// Load everything so that we don't have to do requires later
{
	array_map('do_include', glob(BASE_DIR . "/common/*.php"));
	array_map('do_include', glob(BASE_DIR . "/db/*.php"));
	array_map('do_include', glob(BASE_DIR . "/windows/widgets/*.php"));
	array_map('do_include', glob(BASE_DIR . "/windows/*.php"));
}

// --------------------------------------------------------------------------

// Load db classes based on capability
$path = BASE_DIR . "/db/drivers/";

foreach(pdo_drivers() as $d)
{
	//Favor ibase over PDO firebird
	if ($d === 'firebird')
	{
		continue;
	}

	$file = "{$path}{$d}.php";

	if(is_file($file))
	{
		require_once("{$path}{$d}.php");
		require_once("{$path}{$d}_sql.php");
	}
}

// Load Firebird if there is support
if(function_exists('fbird_connect'))
{
	require_once("{$path}firebird.php");
	require_once("{$path}firebird_sql.php");
}

// --------------------------------------------------------------------------

// Create the main window
new Main();

// Start the GTK event loop
GTK::main();

// End of index.php