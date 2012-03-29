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
			: new GtkTreeStore(Gobject::TYPE_PHP_VALUE, Gobject::TYPE_PHP_VALUE);

		parent::__construct($this->model);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get the value of the cell at the provided coordinate array
	 *
	 * @param array $coord
	 * @return mixed
	 */
	public function get(array $coord)
	{
		// @todo implement
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the value of the cell at the provided coordinate array
	 *
	 * @param array $coord
	 * @param mixed $val
	 */
	public function set(array $coord, $val)
	{
		// @todo implement
	}

	// --------------------------------------------------------------------------

	/**
	 * Empty the model
	 */
	public function reset($model = null)
	{
		$this->model->clear();
	}
}
// End of data_grid.php