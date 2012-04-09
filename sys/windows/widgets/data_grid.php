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
 *Class to simplify dealing with GtkTreeView
 */
class Data_Grid extends GtkTreeView {

	protected $model;

	/**
	 * Create the object
	 *
	 * @param object $model
	 */
	public function __construct($model = null)
	{
		$this->model = ( ! is_null($model))
			? $model
			: new GtkTreeStore(Gobject::TYPE_PHP_VALUE);

		parent::__construct($this->model);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get the value of the model for the current selection
	 *
	 * @param int pos
	 * @return mixed
	 */
	public function get($pos = 0)
	{
		// Get the selection object of the row
		$sel = $this->get_selection();

		// Get the model and iterator for the selected row
		list($model, $iter) = $sel->get_selected();

		// Get the data from the model
		return $model->get_value($iter, $pos);
	}

	// --------------------------------------------------------------------------

	/**
	 * Empty the model
	 */
	public function reset()
	{
		$this->model->clear();

		$cols = $this->get_columns();

		foreach($cols as $c)
		{
			$this->remove_column($c);
		}
	}
}
// End of data_grid.php