<?php
if(!defined('DB_DIR')) die('This script must be run under a wrapper');
if(!$tdb->is_logged_in() || $_COOKIE['power_env'] < 3) die ('You must be logged in as an administrator to execute this page');

if($_POST['avatar_width'] != $_CONFIG['avatar_width'] || $_POST['avatar_height'] != $_CONFIG['avatar_height']) {
    if(strlen($_POST['avatar_width']) > 3) $_POST['avatar_width'] = 999;
    if(strlen($_POST['avatar_height']) > 3) $_POST['avatar_height'] = 999;
}
?>
