<?php
/**
 * UPB_Authentication is apart of the UPB user API and provides easy access to
 * the forum's user authentication functions.
 *
 * @author Tim Hoeppner <timhoeppner@gmail.com> (Design work and implementation)
 * @author ???
 *
 */

include_once(dirname( __FILE__ )."/../../../config.php");
include_once(dirname( __FILE__ )."/../../class/tdb.class.php");
include_once(dirname( __FILE__ )."/../../class/func.class.php");

if(!defined("DB_DIR"))
{
	die("The UPB_Authentication class cannot find the database directory and cannot function without it.");
}

class UPB_Authentication //extends the other user db class?
{
	var $_cache;
	//var $_tpl;
	var $func;

	function UPB_Authentication()
	{	
		// At the present state we will just use our standard authentication mechanism
		// but we need an instance of the function class for that.
		$this->func = new functions(DB_DIR."/", "main.tdb");
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
	 * @return true if access is granted, false if access is denied.
	 */
	function access($itemType, $accessType = "r", $itemId = 0)
	{
		// For all of the "Item Types" we need to determine if the user is actually logged in
		$loggedin = $this->func->is_logged_in();
		$access_granted = false;
		
		switch($itemType)
		{
			case "loggedin":
				$access_granted = $loggedin;
				break;
				
			case "upgrade":
				// Only an administrator has priveledge to upgrade the forum
				if($loggedin && $_COOKIE["power_env"] >= 3)
				{
					$access_granted = true;
				}
				break;
				
			case "config":
				$access_granted = $this->_configAccess($loggedin, $accessType, $itemId);
				break;
				
			case "category":
				$access_granted = $this->_categoryAccess($loggedin, $accessType, $itemId);
				break;
				
			case "forum":
				$access_granted = $this->_forumAccess($loggedin, $accessType, $itemId);
				break;
				
			case "topic":
				$access_granted = $this->_topicAccess($loggedin, $accessType, $itemId);
				break;
				
			case "post":
				$access_granted = $this->_postAccess($loggedin, $accessType, $itemId);
		}
		
		return $access_granted;
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
	
	
	
	function _configAccess($loggedin, $accessType, $itemId)
	{
	}
	
	function _categoryAccess($loggedin, $accessType, $itemId)
	{
	}
	
	function _forumAccess($loggedin, $accessType, $itemId)
	{
	}
	
	function _topicAccess($loggedin, $accessType, $itemId)
	{
	}
	
	function _postAccess($loggedin, $accessType, $itemId)
	{
	}
}
