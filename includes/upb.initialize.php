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

    eval(file_get_contents(DB_DIR.'/constants.php'));
    require_once('./includes/whos_online.php');
}
?>