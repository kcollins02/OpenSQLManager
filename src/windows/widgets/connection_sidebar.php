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

class Connection_Sidebar extends GtkVBox {

	protected $settings, $menu;

	public function __construct()
	{
		parent::__construct();

		$this->settings =& Settings::get_instance();

		$dblabel = new GtkLabel('Database Connections');
		$dblabel->set_alignment(0,0);

		$add_button = new GtkButton();
		$add_button->set_label("New Connnection");
		$add_button->set_image(GTKImage::new_from_stock(GTK::STOCK_ADD, Gtk::ICON_SIZE_SMALL_TOOLBAR));

		$add_button->connect_simple('clicked', array($this, 'new_conn'));

		$this->pack_start($dblabel, FALSE, FALSE);

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

			$treeview->connect('button-press-event', array(&$this, 'on_button'));


			$selection = $treeview->get_selection();
			$selection->set_mode(GTK::SELECTION_SINGLE);
		}
		

		$this->pack_start($treeview);
		$this->pack_start($add_button, FALSE);
	}

	// --------------------------------------------------------------------------

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
			// Load an empty image if the db image doesn't exist
			$img = new GtkImage();
			$cell->set_property('pixbuf', $img->get_pixbuf());
		}
	}

	// --------------------------------------------------------------------------

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

	// --------------------------------------------------------------------------

	/**
	 * Event for mouse clicks on connection sidebar
	 * 
	 * @param  GtkTreeView $view
	 * @param  $event
	 * @return void
	 */
	public function on_button($view, $event)
	{
		// Right click
		if($event->button == 3)
		{
			// get the row and column
			$path_array = $view->get_path_at_pos($event->x, $event->y);
			$path = $path_array[0][0];
			$col = $path_array[1];
			$col_title = $col->get_title();
		}

		$this->menu = $this->conn_popup_menu($path, $col_title, $event);
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates a context menu for the selected connection
	 * 
	 * @param  [type] $pos   [description]
	 * @param  [type] $title [description]
	 * @param  [type] $event [description]
	 * @return [type]
	 */
	public function conn_popup_menu($pos, $title, $event)
	{
		$this->menu = new GtkMenu();

		// Set up menu items
		
		$this->menu->show_all();
		$this->menu->popup();
	}

	// --------------------------------------------------------------------------

	/**
	 * Remove a connection from the connection manager
	 */
	public function remove_connection()
	{

	}

	// --------------------------------------------------------------------------

	/**
	 * Add a connection to the connection manager
	 */
	public function add_connection()
	{

	}

}
// End of connection_sidebar.php
