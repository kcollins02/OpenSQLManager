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
if ( ! class_exists('gtk')) 
{
    die("Please load the php-gtk2 module in your php.ini\r\n");
}

$dir = dirname(__FILE__);

// Load modules
{

	$requires = glob("{$dir}/databases/*.php");
	$requires = array_merge($requires, glob("{$dir}/windows/*.php"));
	
	for($i=0, $count=count($requires); $i<$count; $i++)
	{
		require_once($requires[$i]);
	}
}

// Create the main window
$wnd = new Main();
$wnd->show_all();

// Start the GTK event loop
GTK::main();