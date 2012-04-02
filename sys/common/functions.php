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
// ! Global Functions
// --------------------------------------------------------------------------

/**
 * Convert an array to an object
 *
 * @param array $array
 * @return object
 */
function array_to_object($array)
{
	if (is_object($array))
	{
		return $array;
	}

	$obj = new StdClass();

	foreach($array as $k => $v)
	{
		$obj->$k = $v;
	}

	return $obj;
}

// --------------------------------------------------------------------------

/**
 * Create info dialog to retun an informational message
 *
 * @param  string $message
 * @return void
 */
function alert($message)
{
	$dialog = new GTKMessageDialog(
		NULL,
		Gtk::DIALOG_MODAL,
		Gtk::MESSAGE_INFO,
		Gtk::BUTTONS_OK,
		$message
	);

	$dialog->set_position(Gtk::WIN_POS_CENTER);
	$dialog->run();
	$dialog->destroy();
}

// --------------------------------------------------------------------------

/**
 * Create info dialog to retun an informational message
 *
 * @param string $message
 * @return void
 */
function error($message)
{
	$dialog = new GTKMessageDialog(
		NULL,
		Gtk::DIALOG_MODAL,
		Gtk::MESSAGE_ERROR,
		Gtk::BUTTONS_OK,
		$message
	);

	$dialog->set_position(Gtk::WIN_POS_CENTER);
	$dialog->run();
	$dialog->destroy();
}

// --------------------------------------------------------------------------

/**
 * Creates a binary confirmation dialog
 *
 * @param string $message
 * @return bool
 */
function confirm($message)
{
	$dialog = new GTKMessageDialog(
		NULL,
		Gtk::DIALOG_MODAL,
		Gtk::MESSAGE_QUESTION,
		Gtk::BUTTONS_YES_NO,
		$message
	);

	$dialog->set_position(Gtk::WIN_POS_CENTER);
	$answer = $dialog->run();
	$dialog->destroy();

	return ($answer === Gtk::RESPONSE_YES) ? TRUE : FALSE;
}

// --------------------------------------------------------------------------

/**
 * Display About menu with version information
 *
 * @return void
 */
function about()
{
	$dlg = new GtkAboutDialog();

	$dlg->set_program_name(PROGRAM_NAME);
	$dlg->set_version(VERSION);

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

// End of functions.php