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
 * Widget managing saved database connections
 */
class Connection_Sidebar extends GtkVBox {

	protected $settings, $menu, $treeview;
	private static $instance;

	/**
	 * Return the current instance of the class
	 *
	 * @return Connection_Sidebar
	 */
	public static function &get_instance()
	{
		if( ! isset(self::$instance))
		{
			$name = __CLASS__;
			self::$instance = new $name();
		}

		return self::$instance;
	}

	// --------------------------------------------------------------------------

	/**
	 * Constructor method
	 */
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
			// Render the treeview
			$this->_render();

			// Set up context menu event
			$this->treeview->connect('button-press-event', array($this, 'on_button'));


			$selection = $this->treeview->get_selection();
			$selection->set_mode(GTK::SELECTION_SINGLE);
		}


		$this->pack_start($this->treeview);
		$this->pack_start($add_button, FALSE);
	}

	// --------------------------------------------------------------------------

	/**
	 * Renders the connection sidebar widget
	 */
	protected function _render()
	{
		// Create the treeview
		$this->treeview = (isset($this->treeview))
			? $this->treeview
			: new Data_Grid();

		$model = $this->treeview->get_model();

		// Add the existing connections to the model
		$db_conns = $this->settings->get_dbs();
		if( ! empty($db_conns))
		{
			foreach($db_conns as $name => $props)
			{
				if (is_array($props))
				{
					$props = array_to_object($props);
				}

				$db = $props;
				$db->name = $name;

				$iter = $model->append();
				$model->set($iter, 0, $db);
			}
		}

		// Icon column
		$cell_renderer = new GtkCellRendererPixbuf();
		$this->treeview->insert_column_with_data_func(0, 'Type', $cell_renderer, array($this, 'set_icon'));

		// Label column
		$cell_renderer = new GtkCellRendererText();
		$this->treeview->insert_column_with_data_func(1, 'Connection name', $cell_renderer, array($this, 'set_label'));
	}

	// --------------------------------------------------------------------------

	/**
	 * Sets the icon for the current db type
	 *
	 * @param GtkTreeView Column $col
	 * @param GtkCellRenderer $cell
	 * @param GtkTreeModel $this->model
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
	 * @param GtkTreeModel $this->model
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
		if ($event->button !== 3 || empty($view))
		{
			return;
		}

		// Right click
		if ($event->button == 3)
		{
			// get the row and column
			$path_array = $view->get_path_at_pos($event->x, $event->y);
        	$path = $path_array[0][0];
        	$col = $path_array[1];

			// Don't try to get values for an item that doesn't exist. Instead, return,
			// so that the program doesn't crash because someone thought it funny
			// to click on the empty area of the treeview.
			if(empty($col))
			{
				return;
			}
		}

		$this->menu = $this->conn_popup_menu($path, $event, $col, $path_array);
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates and displays a context menu for the selected connection
	 *
	 * @param  array $pos
	 * @param  object $event
	 * @param  object $col
	 * @param  array  $all
	 * @return void
	 */
	public function conn_popup_menu($pos, $event, $col, $all)
	{
		$this->menu = new GtkMenu();

		// Set up menu items
		{
			$edit = new GtkImageMenuItem('Edit Connection');
			$edit->set_image(GtkImage::new_from_stock(GTK::STOCK_EDIT, GTK::ICON_SIZE_MENU));
			$edit->connect_simple('activate', array($this, 'edit_connection'));

			$this->menu->append($edit);

			$remove = new GtkImageMenuItem('Delete Connection');
			$remove->set_image(GtkImage::new_from_stock(GTK::STOCK_CANCEL, Gtk::ICON_SIZE_MENU));
			$remove->connect_simple('activate', array($this, 'remove_connection'), $all);

			$this->menu->append($remove);
		}

		// Popup the menu
		$this->menu->show_all();
		$this->menu->popup();
	}

	// --------------------------------------------------------------------------

	/**
	 * Recreate sidebar widget to update connections
	 */
	public function refresh()
	{
		$this->treeview->reset();
		$this->_render();
	}

	// --------------------------------------------------------------------------

	/**
	 * Update the connection information for an existing connection
	 */
	public function edit_connection()
	{
		//@todo implement
	}

	// --------------------------------------------------------------------------

	/**
	 * Remove a connection from the connection manager
	 *
	 * @return  void
	 */
	public function remove_connection()
	{
		if ( ! confirm("Are you sure you want to remove this database connection?"))
		{
			return;
		}

		// Get the data from the model for the current selection
		$data = $this->treeview->get(0);

		// Remove the connection from the settings
		$this->settings->remove_db($data->name);

		// Refresh the sidebar
		$this->refresh();
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a connection to the connection manager
	 *
	 * @param  string $key
	 * @param  array $vals
	 * @return  void
	 */
	public function add_connection($key, $vals)
	{
		//@todo implement
		$model = $this->treeview->get_model();
	}

}
// End of connection_sidebar.php
