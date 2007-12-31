<?php
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_reset_stats.php'>Reset Stats</a>";
	require_once("./includes/header.php");
	if (!$tdb->is_logged_in() || $_COOKIE["power_env"] != 3) exitPage("You must be logged into an administrator account!");
	//do something
	require_once('./includes/footer.php');
?>