<?php
// TODO: insert proper header block

class UPB_Registration
{
	function UPB_Registration()
	{
	}

	/**
	 * Checks if this is the first user and automatically makes the
	 * first user an Admin. This should solve the issue of quitting
	 * the installer early and not inserting an admin account.
	 *
	 * @param $userdata - UPB_User
	 *
	 * @return TRUE on success, FALSE on failure.
	 */
	function register($userdata)
	{
	}

	function displayRegisterForm($defaultUserData)
	{
	}

	function validateRegisterForm()
	{
	}
}
?>
