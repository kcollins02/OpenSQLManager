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

	private $main_vbox, $main_hbox;

	/**
	 * Create and display the main window on startup
	 */
	function __construct()
	{
		parent::__construct();
		
		//Layout the interface
		$this->_main_layout();
	}

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
		$main_vbox  = new GTKVBox();

		// Main Hbox for columns
		$main_hbox = new GTKHBox();

		// Add the menubar
		$main_vbox->pack_start($this->_create_menu(), FALSE, FALSE);

		// Add the main interface area hbox
		$main_vbox->pack_start($main_hbox, FALSE, FALSE);

		// Add the Vbox, and show the window
		$this->add($main_vbox);
		$this->show_all();
	}

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

	/**
	 * Display About menu with version information
	 */
	function about()
	{
		$dlg = new GtkAboutDialog();
		$dlg->set_transient_for($this);

		$dlg->set_program_name($this->get_title());
		$dlg->set_version('0.0.1pre');

		$dlg->set_copyright("Copyright (c) ".date('Y')." Timothy J. Warren");

		$dlg->set_website('https://github.com/aviat4ion/OpenSQLManager');

		$dlg->run();

		$dlg->destroy();
	}

	/** 
	 * Quits the GTK loop
	 */
	function quit()
	{
		Gtk::main_quit();
	}	

}

// End of main.php