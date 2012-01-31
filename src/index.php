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

error_reporting(-1 & ~(E_STRICT | E_DEPRECATED));

/**
 * Log fatal errors
 */
function log_fatal()
{
	// Catch the last error
    $error = error_get_last();

    // types of errors that are fatal
    $fatal = array(E_ERROR, E_PARSE, E_RECOVERABLE_ERROR);

    // Display pretty error page
    if(in_array($error['type'], $fatal))
    {
   		file_put_contents('errors.txt', print_r($error, TRUE), FILE_APPEND);
    }
}

register_shutdown_function('log_fatal');

// Make sure php-gtk works
if ( ! class_exists('gtk')) 
{
    die("Please load the php-gtk2 module in your php.ini\r\n");
}

// Set the stupid timezone so PHP shuts up.
date_default_timezone_set('GMT');

// Bulk loading wrapper workaround for PHP < 5.4
function do_include($path)
{
	require_once($path);
}

$dir = dirname(__FILE__);

// Load modules
{
	array_map('do_include',  glob("{$dir}/databases/*.php"));
	array_map('do_include',  glob("{$dir}/windows/*.php"));
}

// Create the main window
$wnd = new Main();

// Start the GTK event loop
GTK::main();

// End of index.php