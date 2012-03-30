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
 * Window controlling modifications of database connections
 */
class Edit_DB extends GtkWindow {

	/**
	 * Connection editing window
	 *
	 * @param object $db_params
	 */
	public function __construct($db_params)
	{
		parent::__construct();

		$this->set_position(Gtk::WIN_POS_CENTER);
		$this->set_title("Edit Database Connection");

		// Create the layout table
		$connection_form = new DB_Info_Widget($db_params);

		// Add the Vbox, and show the window
		$this->add($connection_form);
		$this->show_all();
	}
}
// End of edit_db.php