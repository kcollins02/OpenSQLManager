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
 * Main Window Class
 *
 * Creates and displays the main interface window
 */
class Main extends GtkWindow {

	private $settings, $connection_sidebar;

	/**
	 * Create and display the main window on startup
	 */
	public function __construct()
	{
		parent::__construct();

		$this->settings =& Settings::get_instance();
		

		if ( ! is_null($this->settings->width) && ! is_null($this->settings->height))
		{
			//Resize to the last size
			$this->set_size_request($this->settings->width, $this->settings->height);
		}
		else
		{
			//Resize to a sane size
			$this->set_size_request(640, 480);
		}

		if (! is_null($this->settings->position))
		{
			$this->move($this->settings->position[0], $this->settings->position[1]);
		}
		else
		{
			$this->set_position(Gtk::WIN_POS_CENTER);
		}

		//Layout the interface
		$this->_main_layout();
	}

	// --------------------------------------------------------------------------

	/**
	 * Some cleanup for when the main window is closed
	 */
	public function __destruct()
	{
		// Save the Window position
		$this->settings->position = $this->get_position();

		list($width, $height) = $this->get_size();

		// Save the Window hegiht
		$this->settings->height = $height;

		// Save the Window width
		$this->settings->width = $width;

	}

	// --------------------------------------------------------------------------

	/**
	 * Display About menu with version information
	 */
	public function about()
	{
		$dlg = new GtkAboutDialog();
		$dlg->set_transient_for($this);

		$dlg->set_program_name($this->get_title());
		$dlg->set_version('0.1.0pre');

		$dlg->set_copyright("Copyright (c) ".date('Y')." Timothy J. Warren");

		$dlg->set_website('https://github.com/aviat4ion/OpenSQLManager');
		$dlg->set_website_label('Fork on Github');

		$dlg->set_license(file_get_contents(BASE_DIR . "/LICENSE"));

		$dlg->set_authors(array(
			'Timothy J. Warren',
			//'Nathan Dupuie',
		));

		/*$dlg->set_artists(array(
			'Nathan Dupuie',
		));*/

		$dlg->run();

		$dlg->destroy();
	}

	// --------------------------------------------------------------------------

	/** 
	 * Quits the GTK loop
	 */
	public function quit()
	{
		Gtk::main_quit();
	}

	// --------------------------------------------------------------------------

	/**
	 * Layout the main interface
	 * 
	 * Create menus, hboxes, vboxs and other widgets
	 */
	private function _main_layout()
	{
		$this->set_title('OpenSQLManager');
		
		// Quit when this window is closed
		$this->connect_simple('destroy', array('gtk', 'main_quit'));

		// Main Vbox that everything is contained in
		$main_vbox  = new GTKVBox(FALSE, 5);

		// Main Hpaned for columns
		$hpane = new GTKHPaned();

		// Add the menubar
		$main_vbox->pack_start($this->_create_menu(), FALSE, FALSE);

		// Add the main interface area hbox
		$main_vbox->pack_start($hpane);

		$scrolled_win = new GtkScrolledWindow();
    	$scrolled_win->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
    	$scrolled_win->add(new DataGrid());

    	// Add the connection sidebar
    	$this->connection_sidebar =& Connection_Sidebar::get_instance();

		// Add the left column to the hpane
		$hpane->pack1($this->connection_sidebar, FALSE);
		$hpane->pack2($scrolled_win);

		// Add the Vbox, and show the window
		$this->add($main_vbox);
		$this->show_all();
	}

	// --------------------------------------------------------------------------

	/**
	 * Create the menu for the program
	 *
	 * @return GtkMenuBar
	 */
	private function _create_menu()
	{
		//Menu Bar
		$menu_bar = new GtkMenuBar();

		//Menu Bar Top Items
		$top_file_menu = new GtkMenuItem('_File');
		$top_help_menu = new GtkMenuItem('_Help');

		//Add sub Menus to top items
		$file_menu = new GtkMenu();
		$top_file_menu->set_submenu($file_menu);
		$help_menu = new GtkMenu();
		$top_help_menu->set_submenu($help_menu);

		
		//File Menu
		{
			//Set up the open item
			//$open = new GtkImageMenuItem(GTK::STOCK_OPEN);
			//$file_menu->append($open);

			//Set up the quit item
			$quit = new GtkImageMenuItem(GTK::STOCK_QUIT);
			$quit->connect_simple('activate', array($this, 'quit'));
			$file_menu->append($quit);

			// Add the top level menu to the menubar
			$menu_bar->append($top_file_menu);
		}

		//Help Menu
		{
			//Set up the about item
			$about = new GtkImageMenuItem(GTK::STOCK_ABOUT);
			$about->connect_simple('activate', array($this, 'about'));
			$help_menu->append($about);

			// Add the top level menu to the menubar
			$menu_bar->append($top_help_menu);
		}

		
		return $menu_bar;
	}	
}

// End of main.php