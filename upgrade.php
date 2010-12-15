<?php
/**
 * UPB Upgrader
 *
 * This script will first backup and then attempt to install/update the UPB database
 * to the latest version. The aim of this script is to be as versatile as possible and
 * not rely on figuring out the current version installed. This will hopefully be an
 * appropriate foundation for fully automatic updating in the future.
 *
 * @author Tim Hoeppner <timhoeppner@gmail.com>
 * @author ???
 *
 */


/*! Once the upgrade is completed this will be the new version */
define("UPB_NEW_VERSION", "2.2.7");


// User API includes
include_once("./includes/api/usermanagement/authentication.class.php");
include_once("./includes/api/administration/backup.class.php");

// Third party includes
include_once("./includes/thirdparty/xajax-0.5rc1/xajax_core/xajax.inc.php");

// Helper functions
include_once("./upgrade_tools.php");


$auth = new UPB_Authentication;

// Only proceed if we have access to
if( $auth->access("upgrade", 'a') == false )
{
	if( $auth->access("loggedin") == false )
	{
		// Display login page
	}
	else
	{
		exitPage("...");
		exit;
	}
}

// Register our AJAX calls with Xajax, all these functions can be found in upgrade_tools.php
$xajax = new xajax;

$xajax->registerFunction("AJAX_backupDatabase");
$xajax->registerFunction("AJAX_validateRootConfig");
$xajax->registerFunction("AJAX_validateDbConfig");
$xajax->registerFunction("AJAX_validateDbCategories");
$xajax->registerFunction("AJAX_validateDbForums");
$xajax->registerFunction("AJAX_validateDbTopics");
$xajax->registerFunction("AJAX_validateDbPosts");

// TODO: These need some more thought... may not want these directly accessable via AJAX
$xajax->registerFunction("AJAX_addTableField");
$xajax->registerFunction("AJAX_editTableField");
$xajax->registerFunction("AJAX_removeTableField");
$xajax->registerFunction("AJAX_populateTableField");
$xajax->registerFunction("AJAX_addRootConfigField");
$xajax->registerFunction("AJAX_editRootConfigField");
$xajax->registerFunction("AJAX_removeRootConfigField");

// Let Xajax handle the AJAX requests, anything after this statement will only be run
// when the page is first loaded.
$xajax->processRequest();

// Spit out the javascript, this should be placed between the <head></head> HTML tags
//$xajax->printJavascript();


?>
