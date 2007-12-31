<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	// Ultimate PHP Board Topic display
	require_once('./includes/class/func.class.php');
	require_once('./includes/class/posts.class.php');
	$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
	//check if the id exists
	if (!(is_numeric($_GET["id"]) && $posts_tdb->isTable($_GET["id"]))) exitPage("Forum does not exist.", true);
	if (FALSE === ($fRec = $tdb->get("forums", $_GET["id"]))) exitPage("Forum does not exist.", true);
	$posts_tdb->setFp("topics", $_GET["id"]."_topics");
	$posts_tdb->setFp("posts", $_GET["id"]);
	if (FALSE === ($tRec = $posts_tdb->get("topics", $_GET["t_id"]))) exitPage("Invalid Topic.", true);
	$posts_tdb->edit("topics", $_GET["t_id"], array("views" => ((int)$tRec[0]["views"] + 1)));
	$posts_tdb->set_topic($tRec);
	$posts_tdb->set_forum($fRec);
	if (!($tdb->is_logged_in())) {
		$posts_tdb->set_user_info("guest", "password", "0", "0");
		$_COOKIE["power_env"] = 0;
	}
	else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
	$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"]." ".$tRec[0]["subject"];
	require_once('./includes/header.php');
	if ((int)$_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have enough Power to view this topic");
	if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) exitPage("Invalid Forum ID");
	if (!isset($_GET["t_id"]) || !is_numeric($_GET["t_id"])) exitPage("Invalid Topic ID");
	if (!isset($_GET['page']) || $_GET["page"] == "") {
		$_GET['page'] = ceil((substr_count($tRec[0]['p_ids'], ',') + 1) / $_CONFIG['posts_per_page']);
	}
	$pRecs = $posts_tdb->getPosts("posts", (($_CONFIG["posts_per_page"] * $_GET["page"])-$_CONFIG["posts_per_page"]), $_CONFIG["posts_per_page"]);
	if (empty($pRecs)) exitPage("Posts not found");
	$num_pages = ceil(($tRec[0]["replies"] + 1) / $_CONFIG["posts_per_page"]);
	$p = createPageNumbers($_GET["page"], $num_pages, $_SERVER['QUERY_STRING']);
	$posts_tdb->d_posting($p);
	echo "";
	//show header of topic
	echo "
		";
	if ($_GET['page'] == 1) $first_post = $pRecs[0]['id'];
	else $first_post = 0;
	$x = +1;
	foreach($pRecs as $pRec) {
		// display each post in the current topic
		echo "
			<div class='main_cat_wrapper'>
			<div class='cat_area_1' style='text-align:center;'>Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec["date"]))."</div>
			<table class='main_table' cellspacing='1'>";
		if ($x == 0) {
			$table_color = area_1;
			$table_font = $font1;
			$x++;
		} else {
			$table_color = area_2;
			$table_font = $font2;
			$x--;
		}
		unset($user, $status, $statuscolor);
		$sig = '';
		$status = '';
		$statuscolor = '';
		$pm = "";
		if ($pRec["user_id"] != "0") {
			$user = $tdb->get("users", $pRec["user_id"]);
			if ($user[0]["sig"] != "") {
				$sig = format_text(filterLanguage(UPBcoding($user[0]["sig"]), $_CONFIG["censor"]));
				$sig = "<div class='signature'>$sig</div>";
			}
			if (FALSE === mod_avatar::verify_avatar($user[0]['avatar'], $user[0]['avatar_hash'])) {
				$new_avatar = mod_avatar::new_parameters($user[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
				$tdb->edit('users', $user[0]['id'], $new_avatar);
				$user[0] = array_merge($user[0], $new_avatar);
				unset($new_avatar);
			}
			if ($user[0]["level"] == "1") {
				$statuscolor = $_STATUS["userColor"];
				if ($user[0]["posts"] >= $_STATUS["member_post1"]) $status = $_STATUS["member_status1"];
				if ($user[0]["posts"] >= $_STATUS["member_post2"]) $status = $_STATUS["member_status2"];
				if ($user[0]["posts"] >= $_STATUS["member_post3"]) $status = $_STATUS["member_status3"];
				if ($user[0]["posts"] >= $_STATUS["member_post4"]) $status = $_STATUS["member_status4"];
				if ($user[0]["posts"] >= $_STATUS["member_post5"]) $status = $_STATUS["member_status5"];
			} elseif($user[0]["level"] == "2") {
				$statuscolor = $_STATUS["modColor"];
				if ($user[0]["posts"] >= $_STATUS["mod_post1"]) $status = $_STATUS["mod_status1"];
				if ($user[0]["posts"] >= $_STATUS["mod_post2"]) $status = $_STATUS["mod_status2"];
				if ($user[0]["posts"] >= $_STATUS["mod_post3"]) $status = $_STATUS["mod_status3"];
				if ($user[0]["posts"] >= $_STATUS["mod_post4"]) $status = $_STATUS["mod_status4"];
				if ($user[0]["posts"] >= $_STATUS["mod_post5"]) $status = $_STATUS["mod_status5"];
			} elseif($user[0]["level"] == "3") {
				$statuscolor = $_STATUS["adminColor"];
				if ($user[0]["posts"] >= $_STATUS["admin_post1"]) $status = $_STATUS["admin_status1"];
				if ($user[0]["posts"] >= $_STATUS["admin_post2"]) $status = $_STATUS["admin_status2"];
				if ($user[0]["posts"] >= $_STATUS["admin_post3"]) $status = $_STATUS["admin_status3"];
				if ($user[0]["posts"] >= $_STATUS["admin_post4"]) $status = $_STATUS["admin_status4"];
				if ($user[0]["posts"] >= $_STATUS["admin_post5"]) $status = $_STATUS["admin_status5"];
			} else {
				$status = "Member";
				$statuscolor = $_STATUS["membercolor"];
			}
			if ($user[0]["status"] != "") $status = $user[0]["status"];
			if (isset($_COOKIE["id_env"]) && $pRec["user_id"] != $_COOKIE["id_env"]) {
				$user_blList = getUsersPMBlockedList($pRec["user_id"]);
				if (TRUE !== (in_array($_COOKIE["id_env"], $user_blList))) $pm = "<div class='button_pro2'><a href='newpm.php?to=".$pRec["user_id"]."'>Send ".$pRec["user_name"]." a PM</a></div>";
			}
		}
		if (($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) $edit = "<div class='button_pro1'><a href='editpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec["id"]."'>Edit</a></div>";
		else $edit = "";
		if ((($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) && $pRec['id'] != $first_post) $delete = "<div class='button_pro1'><a href='delete.php?action=delete&t=0&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec["id"]."'>X</a></div>";
		else $delete = "";
		if ((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $quote = "<div class='button_pro1'><a href='newpost.php?id=".$_GET["id"]."&t=0&quote=1&t_id=".$_GET["t_id"]."&p_id=".$pRec["id"]."&page=".$_GET["page"]."'>\"Quote\"</a></div>";
		else $quote = "";
		if ((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $reply = "<div class='button_pro1'><a href='newpost.php?id=".$_GET["id"]."&t=0&t_id=".$_GET["t_id"]."&page=$page'>Add Reply</a></div>";
		else $reply = "";
		$msg = format_text(filterLanguage(UPBcoding($pRec["message"]), $_CONFIG["censor"]));
		echo "
			<tr>
				<th><div class='post_name'>";
		if ($pRec["user_id"] != "0") echo "<a href='profile.php?action=get&id=".$pRec["user_id"]."'>".$pRec["user_name"]."</b>";
		else echo $pRec["user_name"];
		echo "</div></th>
				<th><div style='float:left;'><img src='icon/".$pRec["icon"]."'></div><div align='right'>$delete $edit $quote $reply</div></th>
			</tr>
			<tr>
				<td class='$table_color' valign='top' style='width:15%;'>";
		if (@$user[0]["avatar"] != "") echo "<br /><img src=\"".$user[0]["avatar"]."\" border='0' width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."' alt='' title=''><br />";
		else echo "<br /><a href='profile.php'><img src='images/avatars/noavatar.gif' alt='Click here to set avatar' title='Click here to set avatar' /></a><br />";
		if ($pRec["user_id"] != "0") echo "
					<div class='post_info'><span style='color:#".$statuscolor."'><strong>".$status."</strong></span></div>
					<div class='post_info'>
						<strong>Posts:</strong> ".$user[0]["posts"]."
						<br />
						<strong>Registered:</strong>
						<br />
						".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
					</div>
					<br />
					<div class='post_info_extra'>";
		if ($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["icq"] != "") echo "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
		echo"</div>";
		echo "</td>
				<td class='$table_color' valign='top'>
					<div style='padding:12px;margin-bottom:20px;'>$msg</div>
					<div style='padding:12px;'>".$sig."</div></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2'>";
		if (!empty($pRec['edited_by']) && !empty($pRec['edited_by_id']) && !empty($pRec['edited_date'])) echo '
					<div class="post_edited">Last edited by: <a href="profile.php?action=get&id='.$pRec['edited_by_id'].'" target="_new"><strong>'.$pRec['edited_by'].'</strong></a> on '.gmdate("M d, Y g:i:s a", user_date($pRec['edited_date'])).'</div>';
		if ($pRec["user_id"] != "0") echo "";
		if ($pm != "") echo $pm."";
		echo "
					<div class='button_pro2'><a href='profile.php?action=get&id=".$pRec["user_id"]."'>Profile</a></div>
					<div class='button_pro2'><a href='".$user[0]["url"]."' target = '_blank'>Homepage</a></div>
					<div class='button_pro2'><a href='email.php?id=".$pRec["user_id"]."'>email ".$pRec["user_name"]."</a></div>";
		echo "</td>
			</tr>
		$skin_tablefooter";
	}
	//$posts_tdb->d_posting($p);
	if ((int)$_COOKIE["power_env"] >= 2) {
		echo "
		<div id='tabstyle_2'>
			<ul>
				<li><a href='delete.php?action=delete&t=1&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Delete topic</span></a></li>";
		if ($tRec[0]["locked"] == "0") echo "
				<li><a href='managetopic.php?action=CloseTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Close topic?</span></a></li>";
		else echo "

				<li><a href='managetopic.php?action=OpenTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Open topic?</span></a></li>";
		echo "
				<li><a href='managetopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Manage</span></a></li>
			</ul>
		</div>
		<div style='clear:both;'></div>";
		echoTableHeading("Admin / Moderator topic options", $_CONFIG);
		echo "
			<tr>
				<th>Choose your action from the tab menu...</th>
			</tr>
		$skin_tablefooter";
	}
	$tdb->cleanup();
	unset($tdb);
	require('./includes/footer.php');
?>