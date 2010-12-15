<?php
/**
 * UPB_Authentication is apart of the UPB user API and provides easy access to
 * the forum's user authentication functions.
 *
 * @author Tim Hoeppner <timhoeppner@gmail.com> (Design work and implementation)
 * @author ???
 *
 */

class UPB_Authentication //extends the other user db class?
{
	var $_cache;
	var $_tpl;

	function UPB_Authentication($tplClass)
	{
	}

	function login($username, $password, $duration = -1)
	{
	}

	function logoff()
	{
	}

	/**
	 *
	 * @param char $accessType - r: read, w: write, m: moderate, a: administrate
	 * @param string $itemType - "loggedin", "upgrade", "config", "category", "forum", "topic", "post"
	 *
	 */
	function access($itemType, $accessType = "r", $itemId = 0)
	{
	}

	/**
	 * Displays the login form
	 *
	 * @param string* $formData - If this is not null then the form data will be
	 * 	dumped here instead of stdout.
	 *
	 * @return void
	 */
	function displayLoginForm(&$formData = null)
	{
	}

	/**
	 * Validates the login form fields.
	 *
	 * @return bool true on success, false on failure.
	 */
	function validateLoginForm()
	{
	}
}
