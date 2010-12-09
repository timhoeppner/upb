<?php
// TODO: insert proper header block

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
	 * @param string $itemType - "config??", "category", "forum", "topic", "post"
	 *
	 */
	function access($accessType = "r", $itemType, $itemId = 0)
	{
	}

	function displayLoginForm($defaultValues)
	{
	}

	function validateLoginForm()
	{
	}
}
