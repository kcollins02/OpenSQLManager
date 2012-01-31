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
 * Window controlling addtion of database connections
 */
class Add_DB extends GtkWindow {

	var $dbtype, $host, $user, $password, $database;
	
	function __construct()
	{
		parent::__construct();

		$this->set_title("OpenSQLManager - Add Database Connection");

		// Add the Vbox, and show the window
		$this->add($this->_layout());
		$this->show_all();
	}

	/**
	 * Window layout
	 * 
	 * @return GtkVBox
	 */
	private function _layout()
	{
		$table = new GtkTable();

		$db_types = $this->get_available_dbs();

		//Table attach 
		//$tbl->attach(left_start, right_stop, top_start, bottom_stop)

		//Row 1 - Database type
		{
			$dbtypelbl = new GtkLabel("Database Type");
			$this->dbtype = GtkComboBox::new_text();
			$typealign = new GtkAlignment(0, 0.5, 0, 0);
			$typealign->add($dbtypelbl);

			foreach($db_types as $t)
			{
				$this->dbtype->append_text($t);
			}

			$table->attach($typealign, 0, 1, 0, 1);
			$table->attach($this->dbtype, 1, 2, 0, 1);

		}

		//Row 2 - Host
		{
			$hostlbl = new GtkLabel("DB Host");
			$this->host = new GtkEntry();
			$hostalign = new GtkAlignment(0, 0.5, 0, 0);
			$hostalign->add($hostlbl);

			$table->attach($hostalign, 0, 1, 1, 2);
			$table->attach($this->host, 1, 2, 1, 2);
		}

		//Row 3 - Username
		{
			$userlbl = new GtkLabel("DB User");
			$this->user = new GtkEntry();
			$useralign = new GtkAlignment(0, 0.5, 0, 0);
			$useralign->add($userlbl);

			$table->attach($useralign, 0, 1, 2, 3);
			$table->attach($this->user, 1, 2, 2, 3);
		}

		//Row 4 - Password
		{
			$passlbl = new GtkLabel("DB Password");
			$this->pass = new GtkEntry();
			$passalign = new GtkAlignment(0, 0.5, 0, 0);
			$passalign->add($passlbl);

			$table->attach($passalign, 0, 1, 3, 4);
			$table->attach($this->pass, 1, 2, 3, 4);
		}


		return $table;
	}

	/**
	 * Checks what database drivers are available
	 * 
	 * @return array
	 */
	function get_available_dbs()
	{
		$drivers = array();

		// Check if there is pdo support
		if( ! function_exists('pdo_drivers'))
		{
			return FALSE;
		}

		// Add PDO drivers
		foreach(pdo_drivers() as $d)
		{
			// Skip sqlite2
			if($d === 'sqlite2')
			{
				continue;
			}

			$drivers[] = $d;
		}

		// Add firebird support, if exists
		if(function_exists('ibase_connect'))
		{
			$drivers[] = "Firebird";
		}

		sort($drivers);

		return $drivers;
	}
}

// End of add_db.php