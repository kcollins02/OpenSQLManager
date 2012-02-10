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

	var $conn, $dbtype, $host, $user, $pass, $database, $settings;
	
	function __construct()
	{
		parent::__construct();

		$this->settings = new Settings();

		$this->set_position(Gtk::WIN_POS_CENTER);
		$this->set_title("Add Database Connection");

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

		// Placeholder vars for y values, so that rows can be
		// easily moved
		$y1 = -1;
		$y2 = 0;

		// Connection name
		{
			$this->_add_row($table, "Connection name", $this->conn, $y1, $y2);
		}

		// Database type
		{
			$dbtypelbl = new GtkLabel("Database Type");
			$this->dbtype = GtkComboBox::new_text();
			$typealign = new GtkAlignment(0, 0.5, 0, 0);
			$typealign->add($dbtypelbl);

			foreach($db_types as $t)
			{
				$this->dbtype->append_text($t);
			}

			$table->attach($typealign, 0, 1, ++$y1, ++$y2);
			$table->attach($this->dbtype, 1, 2, $y1, $y2);

		}

		// Host
		{
			$this->_add_row($table, "DB Host", $this->host, $y1, $y2);
		}

		// Username
		{
			$this->_add_row($table, "DB User", $this->user, $y1, $y2);
		}

		// Password
		{
			$this->_add_row($table, "DB Password", $this->pass, $y1, $y2);
		}

		// Add connection button
		{
			$add_button = new GtkButton();
			$add_button->set_label("Add Connnection");
			$add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD, 
				Gtk::ICON_SIZE_SMALL_TOOLBAR));	
			$table->attach($add_button, 0, 3, ++$y1, ++$y2);
			$add_button->connect_simple("clicked", array($this, 'db_add'));	
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
			// Skip sqlite2 as opposed to sqlite3
			if($d === 'sqlite2')
			{
				continue;
			}

			// Replace default capitalization with something that looks better.
			$d = str_replace("sql", "SQL", $d);
			$d = str_ireplace("pg", "Postgre", $d);
			$d = str_ireplace("odbc", "ODBC", $d);
			$d = ucfirst($d);

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

	/**
	 * Simple helper function for adding a row to the GtkTable
	 * 
	 * @param GtkTable &$table
	 * @param string $label
	 * @param mixed &$vname
	 * @param int &$y1
	 * @param int &$y2
	 */
	private function _add_row(&$table, $label, &$vname, &$y1, &$y2)
	{
		$lbl = new GtkLabel($label);
		$vname = new GtkEntry();
		$lblalign = new GtkAlignment(0, 0.5, 0, 0);
		$lblalign->add($lbl);

		$table->attach($lblalign, 0, 1, ++$y1, ++$y2);
		$table->attach($vname, 1, 2, $y1, $y2);
	}

	/**
	 * Adds the database to the settings file
	 */
	function db_add()
	{
		$data = array(
			'type' => $this->dbtype->get_active_text(),
			'host' => $this->host->get_text(),
			'user' => $this->user->get_text(),
			'pass' => $this->pass->get_text(),
		);

		$this->settings->add_db($this->conn->get_text(), $data);

		// Destroy this window
		$this->destroy();
	}
}

// End of add_db.php