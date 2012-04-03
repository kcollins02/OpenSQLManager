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
 * Tabbed Container for database properties
 */
class DB_tabs extends GTKNotebook {

	/**
	 * Current Tab Widget object
	 * @var DB_Tabs
	 */
	private static $instance;

	/**
	 * Return the db tabs object if it exists, or create and return
	 *
	 * @return DB_tabs
	 */
	public static function &get_instance()
	{
		if (empty(self::$instance))
		{
			self::$instance = new DB_tabs();
		}

		return self::$instance;
	}

	// --------------------------------------------------------------------------

	/**
	 * Create the object
	 */
	public function __construct()
	{
		parent::__construct();

		// Move the tab bar to the bottom
		//$this->set_tab_pos(Gtk::POS_BOTTOM);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a new tab with the provided label
	 *
	 * @param string $label
	 * @param GObject $widget
	 * @return void
	 */
	public function add_tab($label, $widget = NULL)
	{
		if (is_null($widget))
		{
			$widget = new Data_Grid();
		}

		$this->append_page($widget, new GtkLabel($label));
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates a new instance of this class, and destroys the existing
	 * instance
	 *
	 * @return DB_tabs
	 */
	public static function reset()
	{
		self::$instance = new DB_tabs();
		return self::get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * Create tabs for database aspects
	 *
	 * @param Query_Builder $conn
	 * @return void
	 */
	public static function get_db_tabs(&$conn)
	{
		$tables = new Data_Grid();
		$table_model = $tables->get_model();
		$table_data = $conn->get_tables();

		foreach($table_data as $t)
		{
			$table_model->append(null, array($t));
			//$table_model->set($iter, 0, $t);
		}

		$cell_renderer = new GtkCellRendererText();
		$tables->insert_column_with_data_func(0, 'Table Name', $cell_renderer, array(self::$instance, 'add_data_col'));

		self::$instance->add_tab('Tables', $tables);

		self::$instance->show_all();

	}

	// --------------------------------------------------------------------------

	/**
	 * Adds a column of data to the model
	 *
	 * @param GtkTreeViewColumn $col
	 * @param GtkCellRenderer $cell
	 * @param GtkTreeModel $model
	 * @param GtkTreeIter $iter
	 * @param int $i
	 * @return void
	 */
	public function add_data_col($col, $cell, $model, $iter, $i=0)
	{
		$col->set_visible(TRUE);
		$data = $model->get_value($iter, $i);
		$cell->set_property('text', $data);
	}
}
// End of db_tabs.php