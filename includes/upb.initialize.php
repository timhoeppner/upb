<?php
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
if(basename($_SERVER['PHP_SELF']) == 'upb.initialize.php') die('This is a wrapper script!');
//Start session for all upb pages
session_start();

//php registered_global off
//prevent exploits for users who have registered globals on
foreach($GLOBALS["_GET"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
    if (((strpos($varname, 'id') !== FALSE) || $varname == 'page') && (!ctype_digit($varvalue) && !empty($varvalue))) die('Possible XSS attack detected');
}
reset($GLOBALS["_GET"]);

foreach($GLOBALS["_POST"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
reset($GLOBALS["_POST"]);
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

define("ALERT_MSG", "
	<div class='alert'>
		<div class='alert_text'><strong>__TITLE__</strong></div>
		<div style='padding:4px;'>__MSG__</div>
	</div><br>", true);
define("CONFIRM_MSG", "
	<div class='alert_confirm'>
		<div class='alert_confirm_text'><strong>__TITLE__</strong></div>
		<div style='padding:4px;'>__MSG__</div>
	</div><br>", true);
define('ALERT_GENERIC_TITLE', 'Attention:', true);
define('ALERT_GENERIC_MSG', 'If you feel you\'ve reached this message in error, contact the forum administrator or web master.', true);
define('MINIMAL_BODY_HEADER', "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>MyUPB</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />", true);
define('MINIMAL_BODY_FOOTER', "
	<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
		&nbsp;&nbsp;&copy;2002 - ".date("Y",time())."</div>
</div>
</body>
</html>", true);

require_once("./includes/class/error.class.php");
$errorHandler = &new errorhandler();
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
if (UPB_VERSION != "2.2.2" && (FALSE === strpos($_SERVER['PHP_SELF'], 'update'))) die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Update Available:', str_replace('__MSG__', 'An update has not been run yet.  Please follow the directions in the readme file to run it to continue.', ALERT_MSG)).MINIMAL_BODY_FOOTER);

//Check to see if User is banned
if(file_exists(DB_DIR.'/banneduser.dat')) {
    $banned_addresses = explode("\n", file_get_contents(DB_DIR.'/banneduser.dat'));
    if((isset($_COOKIE["user_env"]) && in_array($_COOKIE["user_env"], $banned_addresses)) || in_array($_SERVER['REMOTE_ADDR'], $banned_addresses))
		die(MINIMAL_BODY_HEADER.str_replace('__TITLE__', 'Notice:', str_replace('__MSG__', 'You have been banned from this bulletin board.<br>'.ALERT_GENERIC_MSG, ALERT_MSG)).MINIMAL_BODY_FOOTER);
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