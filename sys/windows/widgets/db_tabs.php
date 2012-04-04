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
			$dbs = new Data_Grid();
			$db_model = $dbs->get_model();
			$db_data = $conn->get_dbs();

			if($db_data !== FALSE)
			{
				foreach($db_data as $d)
				{
					$db_model->append(null, array($d));
				}

				$cell_renderer = new GtkCellRendererText();
				$dbs->insert_column_with_data_func(0, 'DB Name', $cell_renderer, array(self::$instance, 'add_data_col'));

				self::$instance->add_tab('Databases', $dbs);

			}


		}

		// 'Tables' Tab
		{
			$tables = new Data_Grid();
			$table_model = $tables->get_model();
			$table_data = $conn->get_tables();

			foreach($table_data as $t)
			{
				$table_model->append(null, array($t));
			}

			$cell_renderer = new GtkCellRendererText();
			$tables->insert_column_with_data_func(0, 'Table Name', $cell_renderer, array(self::$instance, 'add_data_col'));


			self::$instance->add_tab('Tables', $tables);
		}

		// 'Views' Tab
		{
			$views = new Data_grid();
			$view_model = $views->get_model();
			$view_data = $conn->get_views();

			if ($view_data !== FALSE)
			{
				foreach($view_data as $v)
				{
					$view_model->append(null, array($v));
				}

				$cell_renderer = new GtkCellRendererText();
				$views->insert_column_with_data_func(0, 'View Name', $cell_renderer, array(self::$instance, 'add_data_col'));

				self::$instance->add_tab('Views', $views);
			}
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

		print_r($data);

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
}
// End of db_tabs.php
