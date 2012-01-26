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

//Load modules
$dbs = array_map('require_once', glob('./databases/*.php'));
$wnds = array_map('require_once', glob('./windows/*.php'));




//Start the GTK event loop
GTK::main();