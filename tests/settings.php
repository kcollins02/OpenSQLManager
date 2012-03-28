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
 * Settings Class Test Class
 */
class SettingsTest extends UnitTestCase {

	function __construct()
	{
		parent::__construct();
		$this->settings =& Settings::get_instance();
	}

	function TestExists()
	{
		$this->assertIsA($this->settings, 'Settings');
	}
}
