<?php
/**
 * UPB Upgrader
 *
 * This script will first backup and then attempt to install/update the UPB database
 * to the latest version. The aim of this script is to be as versatile as possible and
 * not rely on figuring out the current version installed.
 *
 * @author Tim Hoeppner <timhoeppner@gmail.com>
 *
 */

// TODO: Find an appropriate AJAX framework so we can keep this code nice and clean

include_once("./includes/api/usermanagement/authentication.class.php");
include_once("./includes/api/administration/backup.class.php");

// Once the upgrade is completed this will be the new version
define("UPB_NEW_VERSION", "2.2.7");

$auth = new UPB_Authentication;

// Only proceed if we have access to
if( $auth->access('a', "upgrade") == false )
{
	exitPage("...");
	exit;
}

// What should we do?
$action = $_GET["action"]; // TODO: perform any security checks on the query
switch($action)
{
	case "AJAX_backupDatabase":
		AJAX_backupDatabase();
		break;

	default:
		// TODO: Use Smarty template and display the upgrader HTML
		break;
}

// TODO: Create API's for all of the AJAX calls, this upgrader should be very
// 	simple in terms of implementation.

/**
 * Uses the user API to perform a backup
 */
function AJAX_backupDatabase()
{
	$backup = new UPB_DatabaseBackup;
}

/**
 * Checks over the config.php configuration file and verifies everything is in order
 */
function AJAX_validateRootConfig()
{
}

function AJAX_validateDbConfig()
{
}

function AJAX_validateDbCategories()
{
}

function AJAX_validateDbForums()
{
}

function AJAX_validateDbTopics()
{
}

function AJAX_validateDbPosts()
{
}

?>
