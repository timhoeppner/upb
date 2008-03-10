<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2
require_once('./includes/upb.initialize.php');
if(!isset($_GET['action']) || $_GET['action'] == '') $_GET['action'] = 'edit';
if ($_GET['action'] == "get" || $_GET['action'] == 'view') $where = "Member Profile";
elseif ($_GET['action'] == "bookmarks") $where = "Favorited Topics";
elseif($_GET['action'] == "edit")$where = "User CP";
if (isset($_POST["u_edit"])) {
	if (!($tdb->is_logged_in())) {
		echo "<html><head><meta http-equiv='refresh' content='2;URL=login.php?ref=profile.php'></head></html>";
		exit;
	} else {
		$rec = array();
		if (!isset($_POST["u_email"])) exitPage("please enter your email!", true);
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["u_email"])) exitPage("please enter a valid email!", true);
			$_POST['u_sig'] = stripslashes($_POST['u_sig']);
		if (strlen($_POST["u_sig"]) > 500) exitPage("You cannot have more than 500 characters in your signature.", true);
		$user = $tdb->get("users", $_COOKIE["id_env"]);
		if (strlen($_POST["u_newpass"]) > 0) {
			if ($user[0]['password'] != generateHash($_POST['u_oldpass'], $user[0]['password'])) exitPage('You old password does not match the one on file!', true);
			if ($_POST["u_newpass"] != $_POST["u_newpass2"]) exitPage("your pass and pass confirm are not matching!", true);
			if (strlen($_POST["u_newpass"]) < 4) exitPage("your password has to be longer then 4 characters", true);
			$rec["password"] = generateHash($_POST["u_newpass"]);
			setcookie("user_env", "");
			setcookie("uniquekey_env", "");
			setcookie("power_env", "");
			setcookie("id_env", "");
			$ht = "<meta http-equiv='refresh' content='2;URL=login.php'>";
		}
		else $ht = "<meta http-equiv='refresh' content='2;URL=profile.php'>";
		if ($user[0]["email"] != $_POST["u_email"]) $rec["email"] = $_POST["u_email"];
		if ($user[0]["u_sig"] != encode_text(chop($_POST["u_sig"]))) $rec["sig"] = encode_text(chop($_POST["u_sig"]));
		if (substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://".$_POST["u_site"];
  if ($user[0]["url"] != $_POST["u_site"])
    $rec["url"] = $_POST["u_site"];
  if ($_POST['u_site'] == "http://" or $rec['url'] == 'http://')
    $rec['url'] = "";
  if ($_POST["u_timezone"]{0} == '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);
		if ($_POST["show_email"] != "1") $_POST["show_email"] = "0";
		if ($_POST["email_list"] != "1") $_POST["email_list"] = "0";
		if ($user[0]["view_email"] != $_POST["show_email"]) $rec["view_email"] = $_POST["show_email"];
		if ($user[0]["mail_list"] != $_POST["email_list"]) $rec["mail_list"] = $_POST["email_list"];
		if ($user[0]["location"] != $_POST["u_loca"]) $rec["location"] = $_POST["u_loca"];
		if (FALSE === mod_avatar::verify_avatar($_POST['avatar'], $user[0]['avatar_hash'])) {
			$new_avatar = mod_avatar::new_parameters($_POST['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
			$rec = array_merge($rec, $new_avatar);
			unset($new_avatar);
		}
		if ($user[0]["icq"] != $_POST["u_icq"]) $rec["icq"] = $_POST["u_icq"];
		if ($user[0]["aim"] != $_POST["u_aim"]) $rec["aim"] = $_POST["u_aim"];
		if ($user[0]["yahoo"] != $_POST["u_yahoo"]) $rec["yahoo"] = $_POST["u_yahoo"];
		if ($user[0]["msn"] != $_POST["u_msn"]) $rec["msn"] = $_POST["u_msn"];
		if ($user[0]["timezone"] != $_POST["u_timezone"]) {
			$rec["timezone"] = $_POST["u_timezone"];
			setcookie("timezone", $_POST["u_timezone"], (time() + (60 * 60 * 24 * 7)));
		}
  $tdb->edit("users", $_COOKIE["id_env"], $rec);
		require_once('./includes/header.php');
  echo "<div class='alert_confirm'>
				<div class='alert_confirm_text'>
				<strong>User Profile Update:</strong></div><div style='padding:4px;'>Your user profile has been successfully updated
				</div>
				</div>
				<meta http-equiv='refresh' content='2;URL=".$_GET["ref"]."'>";
		require_once('./includes/footer.php');
	}
} elseif($_GET["action"] == 'get' || $_GET['action'] == 'view') {
	if (!isset($_GET["id"])) {
		echo "<html><head><meta http-equiv='refresh' content='0;URL=index.php'></head></html>";
		exit;
	} else {
		$rec = $tdb->get("users", $_GET["id"]);
		if (FALSE === mod_avatar::verify_avatar($rec[0]['avatar'], $rec[0]['avatar_hash'])) {
			$new_avatar = mod_avatar::new_parameters($rec[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
			$tdb->edit('users', $rec[0]['id'], $new_avatar);
			$rec[0] = array_merge($rec[0], $new_avatar);
			unset($new_avatar);
		}
		$status_config = status($rec);
		$status = $status_config['status'];
		$statuscolor = $status_config['statuscolor'];
		if ($rec[0]["status"] != "") $status = $rec[0]["status"];
		require_once('./includes/header.php');
		echo "";
		echoTableHeading("Viewing profile for ".$rec[0]["user_name"]."", $_CONFIG);
		echo "
			<tr>
				<td colspan='2' id='topcontent'>
				<div class='pro_container'>
						<span style='color:#".$statuscolor.";font-size:14px;'>".$rec[0]["user_name"]."</span>
						<br />
						<br />
						<img src=\"".$rec[0]["avatar"]."\" width=\"".$rec[0]['avatar_width']."\" height=\"".$rec[0]['avatar_height']."\" alt='' title='' />
						<br />
						<div class='link_pm'>";
		require_once('./includes/inc/privmsg.inc.php');
		$blockedList = getUsersPMBlockedList($_GET["id"]);
		if ($_GET["id"] == $_COOKIE["id_env"]) {
			echo "";
		} elseif($_COOKIE["id_env"] == "" || $_COOKIE["id_env"] == "0") {
			echo "Login to contact";
		} elseif(in_array($_COOKIE["id_env"], $blockedList)) {
			echo "You are banned from using the PM system";
		} else {
			echo "<a href='newpm.php?to=".$_GET["id"]."' target='_blank'>Send private message?</a>";
		}
		echo "</div></div></td></tr><tr><td id='leftcontent'>
					<div class='pro_container'>
						<div class='pro_area_1'><div class='pro_area_2'><strong>Joined: </strong></div>".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."</div>
						<div class='pro_area_1'><div class='pro_area_2'><strong>Posts made: </strong></div>".$rec[0]["posts"]."</div>";


  echo "
			<div class='pro_area_1'><div class='pro_area_2'><strong>Homepage: </strong></div>";
		if (strlen($rec[0]['url']) != 0)
    echo "<a href='".$rec[0]["url"]."' target='_blank'>".$rec[0]["url"]."</a>";
		echo "&nbsp;</div>";
  echo "</div>
			</td><td id='rightcontent'>
			<div class='pro_container'>
						<div class='pro_area_1' style='white-space:nowrap;'><div class='pro_area_2'><strong>Status: </strong></div>
							<span style='color:#".$statuscolor."'><strong>$status &nbsp;&nbsp;&nbsp;</strong></span></div>
						<div class='pro_area_1'><div class='pro_area_2'><strong>Email: </strong></div>";
		if ((bool)$rec[0]["view_email"]) echo "<a href='mailto:".$rec[0]["email"]."'>".$rec[0]["email"]."</a>";
		else echo "not public";
		echo "</div>";
  echo "<div class='pro_area_1'><div class='pro_area_2'><strong>Location: </strong></div>".$rec[0]["location"]."&nbsp;</div>";
		echo "</div>";
		if (@$rec[0]["icq"] != "" and @$rec[0]["aim"] != "" and @$rec[0]["msn"] != "" and @$rec[0]["yahoo"] != "")
  {
  echo "<div class='pro_contact'>";
		if (@$rec[0]["icq"] != "") echo "
							<strong>icq</strong><a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$rec[0]["icq"]."&action=message'><img src='images/icq.gif' border='0'>&nbsp;".$rec[0]["icq"]."</a>";
		if (@$rec[0]["aim"] != "") echo "
							<strong>aim</strong><a href='aim:goim?screenname=".$rec[0]["aim"]."'><img src='images/aol.gif' border='0'>&nbsp;".$rec[0]["aim"]."</a>";
		if (@$rec[0]["msn"] != "") echo "
							<strong>msn</strong><a href='http://members.msn.com/".$rec[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0'>&nbsp;".$rec[0]["msn"]."</a>";
		if (@$rec[0]["yahoo"] != "") echo "
							<strong>Y!</strong><a href='http://edit.yahoo.com/config/send_webmesg?.target=".$rec[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$rec[0]["yahoo"]."&m=g&t=0'>&nbsp;".$rec[0]["yahoo"]."</a>";
		echo "</div>";
		}
  //		<div class='pro_blank'>Just imagine what could go here!</div>
		//		<br />
		//		<div class='pro_blank2'>Or here!</div>
		//	";
		echo "</td>
				</tr>";
		if (@$rec[0]["sig"] != "") echo "
				<tr>
					<td id='bottomcontent' colspan='2'>
						<div class='pro_sig_name'>".$rec[0]["user_name"]."'s Signature:</div>
						<div class='pro_sig_area'>
							<div class='pro_signature'>".format_text(UPBcoding(filterLanguage($rec[0]["sig"], $_CONFIG)))."</div>
						</div></td>
				</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
		require_once('./includes/footer.php');
	}
} elseif($_GET['action'] == 'edit') {
	if (!($tdb->is_logged_in())) {
		echo "<html><head><meta http-equiv='refresh' content='2;URL=login.php?ref=profile.php'></head></html>";
		exit;
	} else {
		$rec = $tdb->get("users", $_COOKIE["id_env"]);
		require_once('./includes/header.php');
		@$rec[0]["sig"] = str_replace("<br />", "\n", $rec[0]["sig"]);
		@$rec[0]["sig"] = str_replace("<br />", "\n", $rec[0]["sig"]);
		@$rec[0]["sig"] = str_replace("<br />", "\n", $rec[0]["sig"]);
		@$rec[0]["sig"] = str_replace("<br />", "\n", $rec[0]["sig"]);
		echo "<form action='$PHP_SELF' id='newentry' name='newentry' method='post'>";
        echo "
        <div id='tabstyle_2'>
        	<ul>
        		<li><a href='profile.php?action=edit'><span>User CP</span></a></li>
        		<li><a href='profile.php?action=bookmarks'><span>View Bookmarks</span></a></li>
        	</ul>
        </div>
        <div style='clear:both;'></div>";
		echoTableHeading("Account settings - Edit profile information", $_CONFIG);
		echo "
			<tr>
				<td class='area_1' style='width:45%;'><strong>login:</strong></td>
				<td class='area_2'>".$rec[0]["user_name"]."</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Old password:</strong><br /><i>Submit your old password only if you are changing your password</i></td>
				<td class='area_2'><input type='password' name='u_oldpass'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>New password:</strong></td>
				<td class='area_2'><input type='password' name='u_newpass'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>New password confirmation:</strong></td>
				<td class='area_2'><input type='password' name='u_newpass2'></td>
			</tr>";
		if ($_COOKIE["power_env"] >= 2) {
			echo "
			<tr>
				<td class='area_1'><strong>email:</strong></td>
				<td class='area_2'><input type='text' name='u_email' value='".$rec[0]["email"]."'>&nbsp;".$rec[0]["email"]."</td>
			</tr>";
		} else {
			echo "
			<tr>
				<td class='area_1'><strong>email:</strong><br /><font size='1' face='$font_face'>Email the Forum Administrator to change your email address.</a></td>
				<td class='area_2'><input type='hidden' name='u_email' value='".$rec[0]["email"]."'>&nbsp;".$rec[0]["email"]."</td>
			</tr>";
		}
		if ((bool) $rec[0]["view_email"]) $email_checked = "CHECKED";
		else $email_checked = "";
		if ((bool) $rec[0]["mail_list"]) $mail_checked = "CHECKED";
		else $mail_checked = "";
		echo "
			<tr>
				<td class='area_1'>Make email address public in profile?&nbsp;&nbsp;&nbsp;
					<a href=\"javascript: window.open('privacy.php','','status=no, width=800,height=50'); void('');\">
					Privacy Policy</a></td>
				<td class='area_2'><input type=checkbox name='show_email' value = '1' $email_checked></td>
			</tr>
			<tr>
				<td class='area_1'>Add email to UPB discussion forums mailing list?</td>
				<td class='area_2'><input type=checkbox name=email_list value='1' $mail_checked></td>
			</tr>
			<tr>
				<td class='area_1'><strong>location:</strong></td>
				<td class='area_2'><input type='text' name='u_loca' value='".$rec[0]["location"]."'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
		echoTableHeading("Avatar Options", $_CONFIG);
		echo "
			<tr>
				<th style='text-align:center;'>Current avatar</th>
				<th style='text-align:center;'>Select a new avatar</th>
			</tr>
			<tr>
				<td class='area_1' valign='middle' style='width:45%;text-align:center;padding:20px;height:150px;'>";
		if (@$rec[0]["avatar"] != "") echo "<img src=\"".$rec[0]["avatar"]."\" border='0' width='".$rec[0]['avatar_width']."' height='".$rec[0]['avatar_height']."'><br />";
		else echo "<img src='images/avatars/noavatar.gif' alt='' title='' />";
		//Add coding use$_REGIST[newuseravatar] OR check if user level > 1
		echo "</td>
				<td class='area_2'>
					<table cellspacing='0px' style='width:100%;'>
						<tr>
							<td style='text-align:center;width:50%;'>
								<img src='images/avatars/blank.gif' name='myImage' alt='' title='' /></td>
							<td><select class='select' size='5' name='avatar' onChange='swap(this.options[selectedIndex].value)'>";
		function returnimages($dirname = "images/avatars/") {
			$pattern = "\.(jpg|jpeg|png|gif|bmp)$";
			$files = array();
			$curimage = 0;
			if ($handle = opendir($dirname)) {
				while (false !== ($file = readdir($handle))) {
					if (eregi($pattern, $file)) {
						echo "<option value ='images/avatars/".$file."'>".$file."</option>";
						$curimage++;
					}
				}
				closedir($handle);
			}
			return($files);
		}
		echo "" . "\n";
		returnimages();
		echo "</select></td>
						</tr>
					</table>
				</td>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
		echoTableHeading("Other Information", $_CONFIG);
		echo "
			<tr>
				<td class='area_1' style='width:35%;'><strong>Homepage:</strong></td>
				<td class='area_2'><input type='text' name='u_site' value='";
    if ($rec[0]["url"] == '')
      echo "http://";
    else
      echo $rec[0]["url"];
    echo "'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='bar_icq'><strong>ICQ:</strong></td>
				<td class='area_2'><input type='text' name='u_icq' value='".$rec[0]["icq"]."'></td>
			</tr>
			<tr>
				<td class='bar_aim'><strong>AIM:</strong></td>
				<td class='area_2'><input type='text' name='u_aim' value='".$rec[0]["aim"]."'> </td>
			</tr>
			<tr>
				<td class='bar_yim'><strong>Yahoo!:</strong></td>
				<td class='area_2'><input type='text' name='u_yahoo' value='".$rec[0]["yahoo"]."'></td>
			</tr>
			<tr>
				<td class='bar_msnm'><strong>MSN:</strong></td>
				<td class='area_2'><input type='text' name='u_msn' value='".$rec[0]["msn"]."'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
    <tr>
				<td class='area_1' valign='top'><strong>Signature:</strong></td>
				<td class='area_2'>".bbcodebuttons('u_sig','sig')."<textarea id='u_sig' name='u_sig' cols=45 rows=10>".$rec[0]["sig"]."</textarea><br><input type='button' onClick=\"javascript:sigPreview(document.getElementById('newentry'),'".$_COOKIE['id_env']."','set');\" value='Preview Signature'></td></tr>
			<tr>
				<td class='area_1' valign='top'><div id='sig_title'><strong>Current Signature:</strong></div></td>
				<td class='area_2'><div style='display:inline;' id='sig_preview'>".format_text(filterLanguage(UPBcoding($rec[0]["sig"]), $_CONFIG))."</div></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Timezone Setting:</strong></td>
				<td class='area_2'>";
      timezonelist($rec[0]["timezone"]);
			echo "</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=reset name='reset' value='Reset' onClick=\"javascript:sigPreview(document.getElementById('newentry'),'".$_COOKIE['id_env']."','reset');\"'><input type='submit' name='u_edit' value='Submit'></td>
			</tr>";
    echoTableFooter($_CONFIG['skin_dir']);
    echo "</form>";
		require_once('./includes/footer.php');
	}
} elseif($_GET['action'] == 'bookmarks') {
    require_once('./includes/header.php');
    $topics = array();
    if(isset($_SESSION['newTopics']) && is_array($_SESSION['newTopics'])) while(list($forum, $arr) = each($_SESSION['newTopics'])) {
        if($forum == 'lastVisitForums') continue;
        while(list($topic, $val) = each($arr)) {
            if($val == 2) $topics[] = substr($forum, 1).','.substr($topic, 1);
        }
    }
	echo "
	<div id='tabstyle_2'>
		<ul>
			<li><a href='profile.php?action=edit'><span>User CP</span></a></li>
			<li><a href='profile.php?action=bookmarks'><span>View Bookmarks</span></a></li>
		</ul>
	</div>
	<div style='clear:both;'></div>";
    echoTableHeading("Bookmarked Topics", $_CONFIG);
    echo "
	<tr>
		<th style='width: 75%;'>Topic</th>
		<th style='width:25%;text-align:center;'>Last Post</th>
	</tr>";
	if(empty($topics)) {
	    echo "
	<tr>
		<td colspan='6' class='area_2' style='text-align:center;font-weight:bold;padding:20px;'>you have no bookmarked topics</td>
	</tr>";
    } else {
        require_once('./includes/class/posts.class.php');
        $posts_tdb = new posts(DB_DIR."/", "posts.tdb");
        while(list(, $tmp) = each($topics)) {
            list($f_id, $t_id) = explode(',', $tmp);
            $posts_tdb->setFp("topics", $f_id."_topics");
            $fRec = $tdb->get("forums", $f_id);
            $tRec = $posts_tdb->get('topics', $t_id);
		    if ($tRec[0]["icon"] == "") continue;
		    $tRec[0]['subject'] = "<a href='viewforum.php?id=".$f_id."'>".$fRec[0]["forum"]."</a> " .$_CONFIG["where_sep"] . " <a href='viewtopic.php?id=".$f_id."&amp;t_id=".$tRec[0]["id"]."'>".$tRec[0]["subject"]."</a>";
			settype($tRec[0]["replies"], "integer");
			$total_posts = $tRec["replies"] + 1;
			$num_pages = ceil($total_posts / $_CONFIG["posts_per_page"]);
			if ($num_pages == 1) {
				$r_ext = "";
			} else {
				$r_ext = "<br /><div class='pagination_small'> Pages: ( ";
				for($m = 1; $m <= $num_pages; $m++) {
					$r_ext .= "<a href='viewtopic.php?id=".$f_id."&amp;t_id=".$tRec[0]["id"]."&page=$m'>$m</a> ";
				}
				$r_ext .= ")</div>";
			}
			if ($tRec[0]["topic_starter"] == "guest") $tRec[0]["topic_starter"] = "<i>guest</i>";
			echo "
	<tr>
		<td class='area_2' style=\"cursor:pointer;\" onclick=\"window.location.href='viewtopic.php?id=".$f_id."&amp;t_id=".$tRec[0]["id"]."';\" onmouseover=\"this.className='area_2_over'\" onmouseout=\"this.className='area_2'\">
			<span class='link_1'>".$tRec[0]["subject"].$r_ext."</span>
			<div class='description'>Started By:&nbsp;<span style='color:#".$statuscolor."'>".$tRec[0]["topic_starter"]."</span></div>
			<div class='box_posts'><strong>Views:</strong>&nbsp;".$tRec[0]["views"]."</div>
			<div class='box_posts'><strong>Replies:</strong>&nbsp;".$tRec[0]["replies"]."</div></td>
		<td class='area_1' style='text-align:center;'>
			<div class='post_image'><img src='icon/".$tRec[0]["icon"]."'></div>
			<span class='latest_topic'><span class='date'>".gmdate("M d, Y g:i:s a", user_date($tRec[0]["last_post"]))."</span>
			<br />
			<strong>By:</strong> ";
			if ($tRec[0]["user_id"] != "0") echo "<span class='link_2'><a href='profile.php?action=get&id=".$tRec[0]["user_id"]."'>".$tRec[0]["user_name"]."</a></span></td>
	</tr>";
			else echo "a ".$tRec[0]["user_name"]."</span></td>
	</tr>";
		}
    }
    echoTableFooter(SKIN_DIR);
    require_once('./includes/footer.php');
} else redirect('index.php', 0);
?>
