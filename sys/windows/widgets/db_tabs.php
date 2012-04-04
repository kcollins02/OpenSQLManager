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
	 * Create tabs for database aspects
	 *
	 * @param Query_Builder $conn
	 * @return void
	 */
	public static function get_db_tabs(&$conn)
	{
		// Empty the tabs
		self::reset();

		// 'Databases' Tab
		{
			self::_add_tab($conn, 'Databases', 'Db Name', 'get_dbs');
		}

		// 'Tables' Tab
		{
			self::_add_tab($conn, 'Tables', 'Table Name', 'get_tables');
		}

		// 'Views' Tab
		{
			self::_add_tab($conn, 'Views', 'View Name', 'get_views');
		}


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
		$data = $model->get_value($iter, $i);

		if (empty($data))
		{
			return;
		}

		$col->set_visible(TRUE);
		$cell->set_property('text', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Remove current tabs
	 */
	public static function reset()
	{
		for($i=0, $max=self::$instance->get_n_pages(); $i <= $max; $i++)
		{
			self::$instance->remove_page($i);
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Simplify adding tabs to the Notebook object
	 *
	 * @param object $conn
	 * @param string $tab_name
	 * @param string $col_name
	 * @param string $method
	 * @return void
	 */
	private static function _add_tab(&$conn, $tab_name, $col_name, $method)
	{
		$tab = new Data_Grid();
		$tab_model = $tab->get_model();

		$tab_data = call_user_func_array(array($conn, $method), array());

		if($tab_data !== FALSE)
		{
			foreach($tab_data as $d)
			{
				$tab_model->append(null, array($d));
			}

			$cell_renderer = new GtkCellRendererText();
			$tab->insert_column_with_data_func(0, $col_name, $cell_renderer, array(self::$instance, 'add_data_col'));

			self::$instance->add_tab($tab_name, $tab);

		}
	}
}
// End of db_tabs.php
