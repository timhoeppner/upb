<?php
if(basename($_SERVER['PHP_SELF']) == 'upb.initialize.php') die('This is a wrapper script!');
//Start session for all upb pages
session_start();

//php registered_global off
//prevent exploits for users who have registered globals on
foreach($GLOBALS["_GET"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_POST"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_COOKIE"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_SERVER"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
if(!empty($GLOBALS['_ENV'])) foreach($GLOBALS["_ENV"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_FILES"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
if(!empty($GLOBALS['_ENV'])) foreach($GLOBALS["_REQUEST"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}

require_once("./includes/class/error.class.php");
$errorHandler = &new errorhandler();
set_error_handler(array(&$errorHandler, 'add_error'));
error_reporting(E_ALL ^ E_NOTICE);

require_once("./config.php");
require_once("./includes/class/tdb.class.php");
require_once("./includes/class/config.class.php");
require_once("./includes/class/func.class.php");
require_once("./includes/class/mod_avatar.class.php");

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
    $_REGIST['custom_avatars'] = 2;
    $_REGIST['newuseravatars'] = 0;

    eval(file_get_contents(DB_DIR.'/constants.php'));
    require_once('./includes/whos_online.php');
}

//Move to constants.php
define("ALERT_MSG", "
	<div class='alert'>
		<div class='alert_text'><strong>__TITLE__</strong></div>
		<div style='padding:4px;'>__MSG__</div>
	</div>", true);
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
?>
