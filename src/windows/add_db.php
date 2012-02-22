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

	var $conn, $dbtype, $host, $user, $pass, $database, $settings, $db_file;
	
	public function __construct($conn='', $dbtype='', $host='localhost', $user='', $pass='', $database='', $db_file=NULL)
	{
		parent::__construct();

		$this->settings =& Settings::get_instance();

		$this->set_position(Gtk::WIN_POS_CENTER);
		$this->set_title("Add Database Connection");

		// Set up the form elements, with default values
		$this->conn = new GtkEntry();
		$this->host = new GtkEntry();
		$this->user = new GtkEntry();
		$this->pass = new GtkEntry();
		$this->dbtype = GtkComboBox::new_text();
		$this->db_file = new GtkFileChooserButton("Select a database file",
				Gtk::FILE_CHOOSER_ACTION_OPEN);

		// Populate the available database types
		$db_types = $this->get_available_dbs();
		foreach($db_types as $t)
		{
			$this->dbtype->append_text($t);
		}

		// Reset defaults when changing db types
		$this->dbtype->connect_simple("changed", array($this, "change_db"));

		// Populate the text fields with default values
		$this->conn->set_text($conn);
		$this->host->set_text($host);
		$this->user->set_text($user);
		$this->pass->set_text($pass);
		$this->db_file->set_uri($db_file);

		// Create the layout table
		$this->table = new GtkTable();

		// Add the Vbox, and show the window
		$this->_layout();
		$this->add($this->table);
		$this->show_all();
	}

	/**
	 * Window layout
	 * 
	 * @return GtkVBox
	 */
	private function _layout()
	{
		//Table attach 
		//$tbl->attach(left_start, right_stop, top_start, bottom_stop)

		// Placeholder vars for y values, so that rows can be
		// easily moved
		$y1 = -1;
		$y2 = 0;

		// Connection name
		{
			$this->_add_row("Connection name", $this->conn, $y1, $y2);
		}

		// Database type
		{
			$dbtypelbl = new GtkLabel("Database Type");
			$typealign = new GtkAlignment(0, 0.5, 0, 0);
			$typealign->add($dbtypelbl);
			$this->table->attach($typealign, 0, 1, ++$y1, ++$y2);
			$this->table->attach($this->dbtype, 1, 2, $y1, $y2);
		}

		// DB File
		{
			$this->_add_row("Database File", $this->db_file, $y1, $y2);
		}

		// Host
		{
			$this->_add_row("Host", $this->host, $y1, $y2);
		}

		// Username
		{
			$this->_add_row("User", $this->user, $y1, $y2);
		}

		// Password
		{
			$this->_add_row("Password", $this->pass, $y1, $y2);
		}

		// Add connection button
		{
			$add_button = new GtkButton();
			$add_button->set_label("Add Connnection");
			$add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD, 
				Gtk::ICON_SIZE_SMALL_TOOLBAR));	
			$this->table->attach($add_button, 0, 3, ++$y1, ++$y2);
			$add_button->connect_simple("clicked", array($this, 'db_add'));	
		}
	}

	/**
	 * Checks what database drivers are available
	 * 
	 * @return array
	 */
	public function get_available_dbs()
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
	private function _add_row($label, &$vname, &$y1, &$y2)
	{
		$lbl = new GtkLabel($label);
		//$vname =& $this->{$vname};
		$lblalign = new GtkAlignment(0, 0.5, 0, 0);
		$lblalign->add($lbl);

		$this->table->attach($lblalign, 0, 1, ++$y1, ++$y2);
		$this->table->attach($vname, 1, 2, $y1, $y2);
	}

	/**
	 * Set defaults for new database type
	 * 
	 * @param  GtkComboBox $combo 
	 * @return void
	 */
	public function change_db($combo)
	{
		$new_db = $this->dbtype->get_active_text();

		switch($new_db)
		{
			default:
				$this->host->set_text('localhost');
				$this->db_file->set_uri(NULL);

			case "MySQL":
				$this->user->set_text('root');
				$this->pass->set_text('');
			break;

			case "PostgreSQL":
				$this->user->set_text('');
				$this->pass->set_text('');
			break;

			case "Firebird":
				$this->user->set_text('sysdba');
				$this->pass->set_text('masterkey');
			break;

			case "ODBC":
			case "SQLite":
				$this->user->set_text('');
				$this->pass->set_text('');
			break;

		}
	}

	/**
	 * Adds the database to the settings file
	 */
	public function db_add()
	{
		$data = array(
			'type' => strtolower($this->dbtype->get_active_text()),
			'host' => $this->host->get_text(),
			'user' => $this->user->get_text(),
			'pass' => $this->pass->get_text(),
			'file' => $this->db_file->get_uri(),
		);

		$this->settings->add_db($this->conn->get_text(), $data);

		// Destroy this window
		$this->destroy();
	}
}

// End of add_db.php