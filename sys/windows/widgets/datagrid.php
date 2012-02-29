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
 * Class for controlling database views
 */
class DataGrid extends GtkTreeView{

	protected $model, $settings;
	
	function __construct()
	{
		$this->settings =& Settings::get_instance();
		$this->model = new GtkTreeStore(GObject::TYPE_PHP_VALUE, GObject::TYPE_STRING);
		parent::__construct($this->model);
	}

	function __get($key)
	{
		
	}

	function __set($key, $val)
	{
		
	}
}