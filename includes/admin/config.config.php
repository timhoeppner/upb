<?php
if(!defined('DB_DIR')) die('This script must be run under a wrapper');
if(!$tdb->is_logged_in() || $_COOKIE['power_env'] < 3) die ('You must be logged in as an administrator to execute this page');

?>
