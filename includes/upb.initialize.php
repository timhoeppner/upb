<?php
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
if(basename($_SERVER['PHP_SELF']) == 'upb.initialize.php') die('This is a wrapper script!');
//Start session for all upb pages
session_start();

//prevents some problems with IIS Servers
if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
	if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != "") {
		$_SERVER['REQUEST_URI'] .= "?".$_SERVER['QUERY_STRING'];
	}
}

//php registered_global off
//prevent exploits for users who have registered globals on
foreach($GLOBALS["_GET"] as $varname => $varvalue) {
	if(isset($$varname)) unset($$varname);
	if (((strpos($varname, 'id') !== FALSE) || $varname == 'page') && (!ctype_digit($varvalue) && !empty($varvalue))) die('Possible XSS attack detected');
	$_GET[$varname] = RemoveXSS($varvalue);
}
reset($GLOBALS["_GET"]);
foreach($GLOBALS["_POST"] as $varname => $varvalue) {
	$_POST[$varname] = RemoveXSS($varvalue);
	if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_POST"]);
//var_dump($GLOBALS["_POST"]);
foreach($GLOBALS["_COOKIE"] as $varname => $varvalue) {
	if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_COOKIE"]);
foreach($GLOBALS["_SERVER"] as $varname => $varvalue) {
	if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_SERVER"]);
if(!empty($GLOBALS['_ENV'])) foreach($GLOBALS["_ENV"] as $varname => $varvalue) {
	if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_SERVER"]);
foreach($GLOBALS["_FILES"] as $varname => $varvalue) {
	if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_FILES"]);
if(!empty($GLOBALS['_ENV'])) {
	foreach($GLOBALS["_REQUEST"] as $varname => $varvalue) {
		if(isset($$varname)) unset($$varname);
	}
	reset($GLOBALS["_REQUEST"]);
}

// PHP5.1.0 new timezone req's
// This has to be here, because some date() functions are called before user is verified
// This makes UPB's timezone functions obsolete (but we need them for backwards compadibility with PHP4)
if(function_exists("date_default_timezone_set")) {
	$timezone = "Europe/London";
	if(isset($_COOKIE["timezone"]) && $_COOKIE["timezone"] != "")
	$timezone = timezone_name_from_abbr("", (int)$_COOKIE["timezone"]*3600, 0);
	date_default_timezone_set($timezone);

}

require_once("./includes/inc/defines.inc.php");
require_once("./includes/class/error.class.php");
$errorHandler = new errorhandler();
//set_error_handler(array(&$errorHandler, 'add_error'));
error_reporting(E_ALL ^ E_NOTICE);

//Verify that we're not using a ver. 1 database, otherwise prompt the admin to run the updater
if (!file_exists("./db/main.tdb") && file_exists("./db/config2.php")) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Update Available:', str_replace('__MSG__', 'An update has not been run yet.  Please follow the directions in the readme file to run it to continue.', ALERT_MSG)).MINIMAL_BODY_FOOTER);
if (file_exists("config.php")) {
	require_once("config.php");
}
//Verify that a database exists, otherwise prompt the admin to run the installer
if (!defined('DB_DIR')) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', ALERT_GENERIC_TITLE, str_replace('__MSG__', 'The installer has not been run it.  Please <a href="install.php">run this</a> to continue.', ALERT_MSG)).MINIMAL_BODY_FOOTER);
if(!is_dir(DB_DIR)) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Fatal:', str_replace('__MSG__', 'The data directory is missing.', ALERT_MSG)).MINIMAL_BODY_FOOTER);
if (UPB_VERSION != "2.2.7" && (FALSE === strpos($_SERVER['PHP_SELF'], 'update') && FALSE === strpos($_SERVER['PHP_SELF'], 'upgrade'))) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Update Available:', str_replace('__MSG__', 'An update has not been run yet.  Please follow the directions in the readme file to run it to continue.', ALERT_MSG)).MINIMAL_BODY_FOOTER);

//Check to see if User is banned
if(file_exists(DB_DIR.'/banneduser.dat')) {
	$banned_addresses = explode("\n", file_get_contents(DB_DIR.'/banneduser.dat'));
	if((isset($_COOKIE["user_env"]) && in_array($_COOKIE["user_env"], $banned_addresses)) || in_array($_SERVER['REMOTE_ADDR'], $banned_addresses))
	die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Notice:', str_replace('__MSG__', 'You have been banned from this bulletin board.<br>'.ALERT_GENERIC_MSG, ALERT_MSG)).MINIMAL_BODY_FOOTER);
}

//PHP5 ≥ 5.1 throws a E_WARNING if this isn't called
if(function_exists("date_default_timezone_set")) {
	date_default_timezone_set("Europe/London");
}

require_once("./includes/class/tdb.class.php");
require_once("./includes/class/config.class.php");
require_once("./includes/class/func.class.php");

require_once('./includes/inc/post.inc.php');
require_once("./includes/inc/func.inc.php");
require_once("./includes/inc/date.inc.php");
require_once("./includes/inc/encode.inc.php");
require_once("./includes/inc/privmsg.inc.php");
//whos_online.php included at last line

//installation precausion
//globalize resource $tdb to prevent multiple occurances
if(!file_exists(DB_DIR."/main.tdb"))
{
	echo "File missing";
	die();
}

if(file_exists(DB_DIR."/main.tdb")) {
	$tdb = new functions(DB_DIR.'/', 'main.tdb');
	//$tdb->define_error_handler(array(&$errorHandler, 'add_error'));
	$tdb->setFp('users', 'members');
	$tdb->setFp('forums', 'forums');
	$tdb->setFp('cats', 'categories');
	$tdb->setFp('getpass', 'getpass');
	$tdb->setFp("uploads", "uploads");

	//UPB's main Vars
	$config_tdb = new configSettings();
	$config_tdb->setFp("config", "config");
	$config_tdb->setFp("ext_config", "ext_config");

	$_CONFIG = $config_tdb->getVars("config");

	$_REGISTER = $config_tdb->getVars("regist");
	$_REGIST = &$_REGISTER;
	$_STATUS = $config_tdb->getVars("status");

	//integrate into admin_config
	$_CONFIG["where_sep"] = "<b>&gt;</b>";
	$_CONFIG["table_sep"] = "<b>::</b>";

	define('SKIN_DIR', $_CONFIG['skin_dir'], true);

	if (!defined('DB_DIR')) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Fatal Error:', str_replace('__MSG__', 'The DB_DIR constant is undefined.<br>Please go to <a href="http://myupb.com/" target="_blank">MyUPB.com</a> for support.', ALERT_MSG)).MINIMAL_BODY_FOOTER);
	if (!is_array($_CONFIG)) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Fatal Error:', str_replace('__MSG__', 'Unable to correctly access UPB\'s configuration.<br>Please go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.', ALERT_MSG)).MINIMAL_BODY_FOOTER);
	if (SKIN_DIR == '' || !defined('SKIN_DIR')) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Fatal Error:', str_replace('__MSG__', 'The SKIN_DIR constant is undefined.<br>This may be an indication UPB was unable to correctly access its configuration.<br>Please go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.', ALERT_MSG)).MINIMAL_BODY_FOOTER);

	require_once('./includes/whos_online.php');
}
?>