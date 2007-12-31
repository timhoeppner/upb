<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_iplog.php'>Ip Address Logs</a>";
	require_once("./includes/header.php");
	if (!isset($_COOKIE["user_env"]) || !isset($_COOKIE["uniquekey_env"]) || !isset($_COOKIE["power_env"]) || !isset($_COOKIE["id_env"])) exitPage("
		<div class='alert'><div class='alert_text'>
		<strong>Access Denied!</strong></div><div style='padding:4px;'>You are not logged in.</div></div>
		<meta http-equiv='refresh' content='2;URL=login.php?ref=admin_iplog.php'>");
	if (!$tdb->is_logged_in() || $_COOKIE["power_env"] != 3) exitPage("
		<div class='alert'><div class='alert_text'>
		<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>");
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
	echo "
			<tr>
				<th>Admin Panel Navigation</th>
			</tr>";
	echo "
			<tr>
				<td class='area_2' style='padding:20px;' valign='top'>";
	include "admin_navigation.php";
	echo"</td>
			</tr>
		$skin_tablefooter";
		echoTableHeading("Recent IP address logs", $_CONFIG);
	echo "
			<tr>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_2' style='padding:12px;'>";
	$f = fopen(DB_DIR."/iplog", "r");
	echo "<br /><br /><br />
		";
	if (filesize(DB_DIR."/iplog") > (1024 * 10)) {
		fseek($f, filesize(DB_DIR."/iplog") - (1024 * 10));
	}
	$log = fread($f, (1024 * 10));
	fclose($f);
	echo "$log
			</tr>
		$skin_tablefooter";
	require_once("./includes/footer.php");
?>