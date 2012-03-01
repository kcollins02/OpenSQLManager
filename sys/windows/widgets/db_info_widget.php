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

class DB_Info_Widget extends GtkTable {

	protected $conn, $dbtype, $host, $user, $pass, $database, $settings, $db_file, $port;

	public function __construct($conn='', $dbtype='', $host='localhost', 
		$user='', $pass='', $database='', $db_file=NULL, $port='')
	{
		parent::__construct();

		$this->settings =& Settings::get_instance();

		// Set up the form elements, with default values
		$this->conn = new GtkEntry();
		$this->host = new GtkEntry();
		$this->user = new GtkEntry();
		$this->pass = new GtkEntry();
		$this->port = new GtkEntry();
		$this->dbtype = GtkComboBox::new_text();
		$this->db_file = new GtkFileChooserButton("Select a database file",
				Gtk::FILE_CHOOSER_ACTION_OPEN);

		// Populate the available database types
		$db_types = $this->get_available_dbs();
		foreach($db_types as $t)
		{
			$this->dbtype->append_text($t);
		}

		// Populate the text fields with default values
		$this->conn->set_text($conn);
		$this->host->set_text($host);
		$this->user->set_text($user);
		$this->pass->set_text($pass);
		$this->db_file->set_filename($db_file);

		// Layout the table
		$this->layout();
	}

	/**
	 * Table layout
	 */
	public function layout()
	{
		// Reset defaults when changing db types
		$this->dbtype->connect_simple("changed", array($this, "change_db"));

		//Table attach 
		//$tbl->attach(left_start, right_stop, top_start, bottom_stop)

		// Placeholder vars for y values, so that rows can be
		// easily moved
		$y1 = -1;
		$y2 = 0;

		// Connection name
		{
			$this->_add_row("Connection name", 'conn', $y1, $y2);
		}

		// Database type
		{
			$dbtypelbl = new GtkLabel("Database Type");
			$typealign = new GtkAlignment(0, 0.5, 0, 0);
			$typealign->add($dbtypelbl);
			$this->attach($typealign, 0, 1, ++$y1, ++$y2);
			$this->attach($this->dbtype, 1, 2, $y1, $y2);
		}

		// DB File
		{
			$this->_add_row("Database File", 'db_file', $y1, $y2);
		}

		// Host
		{
			$this->_add_row("Host", 'host', $y1, $y2);
		}

		// Port
		{
			$this->_add_row("Port", 'port', $y1, $y2);
		}

		// Username
		{
			$this->_add_row("User", 'user', $y1, $y2);
		}

		// Password
		{
			$this->_add_row("Password", 'pass', $y1, $y2);
		}

		// Add connection button
		{
			$add_button = new GtkButton();
			$add_button->set_label("Add Connnection");
			$add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD, 
				Gtk::ICON_SIZE_SMALL_TOOLBAR));	
			$this->attach($add_button, 0, 1, ++$y1, ++$y2);
			$add_button->connect_simple("clicked", array($this, 'db_add'));	
		}

		// Test connection button
		{
			$test_button = new GtkButton();
			$test_button->set_label("Test Connection");
			$this->attach($test_button, 1, 2, $y1, $y2);
			$test_button->connect_simple("clicked", array($this, 'test_conn'));
		}
	}

	/**
	 * Simple helper function for adding a row to the GtkTable
	 * 
	 * @param GtkTable &$table
	 * @param string $label
	 * @param string $vname
	 * @param int &$y1
	 * @param int &$y2
	 */
	private function _add_row($label, $vname, &$y1, &$y2)
	{
		$lbl = 'lbl'.$vname;

		$this->$lbl = new GtkLabel($label);
		$lblalign = new GtkAlignment(0, 0.5, 0, 0);
		$lblalign->add($this->$lbl);

		$vname =& $this->$vname;

		$this->attach($lblalign, 0, 1, ++$y1, ++$y2);
		$this->attach($vname, 1, 2, $y1, $y2);
	}

	/**
	 * Set defaults for new database type
	 * 
	 * @return void
	 */
	public function change_db()
	{
		$new_db = $this->dbtype->get_active_text();

		// Reset
		$this->host->set_text('localhost');
		$this->db_file->set_filename(NULL);
		$this->port->show();
		$this->lblport->show();
		$this->db_file->hide();
		$this->lbldb_file->hide();
		$this->host->show();
		$this->lblhost->show();
		$this->user->set_text('');
		$this->pass->set_text('');
		$this->port->set_text('');

		switch($new_db)
		{
			default:
			break;

			case "MySQL":
				$this->user->set_text('root');
				$this->port->set_text(3306);
			break;

			case "PostgreSQL":
				$this->user->set_text('postgres');
				$this->port->set_text(5432);
			break;

			case "Firebird":
				$this->user->set_text('sysdba');
				$this->pass->set_text('masterkey');
				$this->lbldb_file->show();
				$this->db_file->show();
			break;

			case "ODBC":
				$this->lbldb_file->show();
				$this->db_file->show();
			break;

			case "SQLite":
				$this->lbldb_file->show();
				$this->db_file->show();
				$this->port->hide();
				$this->lblport->hide();
				$this->host->hide();
				$this->lblhost->hide();
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
			'port' => $this->port->get_text(),
			'file' => $this->db_file->get_filename(),
		);

		$this->settings->add_db($this->conn->get_text(), $data);

		// Pass to connection sidebar to update
		

		// Destroy the parent window
		$parent_window =& $this->get_parent_window();

		$parent_window->destroy();
	}

	/**
	 * Test a db connection, and display a popup with the result of the test
	 */
	public function test_conn()
	{
		$params = new stdClass();

		$params->type = strtolower($this->dbtype->get_active_text());
		$params->host = $this->host->get_text();
		$params->user = $this->user->get_text();
		$params->pass = $this->pass->get_text();
		$params->port = $this->port->get_text();
		$params->file = $this->db_file->get_filename();

		try
		{
			$db = new Query_Builder($params);
		}
		catch (PDOException $e)
		{
			$dialog = new GTKMessageDialog(
				NULL,
				Gtk::DIALOG_MODAL,
				Gtk::MESSAGE_ERROR,
				Gtk::BUTTONS_OK,
				"Error connecting to database: \n\n" . $e->getMessage()
			);
			$dialog->run();
			$dialog->destroy();

			return;
		}

		$dialog = new GTKMessageDialog(
			NULL,
			Gtk::DIALOG_MODAL,
			Gtk::MESSAGE_INFO,
			Gtk::BUTTONS_OK,
			"Successfully connected"
		);

		$dialog->run();
		$dialog->destroy();

		return;
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
}