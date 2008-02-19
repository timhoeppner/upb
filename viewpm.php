<?php
	// Private Messaging System
	// Add on to Ultimate PHP Board V2.0
	// Original PM Version (before _MANUAL_ upgrades): 2.0
	// Addon Created by J. Moore aka Rebles
	// Using textdb Version: 4.2.3
	require_once('./includes/class/func.class.php');
	require_once('./includes/inc/post.inc.php');
	$where = "<a href='pmsystem.php'>Messenger</a>";
	if ($_POST["action"] == "close") die('<html><body Onload="window.close()"> </body></html>');
	if ($tdb->is_logged_in() && isset($_GET["id"]) && is_numeric($_GET["id"]) && ($_GET["section"] == "inbox" || $_GET["section"] == "outbox")) {
		$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
		$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));
		$options = "";
		if ($_GET["section"] == "inbox") {
			$options = "<input type='submit' name='action' value='Reply' onclick='check_submit()'> <input type='submit' name='action' value='Delete' onclick='check_submit()'>  <input type='submit' name='action' value='Block User' onclick='check_submit()'>";
			$person = "Sender";
			$users_id = "from";
			$other = "to";
		} elseif($_GET["section"] == "outbox") {
			$person = "Sent to";
			$users_id = "to";
			$other = "from";
		} else {
			exitPage("
				<div class='alert'><div class='alert_text'>
				<strong>Warning!</strong></div><div style='padding:4px;'>Invalid Box.</div></div>", true);
		}
		if (isset($_POST["action"])) {
			if ($_POST["action"] == "Reply") {
				redirect("newpm.php?ref=viewpm.php&r_id=".$_GET["id"], "0");
			} elseif($_POST["action"] == "Delete") {
				$where .= " ".$_CONFIG["where_sep"]." Delete a PM";
				require_once('./includes/header.php');
				$PrivMsg->delete("CuBox", $_GET["id"]);
				echo "
					<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Notice:</div><div style='padding:4px;'>
					Sucessfully deleted PM.
					</div>
					</div>";
				require_once('./includes/footer.php');
				redirect("pmsystem.php?section=".$_GET["section"], 2);
			} elseif($_POST["action"] == "<< Last Message") {
				$pmRec = $PrivMsg->getLastRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"]));
				redirect($PHP_SELF."?section=".$_GET["section"]."&id=".$pmRec[0]["id"].$extra, "0");
				$_GET["id"] = $pmRec[0]["id"];
			} elseif($_POST["action"] == "Next Message >>") {
				$pmRec = $PrivMsg->getNextRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"]));
				redirect($PHP_SELF."?section=".$_GET["section"]."&id=".$pmRec[0]["id"].$extra, "0");
				$_GET["id"] = $pmRec[0]["id"];
			} elseif($_POST["action"] == "Block User") {
				redirect("pmblocklist.php?action=add&section=".$_GET["section"]."&ref=viewpm.php&id=".$_GET["id"], "0");
			} else {
				$where = "<a href='pmsystem.php'>Messenger</a>";
				exitPage("
					<div class='alert'><div class='alert_text'>
					<strong>Access Denied!</strong></div><div style='padding:4px;'>You should not be here (Invalid Action).</div></div>", true);
			}
			exit;
		}
		$next_disabled = "";
		$back_disabled = "";
		if (FALSE === ($PrivMsg->getNextRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"])))) $next_disabled = "DISABLED";
		if (FALSE === ($PrivMsg->getLastRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"])))) $back_disabled = "DISABLED";
		if (!isset($pmRec) || $pmRec == "" || !is_array($pmRec)) $pmRec = $PrivMsg->get("CuBox", $_GET["id"]);
		$where = "<a href='pmsystem.php'>Messenger</a> ".$_CONFIG["where_sep"]." <a href='pmsystem.php?section=".$_GET["section"]."'>".ucfirst($_GET["section"])."</a> ".$_CONFIG["where_sep"]." ".$pmRec[0]["subject"];
		require_once('./includes/header.php');
		$pm_navegate = "
		<form action='".$PHP_SELF."?section=".$_GET["section"]."&id=".$_GET["id"].$extra."' method='POST' onSubmit='submitonce(this)' enctype='multipart/form-data'>
		<table border='0' width='".$_CONFIG["table_width_main"]."' align='center'>
			<tr>
				<td align='right'>
					<input type='submit' name='action' value='<< Last Message' onclick='check_submit()' $back_disabled> $options
					<input type='submit' name='action' value='Next Message >>' onclick='check_submit()' $next_disabled></a></td>
			</tr>
		</table>
		</form>
		<br />";
		echo $pm_navegate;
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		$table_color = $table1;
		$table_font = $font1;
		$user = $tdb->get("users", $pmRec[0][$users_id]);
		if ($user[0]["sig"] != "") $user[0]["sig"] = "
			<div class='signature'>".UPBcoding(filterLanguage($user[0]["sig"], $_CONFIG["censor"]))."</div>";
		if (FALSE === mod_avatar::verify_avatar($user[0]['avatar'], $user[0]['avatar_hash'])) {
			$new_avatar = array();
			list($new_avatar['avatar_width'], $new_avatar['avatar_height']) = mod_avatar::calculate_dimensions($user[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
			$new_avatar['avatar_hash'] = mod_avatar::md5_file($user[0]['avatar']);
			$tdb->edit('users', $user[0]['id'], $new_avatar);
			$user[0] = array_merge($user[0], $new_avatar);
			unset($new_avatar);
		}
		$status_config = status($user);
		$status = $status_config['status'];
		$statuscolor = $status_config['statuscolor'];
    
		$message = format_text(filterLanguage(UPBcoding($pmRec[0]["message"]), $_CONFIG["censor"]));
		echo "
			<tr>
				<th style='width:15%;'><div class='post_name'><a href='profile.php?action=get&id=".$user[0]["id"]."'>".$user[0]["user_name"]."</a></div></th>
				<th style='width:85%;'><div style='float:left;'><img src='icon/".$pmRec[0]["icon"]."' alt='' title='' /></div><div style='line-height:15px;margin-right:4px;'>&nbsp;&nbsp;PM Sent: ".gmdate("M d, Y g:i:s a", user_date($pmRec["date"]))."</div></th>
			</tr>
				<tr>
					<td class='area_1' valign='top'>";
		if ($user[0]["avatar"] != "") echo "<br /><img src=\"".$user[0]["avatar"]."\" width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."' alt='' title='' /><br />";
		else echo "<br /><img src='images/avatars/noavatar.gif' alt='' title='' /><br />";
		echo "
						<div class='post_info'><span style='color:#".$statuscolor."'><strong>$status</strong></span></div>
						<div class='post_info'>
							<strong>Posts:</strong> ".$user[0]["posts"]."
							<br />
							<strong>Registered:</strong><br />
							".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
						</div>
						<br />
						<div class='post_info_extra'>";
		if ($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["icq"] != "") echo "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
		echo "</div></td>
					<td class='area_2' valign=top>
						<div style='padding:12px;margin-bottom:20px;'>$message</div>
						<div style='padding:12px;'>".$user[0]["sig"]."</div></td>
				</tr>
				<tr>
					<td class='footer_3a' colspan='2'>
						<div class='button_pro2'><a href='profile.php?action=get&id=".$pmRec["user_id"]."'>Profile</a></div>
						<div class='button_pro2'><a href='".$user[0]["url"]."' target = '_blank'>Homepage</a></div>
						<div class='button_pro2'><a href='email.php?id=".$pmRec["user_id"]."'>email ".$pmRec["user_name"]."</a></div></td>
				</tr>
			$skin_tablefooter";
		echo $pm_navegate;
	} else {
		require_once('./includes/header.php');
		if (FALSE === $tdb->is_logged_in()) echo "
			<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>You are not even logged in</div></div>";
		elseif($_GET["section"] != "inbox" && $_GET["section"] != "outbox") echo "
			<div class='alert'><div class='alert_text'>
			<strong>Warning!</strong></div><div style='padding:4px;'>Invalid Box.</div></div>";
		elseif(!isset($_GET["id"]) || !is_numeric($_GET["id"])) echo "
			<div class='alert'><div class='alert_text'>
			<strong>Warning!</strong></div><div style='padding:4px;'>Invalid ID.</div></div>";
		else echo "
			<div class='alert'><div class='alert_text'>
			<strong>Warning!</strong></div><div style='padding:4px;'>Unknown Error.</div></div>";
	}
	require_once('./includes/footer.php');
?>
