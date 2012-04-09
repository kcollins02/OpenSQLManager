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
			self::_add_tab($conn, 'Databases', 'Db Name', 'get_dbs', array(), array(
				'row-activated' => array(self::$instance, '_switch_db'),
			));
		}

		// 'Schemas' Tab
		{
			self::_add_tab($conn, 'Schemas', 'Schema Name', 'get_schemas');
		}

		// 'Tables' Tab
		{
			self::_add_tab($conn, 'Tables', 'Table Name', 'get_tables');
		}

		// 'System Tables' Tab
		{
			self::_add_tab($conn, 'System Tables', 'Table Name', 'get_system_tables');
		}

		// 'Views' Tab
		{
			self::_add_tab($conn, 'Views', 'View Name', 'get_views');
		}

		// 'Sequences' Tab
		{
			self::_add_tab($conn, 'Sequences', 'Sequence Name', 'get_sequences');
		}

		// 'Triggers' Tab
		{
			self::_add_row_tab($conn, 'Triggers','get_triggers');
		}

		// 'Procedures' Tab
		{
			self::_add_tab($conn, 'Procedures', 'Procedure name', 'get_procedures');
		}

		// 'Functions' Tab
		{
			self::_add_row_tab($conn, 'Functions', 'get_functions');
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
		for($i=self::$instance->get_n_pages(); $i >= 0; $i--)
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
	private static function _add_tab(&$conn, $tab_name, $col_name, $method, $params=array(), $events=array())
	{
		$tab = new Data_Grid();
		$tab_model = $tab->get_model();

		$tab_data = call_user_func_array(array($conn, $method), $params);

		if ($tab_data !== FALSE)
		{
			foreach($tab_data as $d)
			{
				$tab_model->append(null, array($d));
			}

			$cell_renderer = new GtkCellRendererText();
			$tab->insert_column_with_data_func(0, $col_name, $cell_renderer, array(self::$instance, 'add_data_col'));

			if ( ! empty($events))
			{
				foreach($events as $name => $method)
				{
					$tab->connect($name, $method);
				}
			}

			self::$instance->add_tab($tab_name, $tab);

		}

		return;
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a multidimensional array to a tab
	 *
	 * @param object $conn
	 * @param string $tab_name
	 * @param string $method
	 * @return void
	 */
	private static function _add_row_tab(&$conn, $tab_name, $method)
	{
		$tab_data = call_user_func_array(array($conn, $method), array());

		if ( ! empty($tab_data))
		{
			$tab_model = new StdClass();

			$cols = array_keys($tab_data[0]);

			// Add columns to model
			$model_args = array_fill(0, count($cols), Gobject::TYPE_PHP_VALUE);
			$eval_string = '$tab_model = new GTKTreeStore('.implode(',', $model_args).');';

			// Shame, shame, but how else?
			eval($eval_string);
			$tab= new Data_Grid($tab_model);

			// Set the data in the model
			for($i=0, $c = count($tab_data); $i < $c; $i++)
			{
				// Add a row
				$row = $tab_model->insert($i);

				$j = -1;
				$vals = array($row);
				foreach($tab_data[$i] as $v)
				{
					$vals[] = ++$j;
					$vals[] = $v;
				}

				call_user_func_array(array($tab_model, 'set'), $vals);
			}

			// Add columns to view
			foreach($cols as $i => $c)
			{
				$renderer = new GtkCellRendererText();
				$tab->insert_column_with_data_func($i, $c, $renderer, array(self::$instance, 'add_data_col'), $i);
			}

			self::$instance->add_tab($tab_name, $tab);
		}

		return;
	}

	/**
	 * Connects to a different database than the one currently in use
	 *
	 * @param type $view
	 * @param type $path
	 * @param type $column
	 * @param type $data
	 */
	public function _switch_db($view, $path, $column, $data=array())
	{
		// Get the selected database
		$new_db = $view->get(0);

		// Get existing connections
		$conns = DB_REG::get_connections();

		// Get connection info for existing connections
		$conn_info = array();

		foreach($conns as $c)
		{
			$conn_info[$c] = Settings::get_instance()->get_db($c);
		}

		// @todo figure out how to single out the current db connection


	}
}
// End of db_tabs.php
