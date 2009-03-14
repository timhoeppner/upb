<?php
	// Private Messaging System
	// Add on to Ultimate PHP Board V2.0
	// Original PM Version (before _MANUAL_ upgrades): 2.0
	// Addon Created by J. Moore aka Rebles
	// Using textdb Version: 4.2.3
	require_once('./includes/upb.initialize.php');
	if (!$tdb->is_logged_in()) die('You are not properly logged in.');
	if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) die('Invalid ID');
	require_once('./includes/header_simple.php');
	$where = 'PM:';
	$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
	$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));
	$pmRec = $PrivMsg->get("CuBox", $_GET["id"]);
	echo "
		<div class='simple_head' colspan='2'><div style='float:left;margin-right:4px;'><img src='icon/".$pmRec[0]["icon"]."'></div><div style='line-height:15px;'>".$pmRec[0]["subject"]."</div></td>";
	$table_color = "area_1";

	$user = $tdb->get("users", $pmRec[0]["from"]);
	if ($user[0]["sig"] != "") $user[0]["sig"] = "<div class='signature'>".UPBcoding(filterLanguage($user[0]["sig"], $_CONFIG))."</div>";

	$status_config = status($user);
	$status = $status_config['status'];
	$statuscolor = $status_config['statuscolor'];

  $message = format_text(filterLanguage(UPBcoding($pmRec[0]["message"]), $_CONFIG));
	echo "
		<table id='simple_table' style='background-color:#ffffff;' cellspacing='12'>
			<tr>
				<td class='simple_avarea'>";
	if ($user[0]["avatar"] != "") {
		echo "<br /> <img src=\"".$user[0]["avatar"]."\" border='0'><br />";
	}
	else echo "<br /><img src='images/avatars/noavatar.gif' alt='' title='' /><br />";
	//end avatar
	echo "
					<div class='simple_pinfo'><span style='color:#".$statuscolor."'><strong>$status</strong></span><br />
					Posts: ".$user[0]["posts"]."<br />
					Registered: <br />
					".gmdate("Y-m-d", user_date($user[0]["date_added"]))."</div>
					<div class='simple_pinfo_x'>";
	if ($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
	if ($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'></a>&nbsp;&nbsp;";
	if ($user[0]["icq"] != "") echo "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
	if ($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
	echo "</div></td>
				<td valign='top'>
					<div class='simple_date' style='float:left;'>Message Sent: ".gmdate("M d, Y g:i:s a", user_date($pmRec[0]["date"]))."</div>
					<div style='float:right;padding:4px;'><div class='simple_button'><a href='profile.php?action=get&id=".$user[0]["id"]."' target='_parent'>Profile</a></div>
					<div class='simple_button'><a href='".$user[0]["url"]."' target='_parent'>homepage</a></div>";
					if ($_CONFIG['email_mode'])
          echo "
					<div class='simple_button'><a href='email.php?id=".$user[0]["id"]."' target='_parent'>email ".$user[0]["user_name"]."</a></div>";
					echo "</div>
					<div style='clear:both;'></div>
					<div class='simple_content'><div style='margin-bottom:20px;'>$message</div>
		".$user[0]["sig"]."</div></td>
			</tr>
		</table>";
	require_once('./includes/footer_simple.php');
?>
