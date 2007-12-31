<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_badwords.php'>Manage Badwords</a>";
	require_once('./includes/header.php');
	if (!(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"]))) {
		echo "
			<div class='alert'><div class='alert_text'>
			<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not logged in!</div></div>";
		redirect("login.php?ref=admin_badwords.php", 2);
	}
	if (!($tdb->is_logged_in() && $_COOKIE["power_env"] == 3)) exitPage("
		<div class='alert'><div class='alert_text'>
		<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>");
	if ($_GET["action"] == "delete" && $_GET["word"] != "") {
		if ($_POST["verify"] == "Ok") {
			echo "
				<div class='alert_confirm'>
				<div class='alert_confirm_text'>
				<strong>Redirecting:</div><div style='padding:4px;'>
				deleting bad word...
				";
			$words = explode("\n", file_get_contents(DB_DIR."/badwords.dat"));
			if (($index = array_search($_GET["word"], $words)) !== FALSE) unset($words[$index]);
			$f = fopen(DB_DIR."/badwords.dat", 'w');
			fwrite($f, implode("\n", $words));
			fclose($f);
			echo "Done!</div>
				</div>";
			redirect("admin_badwords.php", 1);
		} elseif($_POST["verify"] == "Cancel") redirect("admin_badwords.php", 1);
		else ok_cancel("admin_badwords.php?action=delete&word=".$_GET["word"], "Are you sure you want to delete <b>".$_GET["word"]."</b> from the badword list?");
	} elseif($_GET["action"] == "addnew") {
		if ($_POST["newword"] != "") {
			echo "
				<div class='alert_confirm'>
				<div class='alert_confirm_text'>
				<strong>Redirecting:</div><div style='padding:4px;'>
				adding new word...";
			if (filesize(DB_DIR.'/badwords.dat') > 0) {
				$pre = file_get_contents(DB_DIR."/badwords.dat");
			}
			else $pre = '';
			$f = fopen(DB_DIR."/badwords.dat", 'w');
			fwrite($f, $pre."\n".stripslashes(trim($_POST['newword'])));
			fclose($f);
			echo "Done!</div>
				</div>";
			redirect("admin_badwords.php", 1);
		} else {
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
			</tr>
				$skin_tablefooter";
			echo "<form action='admin_badwords.php?action=addnew' method=POST>";
		echoTableHeading("Adding a badword", $_CONFIG);
			echo "
			<tr>
				<th colspan='2'>Add the word you wish to be censored below</th>
			</tr>
			<tr>
				<td class='area_1' style='width:35%;padding:12px;'><strong>New badword</strong></td>
				<td class='area_2'><input type='text' name='newword' size='20'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='submit' value='Add'></td>
			</tr>
		$skin_tablefooter
	</form>";
		}
	} else {
		$words = explode("\n", file_get_contents(DB_DIR."/badwords.dat"));
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
			</tr>
		$skin_tablefooter";
		echo "
			<div id='tabstyle_2'>
			<ul>
			<li><a href='admin_badwords.php?action=addnew' title='Add a new word?'><span>Add a new word?</span></a></li>
			</ul>
			</div>
			<div style='clear:both;'></div>";
		echoTableHeading("Censored words", $_CONFIG);
		echo "
			<tr>
				<th style='width:35%;'>Word</th>
				<th>Option</th>
			</tr>";
		if (trim($words[0]) == "") {
			echo "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='2'>No words found</td>
			</tr>";
		} else {
			for($i = 0; $i < count($words); $i++) {
				echo "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>$words[$i]</strong></td>
				<td class='area_2' style='padding:8px;'><a href='admin_badwords.php?action=delete&word=$words[$i]'>Delete</a></td>
			</tr>";
			}
		}
		echo "
		$skin_tablefooter";
	}
	require_once("./includes/footer.php");
?>