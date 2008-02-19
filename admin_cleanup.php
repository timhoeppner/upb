<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_cleanup.php'>Clean up (old search files)</a>";
	require_once('./includes/header.php');
	if ($tdb->is_logged_in() && $_COOKIE["power_env"] >= 3) {
		if ($_GET["action"] == "cleanup") {
			echo "Cleaning up...<br />";
			$dbdir = opendir(DB_DIR);
			while ($p = readdir($dbdir)) {
				if (substr($p, 0, 7) == "search_") {
					unlink(DB_DIR.$p);
					echo "$p<br />";
				}
			}
			echo "Done!";
			redirect($PHP_SELF, 2);
		} else {
			$files = 0;
			$size = 0;
			$dbdir = opendir(DB_DIR);
			while ($p = readdir($dbdir)) {
				if (substr($p, 0, 7) == "search_") {
					$files++;
					$size += filesize(DB_DIR.$p);
				}
			}
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
			echo "
			<tr>
				<th>Admin Panel Navigation</th>
			</tr>";
			echo "
			<tr>
				<td class='area_2' style='padding:20px;' valign='top'>";
			require_once("admin_navigation.php");
			echo "</td>
			</tr>";
			echoTableFooter($_CONFIG['skin_dir']);
		echoTableHeading("Cleaning...", $_CONFIG);
			echo "
			<tr>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;'>";
			if ($files > 0) echo "There is $files old search files. The files are taking up ".round($size / 1024, 2)." KB on the server.<br /><a href='admin_cleanup.php?action=cleanup'>Remove the old search files?</a>";
			else echo "There are no old search files.";
			echo "</td>
			</tr>";
			echoTableFooter($_CONFIG['skin_dir']);
		}
	} else {
		echo "
			<div class='alert'><div class='alert_text'>
			<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>";
	}
	require_once("./includes/footer.php");
?>
