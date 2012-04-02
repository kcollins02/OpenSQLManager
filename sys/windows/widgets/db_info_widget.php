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
 * Widget for adding / editing database connections
 */
class DB_Info_Widget extends GtkTable {

	/**
	 * Alias to Settings::get_instance
	 * @var Settings
	 */
	private $settings;

	/**
	 * Fields
	 */
	protected $conn,
		$conn_db,
		$dbtype,
		$host,
		$user,
		$pass,
		$database,
		$db_file,
		$port,
		$old_conn;

	/**
	 * Labels
	 */
	protected $lblconn,
		$lblconn_db,
		$lbldbtype,
		$lblhost,
		$lbluser,
		$lblpass,
		$lbldatabase,
		$lbldb_file,
		$lblport;

	/**
	 * No params = add, params = edit
	 *
	 * @param object $db
	 */
	public function __construct($db=null)
	{
		parent::__construct();

		$this->settings =& Settings::get_instance();

		if (is_null($db))
		{
			$db = new StdClass();
			$db->name = '';
			$db->host = '';
			$db->user = '';
			$db->pass = '';
			$db->port = '';
			$db->conn_db = '';
			$db->type = '';
			$db->file = NULL;
		}

		// Set up the form elements, with default values
		$this->conn = new GtkEntry();
		$this->host = new GtkEntry();
		$this->user = new GtkEntry();
		$this->pass = new GtkEntry();
		$this->port = new GtkEntry();
		$this->conn_db = new GtkEntry();
		$this->dbtype = GtkComboBox::new_text();
		$this->db_file = new GtkFileChooserButton("Select a database file",
				Gtk::FILE_CHOOSER_ACTION_OPEN);

		// Populate the available database types
		$db_types = $this->get_available_dbs();
		foreach($db_types as $t)
		{
			$this->dbtype->append_text($t);
		}
		$lower_db_types = array_map('strtolower', $db_types);

		// Populate the text fields with default values
		$this->conn->set_text($db->name);
		$this->host->set_text($db->host);
		$this->user->set_text($db->user);
		$this->pass->set_text($db->pass);
		$this->conn_db->set_text($db->conn_db);
		$this->port->set_text($db->port);

		// Layout the table
		$this->layout();

		// Select the proper db type if editing
		if ( ! empty($db->type))
		{
			// Set the old conn variable for editing
			$this->old_conn = $db->name;

			$dbtype = strtolower($db->type);

			// Set the db type based  on the current connection
			$this->dbtype->set_active(array_search($dbtype, $lower_db_types));

			// Set default path
			if ( ! empty($db->file))
			{
				$this->db_file->set_filename($db->file);
			}

			$this->pass->set_text($db->pass);
			$this->conn_db->set_text($db->conn_db);
		}
	}

	// --------------------------------------------------------------------------

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

		// First Db
		{
			$this->_add_row("Database Name", 'conn_db', $y1, $y2);
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

		// Add/Edit connection button
		{
			$conn_name = $this->conn->get_text();
			$caption = (empty($conn_name)) ? 'Add Connection' : 'Update Connection';

			$add_button = new GtkButton();
			$add_button->set_label($caption);

			( ! empty($conn_name))
				? $add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_SAVE,
					GTK::ICON_SIZE_SMALL_TOOLBAR))
				: $add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD,
					Gtk::ICON_SIZE_SMALL_TOOLBAR));

			$this->attach($add_button, 0, 1, ++$y1, ++$y2);

			if ( ! empty($conn_name))
			{
				$add_button->connect_simple("clicked", array($this, 'db_edit'));
			}
			else
			{
				$add_button->connect_simple("clicked", array($this, 'db_add'));
			}

		}

		// Test connection button
		{
			$test_button = new GtkButton();
			$test_button->set_label("Test Connection");
			$this->attach($test_button, 1, 2, $y1, $y2);
			$test_button->connect_simple("clicked", array($this, 'test_conn'));
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Set defaults for new database type
	 *
	 * @return void
	 */
	public function change_db()
	{
		$new_db = $this->dbtype->get_active_text();

		// Reset
		$this->host->set_text('127.0.0.1');
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
		$this->conn_db->set_text('');
		$this->conn_db->show();
		$this->lblconn_db->show();

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
				$this->conn_db->hide();
				$this->lblconn_db->hide();
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
				$this->conn_db->hide();
				$this->lblconn_db->hide();
			break;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Like change_db function, but save current values
	 *
	 * @return void
	 */
	public function set_db()
	{
		$dbtype = strtolower($this->dbtype->get_active_text());

		// Reset
		$this->db_file->hide();
		$this->lbldb_file->hide();

		switch($dbtype)
		{
			default:
			break;

			case "firebird":
				$this->lbldb_file->show();
				$this->db_file->show();
				$this->conn_db->hide();
				$this->lblconn_db->hide();
			break;

			case "odbc":
				$this->lbldb_file->show();
				$this->db_file->show();
			break;

			case "sqlite":
				$this->lbldb_file->show();
				$this->db_file->show();
				$this->port->hide();
				$this->lblport->hide();
				$this->host->hide();
				$this->lblhost->hide();
				$this->conn_db->hide();
				$this->lblconn_db->hide();
			break;
		}
	}

	// --------------------------------------------------------------------------

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
			'conn_db' => $this->conn_db->get_text(),
			'name' => $this->conn->get_text(),
		);

		$this->settings->add_db($data['name'], $data);

		// Pass to connection sidebar to update
		Connection_Sidebar::get_instance()->refresh();

		// Destroy the parent window
		$parent_window =& $this->get_parent_window();

		$parent_window->destroy();
	}

	// --------------------------------------------------------------------------

	/**
	 * Edit an existing database connection
	 */
	public function db_edit()
	{
		$data = array(
			'type' => strtolower($this->dbtype->get_active_text()),
			'host' => $this->host->get_text(),
			'user' => $this->user->get_text(),
			'pass' => $this->pass->get_text(),
			'port' => $this->port->get_text(),
			'file' => $this->db_file->get_filename(),
			'conn_db' => $this->conn_db->get_text(),
			'name' => $this->conn->get_text(),
		);

		if ($this->settings->edit_db($this->old_conn, $data))
		{
			// Let the user know the connection has been updated
			alert("Changes to database connection have been saved");
		}
		else
		{
			error("Error saving changes");
		}

		// Pass to connection sidebar to update
		Connection_Sidebar::get_instance()->refresh();

		// Destroy the parent window
		$parent_window =& $this->get_parent_window();

		$parent_window->destroy();
	}

	// --------------------------------------------------------------------------

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
		$params->database = $this->conn_db->get_text();

		// Return early if a db type isn't selected.
		// Better to bail out then crash because of
		// silly user input.
		if( empty($params->type))
		{
			return;
		}

		// Catch connection exceptions, and
		// display the error message to the
		// user so they can edit the db
		// parameters
		try
		{
			new Query_Builder($params);
		}
		catch (PDOException $e)
		{
			error("Error connecting to database: \n\n" . $e->getMessage());
			return;
		}

		// Successful Connection?
		// Tell the user!
		alert("Successfully Connected.");
	}

	// --------------------------------------------------------------------------

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

		$pdo_drivers = pdo_drivers();

		// Add PDO drivers
		foreach($pdo_drivers as $d)
		{
			// Skip sqlite2 as opposed to sqlite3
			if($d === 'sqlite2' && (in_array('sqlite', $pdo_drivers) || in_array('sqlite3', $pdo_drivers)))
			{
				continue;
			}

			// Use the ibase_functions over PDO::Firebird, at least for now
			if($d === 'firebird')
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
		if(function_exists('fbird_connect') && ! in_array('firebird', $pdo_drivers))
		{
			$drivers[] = "Firebird";
		}

		sort($drivers);

		return $drivers;
	}

	// --------------------------------------------------------------------------

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
}
// End of db_info_widget.php