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
	
	function __construct()
	{
		parent::__construct();

		$this->set_title("OpenSQLManager - Add Database Connection");

		$this->resize(400, 300);

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

		$db_types = array(
			'MySQL',
			'PostgreSQL',
			'SQLite',
			'ODBC'
		);

		//Row 1 - Database type
		$dbtypelbl = new GtkLabel("Database Type");
		$dbtype = GtkComboBox::new_text();
		$align = new GtkAlignment(0, 0.5, 0, 0);
		$align->add($dbtypelbl);

		foreach($db_types as $t)
		{
			$dbtype->append_text($t);
		}

		$table->attach($align, 0,1,0,1);
		$table->attach($dbtype, 1,2,0,1);


		return $table;
	}
}

// End of add_db.php