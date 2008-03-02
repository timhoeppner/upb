<?php
require_once('./includes/class/func.class.php');
require_once('./includes/inc/post.inc.php');
require_once("./includes/class/upload.class.php");
require_once("./includes/class/posts.class.php");

$ajax_type = $_POST['type'];

switch ($ajax_type)
{
  case "getpost" :
    //GETS THE POST INFORMATION FROM THE DATABASE AND PLACES IN TEXT AREA FOR EDITING
    $posts_tdb = new posts(DB_DIR, "posts.tdb");
    $posts_tdb->setFp("topics", $_POST["forumid"]."_topics");
    $posts_tdb->setFp("posts", $_POST["forumid"]);
    $pRec = $posts_tdb->get("posts", $_POST["postid"]);
    if ($_POST['method'] != 'cancel')
    {
    $output = "";
      $output .= "<form action='editpost.php?id=".$_POST["forumid"]."&t_id=".$_POST["threadid"]."&p_id=".$_POST["postid"]."' method='POST' id='quickedit' name='quickedit'>";
      $output .= "<input type='hidden' id='forumid' name='userid' value='".$_POST["forumid"]."'>";
      $output .= "<input type='hidden' id='userid' name='userid' value='".$_POST["userid"]."'>";
      $output .= "<input type='hidden' id='threadid' name='threadid' value='".$_POST["threadid"]."'>";
      $output .= "<input type='hidden' id='postid' name='postid' value='".$_POST["postid"]."'>";
      $output .= "<textarea name='newedit' id='newedit' cols='60' rows='18'>".$pRec[0]['message']."</textarea><br>";
      $output .= "\n<input type='button' onclick='javascript:getEdit(document.getElementById(\"quickedit\"),\"".$_POST['divname']."\");'\' name='qedit' value='Save Edit'>";
      $output .= "\n<input type='submit' name='submit' value='Go Advanced'>";
      $output .= "\n<input type='button' name='cancel_edit' onClick=\"javascript:getPost('".$_POST["userid"]."','".$_POST["forumid"]."-".$_POST["threadid"]."-".$_POST["postid"]."','cancel');\" value='Cancel Edit'>";
      $output .= "</form>";
    }
    else
      $output = format_text(filterLanguage(UPBcoding(encode_text(utf8_decode(stripslashes($pRec[0]['message'])))), $_CONFIG));

    echo $output;

    break 1;

  case "edit" :
    $posts_tdb = new posts(DB_DIR, "posts.tdb");
    $posts_tdb->setFp("topics", $_POST["forumid"]."_topics");
    $posts_tdb->setFp("posts", $_POST["forumid"]);
    $pRec = $posts_tdb->get("posts", $_POST["postid"]);
    //STORES THE EDITED VERSION OF THE POST IN THE DATABASE AND RETURNS THE EDITED PAGE TO THE USER
    if(!(isset($_POST["userid"]) && isset($_POST["forumid"]) && isset($_POST["threadid"]) && isset($_POST["postid"]))) exitPage("Not enough information to perform this function.");
    if(!($tdb->is_logged_in())) exitPage("You are not logged in, therefore unable to perform this action.");

    if($pRec[0]["user_id"] != $_COOKIE["id_env"] && $_COOKIE["power_env"] < 2) exitPage("You are not authorized to edit this post.");

    $msg = format_text(filterLanguage(UPBcoding(encode_text(utf8_decode(stripslashes($_POST["newedit"])))), $_CONFIG));
    $dbmsg = encode_text(stripslashes(utf8_decode($_POST["newedit"])),ENT_NOQUOTES);

    $posts_tdb->edit("posts", $_POST["postid"], array("message" => $dbmsg, "edited_by_id" => $_COOKIE["id_env"], "edited_by" => $_COOKIE["user_env"], "edited_date" => mkdate()));
//clearstatcache();
    $posts_tdb->cleanup();
    $posts_tdb->setFp("posts", $_POST["forumid"]);
    $pRec2 = $posts_tdb->get("posts", $_POST["postid"]);

    $div = $_POST['forumid']."-".$_POST['threadid']."-".$_POST['postid'];

    if(!empty($pRec2[0]['edited_by']) && !empty($pRec2[0]['edited_by_id']) && !empty($pRec2[0]['edited_date']))
    $edited = "Last edited by: <a href='profile.php?action=get&id=".$pRec2[0]['edited_by_id']."' target='_new'>".$pRec2[0]['edited_by']."</a> on ".gmdate("M d, Y g:i:s a", user_date($pRec2[0]['edited_date']));
    echo "$msg<!--divider-->$edited";

    break 1;

  case "reply" :
    //QUICK REPLY TO TOPIC, STORES POST IN DATABASE AND RETURNS THE USER TO THE NEW POST AND ADDS NEW QUICK REPLY FORM
    $output = "<link rel=\"stylesheet\" href=\"".$_CONFIG["skin_dir"]."/css/style.css\" type=\"text/css\">";
    $fRec = $tdb->get("forums", $_POST["id"]);
    $posts_tdb = new posts(DB_DIR."/", "posts.tdb");
    $posts_tdb->setFp("topics", $_POST["id"]."_topics");
    $posts_tdb->setFp("posts", $_POST["id"]);
    $tRec = $posts_tdb->get("topics", $_POST["t_id"]);
    $posts_tdb->set_topic($tRec);
    $posts_tdb->set_forum($fRec);
    $tdb->setFp('users', 'members');

    if (!isset($a)) $a = 0;

    if(!($tdb->is_logged_in()))
    {
      $_COOKIE["user_env"] = "guest";
      $_COOKIE["power_env"] = 0;
      $_COOKIE["id_env"] = 0;
    }

    $msg = encode_text(stripslashes($_POST["newentry"]));
    $tdb->edit("forums", $_POST["id"], array("posts" => ((int)$fRec[0]["posts"] + 1)));
    $rec = $posts_tdb->get("topics", $_POST["t_id"]);

    $posts_tdb->edit("topics", $_POST["t_id"], array("replies" => ((int)$rec[0]["replies"] + 1), "last_post" => mkdate(), "user_name" => $_COOKIE["user_env"], "user_id" => $_COOKIE["id_env"], "monitor" => ""));

    $pre = $rec[0]["p_ids"].",";

    clearstatcache();
    $posts_tdb->sort("topics", "last_post", "DESC");
    clearstatcache();

    $post_date = mkdate();

    $p_id = $posts_tdb->add("posts", array(
        "icon" => $_POST["icon"],
        "user_name" => $_COOKIE["user_env"],
        "date" => $post_date,
        "message" => $msg,
        "user_id" => $_COOKIE["id_env"],
        "t_id" => $_POST["t_id"],
        "upload_id" => $uploadId
    ));

    $posts_tdb->edit("topics", $_POST["t_id"], array("p_ids" => $pre.$p_id));

    if($_COOKIE["power_env"] != "0")
    {
      $user = $tdb->get("users",$_COOKIE["id_env"]);
      $tdb->edit("users", $_COOKIE["id_env"], array("posts" => ((int)$user[0]["posts"] + 1)));
    }

    $posts_tdb->cleanUp();
    $fRec = $tdb->get("forums", $_POST["id"]);
    $posts_tdb->setFp("topics", $_POST["id"]."_topics");
    $posts_tdb->setFp("posts", $_POST["id"]);
    $tRec = $posts_tdb->get("topics", $_POST["t_id"]);
    $posts_tdb->set_topic($tRec);
    $posts_tdb->set_forum($fRec);
    $tdb->setFp('users', 'members');
    if(!($tdb->is_logged_in()))
    {
      $posts_tdb->set_user_info("guest", "password", "0", "0");
      $_COOKIE["power_env"] = 0;
    }
    else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
    $page=1;

    $postids = $tRec[0]['p_ids'];
    $postnums = explode(",",$postids);
    $count = count($postnums);

    $num_pages = ceil($count/$_CONFIG["posts_per_page"]);
    $page = $num_pages;

    $pRecs = $posts_tdb->getPosts("posts", (($_CONFIG["posts_per_page"] * $page)-$_CONFIG["posts_per_page"]), $_CONFIG["posts_per_page"]);

    $query = "id={$_POST['id']}&t_id={$_POST['t_id']}";

    $p = createPageNumbers($page, $num_pages, $query,true);
    $p = str_replace('ajax.php', 'viewtopic.php', $p);
    $pagelinks1 = $posts_tdb->d_posting($p);
    $pagelinks2 = $posts_tdb->d_posting($p,"bottom");

    //BEGIN NEW REPLY OUTPUT
    $x = +1;
    $output = "";

    foreach($pRecs as $key => $pRec)
    {
		// display new reply
		$output .= "<a name='{$pRec['id']}'>
      <div name='post{$_GET['id']}-{$_GET['t_id']}-{$pRec['id']}' id='post{$_GET['id']}-{$_GET['t_id']}-{$pRec['id']}'>
      <div class='main_cat_wrapper'>
			<div class='cat_area_1' style='text-align:center;'>Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec["date"]))."</div>
			<table class='main_table' cellspacing='1'>";
		if ($x == 0)
    {
			$table_color = "area_1";
			$table_font = $font1;
			$x++;
		} else
    {
			$table_color = "area_2";
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
				$sig = format_text(filterLanguage(UPBcoding($user[0]["sig"]), $_CONFIG));
				$sig = "<div class='signature'>$sig</div>";
			}
			if (FALSE === mod_avatar::verify_avatar($user[0]['avatar'], $user[0]['avatar_hash'])) {
				$new_avatar = mod_avatar::new_parameters($user[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
				$tdb->edit('users', $user[0]['id'], $new_avatar);
				$user[0] = array_merge($user[0], $new_avatar);
				unset($new_avatar);
			}
			$status_config = status($user);
			$status = $status_config['status'];
			$statuscolor = $status_config['statuscolor'];
			if ($user[0]["status"] != "") $status = $user[0]["status"];
			if (isset($_COOKIE["id_env"]) && $pRec["user_id"] != $_COOKIE["id_env"]) {
				$user_blList = getUsersPMBlockedList($pRec["user_id"]);
				if (TRUE !== (in_array($_COOKIE["id_env"], $user_blList))) $pm = "<div class='button_pro2'><a href='newpm.php?to=".$pRec["user_id"]."'>Send ".$pRec["user_name"]." a PM</a></div>";
			}
		}
		if (($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >=2)
      $edit = "<div class='button_pro1'><a href=\"javascript:getPost('{$pRec["user_id"]}','{$_POST["id"]}-{$_POST["t_id"]}-{$pRec["id"]}');\">Edit</a></div>";
     //$edit = "<div class='button_pro1'><a href=\"editpost.php?id={$_POST["id"]}&t_id={$_POST["t_id"]}&p_id={$pRec["id"]}\">Edit</a></div>";
		else $edit = "";
		if ((($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) && $pRec['id'] != $first_post) $delete = "<div class='button_pro1'><a href='delete.php?action=delete&t=0&id=".$_POST["id"]."&t_id=".$_POST["t_id"]."&p_id=".$pRec["id"]."'>X</a></div>";
		else $delete = "";
		if ((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $quote = "<div class='button_pro1'><a href='newpost.php?id=".$_POST["id"]."&t=0&quote=1&t_id=".$_POST["t_id"]."&p_id=".$pRec["id"]."&page=".$_GET["page"]."'>\"Quote\"</a></div>";
		else $quote = "";
		if ((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $reply = "<div class='button_pro1'><a href='newpost.php?id=".$_POST["id"]."&t=0&t_id=".$_POST["t_id"]."&page=$page'>Add Reply</a></div>";
		else $reply = "";
		$msg = format_text(filterLanguage(UPBcoding($pRec["message"]), $_CONFIG));
		$output .= "
			<tr>
				<th><div class='post_name'>";
		if ($pRec["user_id"] != "0") $output .= "<a href='profile.php?action=get&id=".$pRec["user_id"]."'>".$pRec["user_name"]."</b>";
		else $output .= $pRec["user_name"];
		$output .= "</div></th>
				<th><div style='float:left;'><img src='icon/".$pRec["icon"]."'></div><div align='right'>$delete $edit $quote $reply</div></th>
			</tr>
			<tr>
				<td class='$table_color' valign='top' style='width:15%;'>";
		if (@$user[0]["avatar"] != "") $output .= "<br /><img src=\"".$user[0]["avatar"]."\" border='0' width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."' alt='' title=''><br />";
		else $output .= "<br /><a href='profile.php'><img src='images/avatars/noavatar.gif' alt='Click here to set avatar' title='Click here to set avatar' /></a><br />";
		if ($pRec["user_id"] != "0") $output .= "
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
		if ($user[0]["aim"] != "") $output .= "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["msn"] != "") $output .= "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["icq"] != "") $output .= "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
		if ($user[0]["yahoo"] != "") $output .= "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";

		$output .= "</div>";
		$output .= "</td>
				<td class='$table_color' valign='top'>
					<div style='padding:12px;margin-bottom:20px;' id='{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}' name='{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}'>$msg</div>
					<div style='padding:12px;'>".$sig."</div></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2'>";
				if ($pRec["user_id"] != "0") $output .= "";
		if ($pm != "") $output .= $pm."";

        //echo "<div name='edit{$_GET['id']}-{$_GET['t_id']}-{$pRec['id']}' id='edit{$_GET['id']}-{$_GET['t_id']}-{$pRec['id']}' style='float: right;'>";
		if (!empty($pRec['edited_by']) && !empty($pRec['edited_by_id']) && !empty($pRec['edited_date']))
    $output .= "<div class='post_edited' name='edit{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}' id='edit{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}'>Last edited by: <a href='profile.php?action=get&id=".$pRec['edited_by_id']." target='_new'><strong>".$pRec['edited_by']."</strong></a> on ".gmdate("M d, Y g:i:s a", user_date($pRec['edited_date']))."</div>";
		else
      	$output .= "<div name='edit{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}' id='edit{$_POST['id']}-{$_POST['t_id']}-{$pRec['id']}' class='post_edited'></div>";
		$output .= "
					<div class='button_pro2'><a href='profile.php?action=get&id=".$pRec["user_id"]."'>Profile</a></div>
					<div class='button_pro2'><a href='".$user[0]["url"]."' target = '_blank'>Homepage</a></div>
					<div class='button_pro2'><a href='email.php?id=".$pRec["user_id"]."'>email ".$pRec["user_name"]."</a></div>";
    $output .= "</td>
			</tr>
		</tbody>
		</table>
		<div class='footer'><img src='".$_CONFIG['skin_dir']."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";
}

    $qrform = ""; //NEW QUICK REPLY FORM

    $qrform .= "<form name='quickreply' action='newpost.php?id=".$_POST['id']."&t_id=".$_POST['id']."&page=".$page."' method='POST' name='quickreply'>\n";
    $qrform .= "<div class='main_cat_wrapper'>
		<div class='cat_area_1'>Quick Reply</div>
		<table class='main_table' cellspacing='1'>
		<tbody>";
    $qrform .= "<table class='main_table' cellspacing='1'>";
    $qrform .= "<input type='hidden' id='id' name='id' value='".$_POST['id']."'>\n";
    $qrform .= "<input type='hidden' id='t_id' name='t_id' value='".$_POST['t_id']."'>\n";
    $qrform .= "<input type='hidden' id='page' name='page' value='".$_POST['page']."'>\n";
    $qrform .= "<input type='hidden' id='user_id' name='user_id' value='{$_COOKIE['id_env']}'>\n";
    $qrform .= "<input type='hidden' id='icon' name='icon' value='icon1.gif'>\n";
    $qrform .= "<input type='hidden' id='username' name='username' value='{$_COOKIE["user_env"]}'>\n";
    $qrform .= "
		<tr><td class='area_1' style='padding:8px;'><strong>User Name:</strong></td><td class='area_2'>".$_COOKIE["user_env"]."</td></tr>\n
		<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
		<tr><td class='area_1' style='padding:8px;' valign='top'><strong>Message:</strong></td>
    <td class='area_2'>\n
    <textarea id=\"newentry\" name=\"newentry\" cols=\"60\" rows=\"18\"></textarea>\n
    </td></tr>\n";
    $qrform .= "<tr><td class='footer_3a' style='text-align:center;' colspan='2'>\n
    <input type='button' name='quickreply' value='Quick Reply' onclick=\"javascript:getReply(document.getElementById('quickreply'))\">\n
    <input type='submit' name='submit' value='Go Advanced'>\n</td></tr></form></font>";
    $qrform .= "</tbody>
		</table>
		<div class='footer'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";

    $output .= "<!--divider-->$pagelinks1<!--divider-->$pagelinks2<!--divider-->$qrform";

    echo $output;

    break 1;

    case "sort" :
      //SORTING OF FORUMS AND CATEGORIES
      $output = "";
      if($_POST['what'] == 'cat')
        $sort = $_CONFIG['admin_catagory_sorting'];
      elseif($_POST['what'] == 'forum')
      {
        $fRec = $tdb->get('forums', $_POST['id']);
        $cRec = $tdb->get('cats', $fRec[0]['cat']);
        $sort = $cRec[0]['sort'];
      }

      $sort = explode(',', $sort);

      if(FALSE !== ($index = array_search($_POST['id'], $sort)))
      {
        if($_POST['where'] == 'up' && $index > 0)
        {
          $tmp = $sort[$index-1];
          $sort[$index-1] = $sort[$index];
          $sort[$index] = $tmp;
        }
        elseif($_POST['where'] == 'down' && $index < (count($sort)-1))
        {
          $tmp = $sort[$index+1];
          $sort[$index+1] = $sort[$index];
          $sort[$index] = $tmp;
        }
	     $sort = implode(',', $sort);

       if($_POST['what'] == 'cat')
          $config_tdb->editVars('config', array('admin_catagory_sorting' => $sort));
      elseif($_POST['what'] == 'forum')
        $tdb->edit('cats', $cRec[0]['id'], array('sort' => $sort));
      }

      $tdb->cleanUp();
      $tdb->setFp('forums', 'forums');
      $tdb->setFp('cats', 'categories');

      $cRecs = $tdb->listRec("cats", 1);
      $config_tdb->clearcache();
      $vars = $config_tdb->getVars('config', true);
    	// Sort categories in the order that they appear

      $cSorting = explode(",", $vars[7]['value']);
      $k = 0;
    	$i = 0;
    	$sorted = array();
    	while ($i < count($cRecs)) {
    	 if ($cSorting[$k] == $cRecs[$i]["id"])
       {
    	   $sorted[] = $cRecs[$i];
    		  //unset($cRecs[$i]);
    			$k++;
    			$i = 0;
    	 }
       else
        $i++;
    	 }

      $cRecs = $sorted;
    	unset($sorted, $i, $catdef, $cSorting);
    	reset($cRecs);

      $output .= "<div class='main_cat_wrapper'>
		<div class='cat_area_1'>Forum Control</div>
		<table class='main_table' cellspacing='1'>
		<tbody>";

		  $output .= "
			<tr>
			    <th style='width:7%;'>&nbsp;</th>
				<th style='width:68%;'>Name</th>
				<th style='width:5%;text-align:center;'>View</th>
				<th style='width:5%;text-align:center;'>Post</th>
				<th style='width:5%;text-align:center;'>Reply</th>
				<th style='width:10%;text-align:center;'>Edit?</th>
				<th style='width:10%;text-align:center;'>Delete?</th>
			</tr>";
		    if ($cRecs[0]["name"] == "") {
				$output .= "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='6'>No categories found</td>
			</tr>";
			} else {
			    for($i=0,$c1=count($cRecs);$i<$c1;$i++) {
					//show each category
					$view = createUserPowerMisc($cRecs[$i]["view"], 2);
					$output .= "
			<tr>
			    <td class='area_1' style='padding:8px;'>".(($i>0) ? "<a href=\"javascript:forumSort('cat','up','".$cRecs[$i]['id']."');\"><img src='./images/up.gif'></a>&nbsp;" : "&nbsp;&nbsp;&nbsp;&nbsp;").(($i<($c1-1)) ? "<a href=\"javascript:forumSort('cat','down','".$cRecs[$i]['id']."');\"><img src='./images/down.gif'></a>" : "")."</td>
				<td class='area_1' style='padding:8px;'><strong>".$cRecs[$i]["name"]."</strong></td>
				<td class='area_1' style='padding:8px;text-align:center;' colspan=3>$view</td>
				<td class='area_1' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=edit_cat&id=".$cRecs[$i]["id"]."'>Edit</a></td>
				<td class='area_1' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=delete_cat&id=".$cRecs[$i]["id"]."'>Delete</a></td>
			</tr>";

					if($cRecs[$i]['sort'] == '') {
					   $output .= "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='7'>No forums exist in this category yet.</td>
			</tr>";
					} else {
					    $ids = explode(',', $cRecs[$i]['sort']);
					    for($j=0,$c2=count($ids);$j<$c2;$j++) {
					       $fRec = $tdb->get('forums', $ids[$j]);
                			//$post_tdb->setFp("topics", $fRec[0]["id"]."_topics");
                			//$post_tdb->setFp("posts", $fRec[0]["id"]);
                			$whoView = createUserPowerMisc($fRec[0]["view"], 3);
                			$whoPost = createUserPowerMisc($fRec[0]["post"], 3);
                			$whoReply = createUserPowerMisc($fRec[0]["reply"], 3);
                			//show each forum
                			$output .= "
			<tr>
			    <td class='area_2' style='padding:8px;text-align:center;'>".(($j>0) ? "<a href=\"javascript:forumSort('forum','up','".$fRec[0]['id']."');\"><img src='./images/up.gif'></a>" : "&nbsp;&nbsp;&nbsp;").(($j<($c2-1)) ? "<a href=\"javascript:forumSort('forum','down','".$fRec[0]['id']."');\"><img src='./images/down.gif'></a>" : "")."</td>
				<td class='area_2' style='padding:8px;'><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$fRec[0]["forum"]."</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoView</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoPost</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoReply</td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=edit_forum&id=".$fRec[0]["id"]."'>Edit</a></td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=delete_forum&id=".$fRec[0]["id"]."'>Delete</a></td>
			</tr>";
					    }
					}
				}
			}
    		$output .= "
		</tbody>
		</table>
		<div class='footer'><img src='".$_CONFIG['skin_dir']."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";
	     echo $output;

        break 1;

    case "sig" :
      if ($_POST['status'] == "set")
      {
        $sig = format_text(filterLanguage(UPBcoding($_POST["sig"]), $_CONFIG));
        $sig_title = "<strong>Signature Preview:</strong><br>To save this signature press Submit below";
      }
      else
      {
        $rec = $tdb->get("users", $_POST["id"]);
        $sig = format_text(filterLanguage(UPBcoding($rec[0]['sig']), $_CONFIG));
        $sig_title = "<strong>Current Signature:</strong>";
      }
      echo $sig."<!--divider-->".$sig_title;

      break 1;

    default:
      echo "Something has gone horribly wrong. You should never see this text";
      break 1;
}
?>
