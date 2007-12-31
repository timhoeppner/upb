<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	require_once("./includes/class/posts.class.php");
	$post_tdb = new posts(DB_DIR, "posts.tdb");
	$post_tdb->setFp("topics", $_GET["id"]."_topics");
	$post_tdb->setFp("posts", $_GET["id"]);
	if ($_GET["t"] == 1) $where = "Delete a Topic";
	else $where = "Delete a Post";
	require_once("./includes/header.php");
	if ($tdb->is_logged_in() === false) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>You are not logged in, therefore unable to perform this action.</div></div>");
	if (!isset($_GET["id"]) || !isset($_GET["t_id"]) || ($_GET["t"] == 0 && !isset($_GET["p_id"]))) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Not enough information to perform this function.</div></div>");
	if ($_COOKIE["power_env"] < 2 && $_GET['t'] != 0) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>You do not have enough power to delete this topic.</div></div>");
	if ($_GET['action'] != "delete") exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Unknown Action.  Seek Administrative Help.</div></div>");
	$tRec = $post_tdb->get("topics", $_GET["t_id"]);
	if ($_GET["t"] == 1) {
		if ($_POST["verify"] == "Ok") {
			if (isset($_GET["t_id"])) {
				$p_ids = explode(",", $tRec[0]["p_ids"]);
				$subtract_user_post_count = array();
				foreach($p_ids as $p_id) {
					$pRec = $post_tdb->get('posts', $p_id);
					if (!isset($subtract_user_post_count[$pRec[0]['user_id']])) {
						$subtract_user_post_count[$pRec[0]['user_id']] = 1;
					}
					else $subtract_user_post_count[$pRec[0]['user_id']]++;
					$post_tdb->delete("posts", $p_id, false);
				}
				while (list($user_id, $post_count) = each($subtract_user_post_count)) {
					$user = $tdb->get('users', $user_id);
					$tdb->edit('users', $user_id, array('posts' => (int)$user[0]['posts'] - $post_count));
				}
				$post_tdb->delete("topics", $_GET["t_id"]);
				$fRec = $tdb->get("forums", $_GET["id"]);
				$tdb->edit("forums", $_GET["id"], array("topics" => ((int)$fRec[0]["topics"] - 1), "posts" => ((int)$fRec[0]["posts"] - ($tRec[0]["replies"] + 1))));
				echo "
					<div class='alert_confirm'><div class='alert_confirm_text'>
					<strong>Redirecting:</strong></div><div style='padding:4px;'>Successfully deleted \"".$tRec[0]["subject"]."\"(T_ID:".$_GET["t_id"].")<br />from ".$fRec[0]["forum"]." (F_ID:".$_GET["id"].").</div></div>";
				redirect("viewforum.php?id=".$_GET["id"], "2");
				exit;
			}
		} elseif($_POST["verify"] == "Cancel") {
			if ($_GET["ref"] == "") $_GET["ref"] = "viewtopic.php";
			redirect($_GET["ref"]."?id=".$_GET["id"]."&t_id=".$_GET["t_id"], "0");
		} else {
			ok_cancel($_SERVER['PHP_SELF']."?action=".$_GET['action']."&t=".$_GET["t"]."&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&ref=".$_GET["ref"], "Are you sure you want to delete a topic?");
		}
	} elseif($_GET["t"] == 0) {
		$p_ids = explode(",", $tRec[0]["p_ids"]);
		if ($_GET["p_id"] == $p_ids[0]) {
			echo "The topic is dependent on the first post, therefore you cannot delete it. The topic must be deleted in order to remove this post.";
		}
		$pRec = $post_tdb->get("posts", $_GET["p_id"]);
		if (!(($pRec[0]["user_id"] == $_COOKIE["id_env"]) || ($_COOKIE["power_env"] >= 2))) exitPage("You are not authorized to delete this post.");
		$pRec[0]["message"] = format_text($pRec[0]["message"]);
		if ($_POST["verify"] == "Ok") {
			$update_topic = array("replies" => ((int)$tRec[0]["replies"] - 1), "p_ids" => $tRec[0]["p_ids"]);
			if (($key = array_search($_GET["p_id"], $p_ids)) !== FALSE) {
				$update_topic = array("replies" => ((int)$tRec[0]["replies"] - 1), "p_ids" => $tRec[0]["p_ids"]);
				if ($key == (count($p_ids) - 1)) {
					//last post, update last_post of topic
					if (FALSE === ($last_post = $post_tdb->get('posts', $p_ids[($key - 1)])))
					$update_topic = array_merge($update_topic, array('last_post' => $last_post[0]['date'], 'user_name' => $last_post[0]['user_name'], $user_id => $last_post[0]['user_id']));
				}
				unset($p_ids[$key]);
				$update_topic["p_ids"] = implode(",", $p_ids);
				$fRec = $tdb->get("forums", $_GET["id"]);
				$tdb->edit("forums", $_GET["id"], array("posts" => ((int)$fRec[0]["posts"] - 1)));
				$post_tdb->edit("topics", $_GET["t_id"], $update_topic);
				$post_tdb->delete("posts", $_GET["p_id"]);
				if ($pRec[0]['user_id'] != 0) {
					$user = $tdb->get('users', $pRec[0]['user_id']);
					$tdb->edit('users', $pRec[0]['user_id'], array('posts' => (int)$user[0]['posts'] - 1));
				}
				echo "Successfully deleted post, redirecting...";
				require_once("./includes/footer.php");
				redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"], "2");
				exit;
			}
			else echo '<b><font color="red">Fatal Error: </font></b> $p_id not found in the topic record.  The topic was not deleted';
		} elseif($_POST["verify"] == "Cancel") redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"], 0);
		else
		{
			echo "";
		echoTableHeading("Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec[0]["date"]))."", $_CONFIG);
			$table_color = $table1;
			$table_font = $font1;
			$user = $tdb->get("users", $pRec[0]["user_id"]);
			if ($user[0]["sig"] != "") $user[0]["sig"] = "<div class='signature'>".UPBcoding(filterLanguage($user[0]["sig"], $_CONFIG["censor"]))."</div>";
			if ($user[0]["level"] == '1') {
				$statuscolor = $userColor;
				if ($user[0]["posts"] >= $member_post1) $status = $member_status1;
				elseif($user[0]["posts"] >= $member_post2) $status = $member_status2;
				elseif($user[0]["posts"] >= $member_post3) $status = $member_status3;
				elseif($user[0]["posts"] >= $member_post4) $status = $member_status4;
				elseif($user[0]["posts"] >= $member_post5) $status = $member_status5;
			} elseif($user[0]["level"] == '2') {
				$statuscolor = $modColor;
				if ($user[0]["posts"] >= $mod_post1) $status = $mod_status1;
				elseif($user[0]["posts"] >= $mod_post2) $status = $mod_status2;
				elseif($user[0]["posts"] >= $mod_post3) $status = $mod_status3;
				elseif($user[0]["posts"] >= $mod_post4) $status = $mod_status4;
				elseif($user[0]["posts"] >= $mod_post5) $status = $mod_status5;
			} elseif($user[0]["level"] == '3') {
				$statuscolor = $adminColor;
				if ($user[0]["posts"] >= $admin_post1) $status = $admin_status1;
				elseif($user[0]["posts"] >= $admin_post2) $status = $admin_status2;
				elseif($user[0]["posts"] >= $admin_post3) $status = $admin_status3;
				elseif($user[0]["posts"] >= $admin_post4) $status = $admin_status4;
				elseif($user[0]["posts"] >= $admin_post5) $status = $admin_status5;
			} else {
				$status = 'Member';
				$statuscolor = $membercolor;
			}
			if ((($_COOKIE["id_env"] == $pRec[0]["user_id"])) || ($_COOKIE["power_env"] >= "2")) {
				$edit = "<a href='editpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec[0]["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_edit.JPG' alt='Edit Post' border='0'></a>";
				$delete = "<a href='delete.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec[0]["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_delete.JPG' border='0'></a>";
			} else {
				$edit = "";
				$delete = "";
			}
			if ($_COOKIE["power_env"] >= "1") $quote = "<a href='quotepost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec[0]["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_quote.JPG' border='0' alt='Quote'></a>";
			else $quote = "";
			if (isset($_COOKIE["id_env"]) && $pRec[0]["user_id"] != $_COOKIE["id_env"]) $pm = "<a href='newpm.php?to=".$pRec[0]["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/sendpm.jpg' border='0' alt='Send User a PM'></a>";
			else $pm = "";
			$msg = UPBcoding(filterLanguage($pRec[0]["message"], $_CONFIG["censor"]));
			echo "
			<tr>
				<th><div class='post_name'><a href='profile.php?action=get&id=".$pRec[0]["user_id"]."'>".$pRec[0]["user_name"]."</a></div></th>
				<th><div style='float:left;'><img src='icon/".$pRec[0]["icon"]."'></div><div align='right'>$delete $edit $quote $reply</div></th>
			</tr>
			<tr>
				<td class='$table_color' valign=top width=15%>";
			//add avatar
			if (@$user[0]["avatar"] != "") {
				$set_width = $avatar_width;
				$set_height = $avatar_height;
				if (@fclose(@fopen($user[0]["avatar"], "r"))) {
					list($width, $height, $type, $attr) = getimagesize($user[0]["avatar"]);
					if ($width > $height) {
						$set_height = round(($avatar_width * $height) / $width);
					} elseif($width < $height) {
						$set_width = round(($avatar_height * $width) / $height);
					} elseif($width <= $avatar_width && $height <= $avatar_height) {
						$set_width = $width;
						$set_height = $height;
					}
				}
				echo "<br /><img src=\"".$user[0]["avatar"]."\" width='$set_width' height='$set_height' alt='' title=''><br />";
			}
			//end avatar
			echo "<div class='post_info'><span style='color:#$statuscolor'><strong>$status</strong></span></div>
				<div class='post_info'>
				<strong>Posts:</strong> ".$user[0]["posts"]."
				<br />
				<strong>Registered:</strong>
				<br />
				".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
				</div>
				<br />
				<div class='post_info_extra'>";
			if ($pm != "") echo $pm."<br />";
			if ($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
			if ($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'>&nbsp;&nbsp;";
			if ($user[0]["icq"] != "") echo "&nbsp;<a href='icq.php?action=get&id=".$pRec[0]["user_id"]."'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
			if ($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
			echo"
				</font></font></td>
				<td bgcolor='$table_color' valign=top><table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%'>
					<tr valign='top'> <td height='99%'><table cellspacing=0 cellpadding=0 width='100%' border=0><tbody>
					<tr>
						<td width='45%'><font face=verdana color=#ffffff size=2><img src='icon/".$pRec[0]["icon"]."'> <font color='black' size='$font_s'>Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec[0]["date"]))."&nbsp;&nbsp;&nbsp;&nbsp; </font></font></td>
						<td width='55%' valign='middle'> <div align='right'><font face=verdana color=#ffffff size=2><font color=yellow size=1>$edit $delete $quote <a href='profile.php?action=get&id=".$pRec[0]["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_profile.JPG' alt='Profile' border='0'></a></font> <a href='".$user[0]["url"]."' target = '_blank'><img src='".$_CONFIG["skin_dir"]."/icons/pb_www.JPG' border='0' alt='homepage'></a> <a href='email.php?id=".$pRec[0]["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_email.JPG' border='0' alt='email ".$pRec[0]["user_name"]."'></a></font></div></td>
					</tr>
				</table>
				<table width=100% cellspacing=0 cellpadding=0>
					<tr>
						<td height=1 bgcolor='$divider'></td>
					</tr>
				</table><br /><font size='$font_m' face='$font_face'>$msg</font></td>
			</tr>
			<tr valign='bottom'>
				<td height='1%'><br />".$user[0]["sig"]."</td>
			</tr>";
			echo "
		$skin_tablefooter";
			ok_cancel("delete.php?action=".$_GET["action"]."&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$_GET["p_id"], "Delete Post?");
		}
	} else {
		echo "<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Corrupt Information.  Seek Administrative Help.</div></div>";
	}
	require_once("./includes/footer.php");
?>