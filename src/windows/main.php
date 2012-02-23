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

	private $settings, $model;

	/**
	 * Create and display the main window on startup
	 */
	public function __construct()
	{
		parent::__construct();
		
		//Resize to a sane size
		$this->resize(640, 480);

		$this->set_position(Gtk::WIN_POS_CENTER);
		$this->settings =& Settings::get_instance();

		//Layout the interface
		$this->_main_layout();
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
		$main_vbox  = new GTKVBox();

		// Main Hpaned for columns
		$hpane = new GTKHPaned();

		// Add the menubar
		$main_vbox->pack_start($this->_create_menu(), FALSE, FALSE);

		// Add the main interface area hbox
		$main_vbox->pack_start($hpane);

		$scrolled_win = new GtkScrolledWindow();
    	$scrolled_win->set_policy( Gtk::POLICY_AUTOMATIC, Gtk::POLICY_ALWAYS);
    	$scrolled_win->add(new DataGrid());


		// Add the left column to the hpane
		$hpane->pack1($this->_connection_sidebar(), FALSE);
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

	// --------------------------------------------------------------------------

	/**
	 * Lays out the left sidebar in the main window
	 *
	 * @return GtkVbox
	 */
	private function _connection_sidebar()
	{
		$dblabel = new GtkLabel('Database Connections');
		$dblabel->set_alignment(0,0);

		$add_button = new GtkButton();
		$add_button->set_label("New Connnection");
		$add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD, Gtk::ICON_SIZE_SMALL_TOOLBAR));

		$add_button->connect_simple('clicked', array($this, 'new_conn'));

		$conn_vbox = new GtkVBox();

		$conn_vbox->pack_start($dblabel, FALSE, FALSE);

		// Treeview to show database connections
		{
			// Create a Storage object for connection list
			$model = new GtkListStore(GObject::TYPE_PHP_VALUE, GObject::TYPE_STRING);

			// Add the existing connections to the model
			$db_conns = $this->settings->get_dbs();
			if( ! empty($db_conns))
			{
				foreach($db_conns as $name => $props)
				{
					$db = $props;
					$db->name = $name;

					$iter = $model->append();
					$model->set($iter, 0, $db);
				}
			}
			
			// Initialize the treeview with the data
			$treeview = new GtkTreeView($model);

			// Icon column
			$cell_renderer = new GtkCellRendererPixbuf();
			$treeview->insert_column_with_data_func(0, 'Type', $cell_renderer, array(&$this, 'set_icon'));

			// Label column
			$cell_renderer = new GtkCellRendererText();
			$treeview->insert_column_with_data_func(1, 'Connection name', $cell_renderer, array(&$this, 'set_label'));


			$selection = $treeview->get_selection();
			$selection->set_mode(GTK::SELECTION_SINGLE);
		}
		

		$conn_vbox->pack_start($treeview);
		$conn_vbox->pack_start($add_button, FALSE);
		
		return $conn_vbox;
	}

	/**
	 * Sets the icon for the current db type
	 * 
	 * @param GtkTreeView Column $col   
	 * @param GtkCellRenderer $cell
	 * @param GtkTreeModel $model
	 * @param GtkTreeIter $iter
	 */
	public function set_icon($col, $cell, $model, $iter)
	{
		$col->set_reorderable(TRUE);
		$info = $model->get_value($iter, 0);
		$db_type = strtolower($info->type);
		$img_file = BASE_DIR."/images/{$db_type}-logo-32.png";

		if(is_file($img_file))
		{
			$cell->set_property('pixbuf', GdkPixbuf::new_from_file($img_file));
		}
		else
		{
			$img = new GtkImage();
			$cell->set_property('pixbuf', $img->get_pixbuf());
		}
	}

	/**
	 * Sets the label of the current db connection
	 * 
	 * @param GtkTreeViewColumn $col
	 * @param GtkCellRenderer $cell
	 * @param GtkTreeModel $model
	 * @param GtkTreeIter $iter
	 */
	public function set_label($col, $cell, $model, $iter)
	{
		$col->set_reorderable(TRUE);
		$info = $model->get_value($iter, 0);
		$cell->set_property('text', $info->name);
	}

	/**
	 * Redraws the data area based on the which connection is selected
	 * 
	 * @param $selection
	 */
	private function _render_selected($selection)
	{
		
	}

	// --------------------------------------------------------------------------

	/**
	 * Returns window for creating a new database connection
	 * 
	 * @return Add_DB object
	 */
	public function new_conn()
	{
		return new Add_DB();
	}
		
}

// End of main.php