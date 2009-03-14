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
			if (strlen($_POST["u_newpass"]) < 6) exitPage("your password has to be longer then 6 characters", true);
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
		if ($_POST['u_sig'] == "")
		  $rec['sig'] = "";
		if (substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://".$_POST["u_site"];
  if ($user[0]["url"] != $_POST["u_site"])
    $rec["url"] = $_POST["u_site"];
  if ($_POST['u_site'] == "http://" or $rec['url'] == 'http://')
    $rec['url'] = "";
  for ($i=1;$i<=6;$i++)
    $rec["custom_profile$i"] = $_POST["custom_profile$i"];
  if ($_POST["u_timezone"]{0} == '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);
    if ($_POST["show_email"] != "1") $_POST["show_email"] = "0";
		if ($_POST["email_list"] != "1") $_POST["email_list"] = "0";
		if ($user[0]["view_email"] != $_POST["show_email"]) $rec["view_email"] = $_POST["show_email"];
		if ($user[0]["mail_list"] != $_POST["email_list"]) $rec["mail_list"] = $_POST["email_list"];
		if ($user[0]["location"] != $_POST["u_loca"]) $rec["location"] = $_POST["u_loca"];
	    if($_REGIST['custom_avatars'] == 2 && isset($_FILES["avatar2"]["name"]) && trim($_FILES["avatar2"]["name"]) != "") {
	        if($_FILES['avatar2']['error'] == UPLOAD_ERR_OK) {
                require_once('./includes/class/upload.class.php');
    			$upload = new upload(DB_DIR, $_CONFIG["avatarupload_size"], $_CONFIG["fileupload_location"]);
    			$uploadId = $upload->storeFile($_FILES["avatar2"]);
    			if ($uploadId !== false) {
    			    $rec['avatar'] = 'downloadattachment.php?id='.$uploadId;
    			}
	        } else {
	            $upload_err = "The uploaded avatar ";
	            switch ($_FILES['avatar2']['error']) {
	                case UPLOAD_ERR_INI_SIZE:
	                    $upload_err .= "exceeds the <strong>upload_max_filesize</strong> directive in <i>php.ini.</i>";
	                    break;
	                case UPLOAD_ERR_FORM_SIZE:
	                    $upload_err .= "exceeds the <b><i>MAX_FILE_SIZE</i></b> directive that was specified in the HTML form.";
	                    break;
	                case UPLOAD_ERR_PARTIAL:
	                    $upload_err .= "was only partially uploaded.";
	                    break;
	                case UPLOAD_ERR_NO_FILE:
	                    $upload_err .= "was not uploaded.";
	                    break;
	                case UPLOAD_ERR_NO_TMP_DIR:
	                    $upload_err = "Uploaded Avatar Error: Missing a temporary folder.";
	                    break;
	                case 7: //UPLOAD_ERR_CANT_WRITE (PHP Version >= 5.1.0)
	                   $upload_err = "Uploaded Avatar Error: Failed to write file to disk.";
	                   break;
	                case 8: //UPLOAD_ERR_EXTENSION (PHP Version >= 5.2.0)
	                   $upload_err = "The avatar upload stopped by extension.";
	                default:
	                    $upload_err .= "encountered an unknown error while trying to upload";
	            }
	        }
	    } elseif($_REGIST['custom_avatars'] == 1 && isset($_POST['avatar2']) && $_POST['avatar2'] != '') {
          $rec['avatar'] = $_POST['avatar2'];
	    } elseif(isset($_POST['avatar']) && $_POST['avatar'] != '') {
	        $rec['avatar'] = $_POST['avatar'];
	    }
        if(isset($rec['avatar']) && FALSE !== strpos($user[0]['avatar'], 'downloadattachment.php?id=')) {
            $id = substr($user[0]['avatar'], 26);
            if(ctype_digit($id)) {
                if(!isset($upload)) {
                    require_once('./includes/class/upload.class.php');
                    $upload = new upload(DB_DIR, $_CONFIG["avatarupload_size"], $_CONFIG["fileupload_location"]);
                }
                $upload->deleteFile($id);
            }
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
		if($rec === false) {
		    exitPage(str_replace('__TITLE__', ALERT_GENERIC_TITLE, str_replace('__MSG__', 'This user was either deleted or not found.', ALERT_MSG)),
		    true);
		}

		$status_config = status($rec);
		$status = $status_config['status'];
		$statuscolor = $status_config['statuscolor'];
		if ($rec[0]["status"] != "") $status = $rec[0]["status"];
		require_once('./includes/header.php');
		require_once('./includes/class/posts.class.php');
		
		echoTableHeading("Viewing profile for ".$rec[0]["user_name"]."", $_CONFIG);
		echo "<tr><th width='20%'>Forum Info</th><th colspan='2'>Contact Info</th></tr>";
		echo "
			<tr>
				<td class='area_1' rowspan='5'>
						<span style='color:#".$statuscolor.";font-size:14px;'>".$rec[0]["user_name"]."</span>
						<br />
						<br />";
              echo "<img src='".$rec[0]["avatar"]."' alt='' title='' />";
            echo "<br /><span style='color:#".$statuscolor."'><strong>$status &nbsp;&nbsp;&nbsp;</strong></span><br>";
            
    
    echo "<strong>Joined:</strong> ".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."<br><strong>Posts made:</strong> ".$rec[0]["posts"]."<br><strong>Last Visit:</strong> ".gmdate("Y-m-d", user_date($rec[0]["lastvisit"]));
    if($_COOKIE['power_env'] >= 3 && $rec[0]['level'] <= $_COOKIE['power_env']) 
    echo "<p><a href='admin_members.php?action=edit&id={$_GET['id']}'>Edit Member</a>";
    	echo "</td>";

    
		require_once('./includes/inc/privmsg.inc.php');
		$blockedList = getUsersPMBlockedList($_GET["id"]);
		echo "<td class='pro_area_1' width='20%' valign='top'><div class='pro_area_2'><strong>Private Message:</strong></div></td><td valign='top' class='pro_area_1'>";
    if ($_GET["id"] == $_COOKIE["id_env"]) {
			echo "Hi";
		} elseif($_COOKIE["id_env"] == "" || $_COOKIE["id_env"] == "0") {
			echo "Login to contact";
		} elseif(in_array($_COOKIE["id_env"], $blockedList)) {
			echo "You are banned from using the PM system";
		} else {
			echo "<a href='newpm.php?to=".$_GET["id"]."' target='_blank'>Send private message?</a></td></tr>";
		}
		echo "<tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>ICQ:</strong></div></td><td class='pro_area_1'>";
    if (@$rec[0]["icq"] != "") 
      echo "<a href='http://www.icq.com/people/cmd.php?uin=".$rec[0]["icq"]."&action=message'>Contact via ICQ</a>";
		echo "</td></tr><tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>AIM:</strong></div></td><td class='pro_area_1'>";
    if (@$rec[0]["aim"] != "") 
      echo "<a href='aim:goim?screenname=".$rec[0]["aim"]."'>Contact via AIM</a>";
    echo "</td></tr><tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>MSN:</strong></div></td><td class='pro_area_1'>";
		if (@$rec[0]["msn"] != "") 
      echo "<a href='http://members.msn.com/".$rec[0]["msn"]."' target='_blank'>Contact via MSN</a>";
    echo "</td></tr><tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>Yahoo!</strong></div></td><td class='pro_area_1'>";
		if (@$rec[0]["yahoo"] != "") 
      echo "<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$rec[0]["yahoo"]."&.src=pg'>Contact via Yahoo!</a>";
    echo "</td></tr>";

    echoTableFooter(SKIN_DIR);
    for($i=1;$i<=6;$i++)
    {
    $custom = $config_tdb->basicQuery('config',"name","custom_profile$i");
    if (trim($custom[0]['value']) != "")
      $customs[] = array($custom[0]['value'],$rec[0]["custom_profile$i"]);
    }
    
    echoTableHeading("Viewing profile for ".$rec[0]["user_name"]."", $_CONFIG);
    echo "<tr><td valign='top' class='pro_area_1' width='20%'><div class='pro_area_2'><strong>Homepage:</strong></div></td><td class='pro_area_1'>";
		if (strlen($rec[0]['url']) != 0)
    if ($rec[0]['url'] != "http://")
    echo "<a href='".$rec[0]["url"]."' target='_blank'>".$rec[0]["url"]."</a>";
    echo "</td></tr>\n<tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>Email: </strong></div></td><td class='pro_area_1'>";
		if ((bool)$rec[0]["view_email"]) echo "<a href='mailto:".$rec[0]["email"]."'>".$rec[0]["email"]."</a>";
		else echo "not public";
		echo "</td></tr>\n";
  echo "<tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>Location:</strong></div></td><td class='pro_area_1'>".$rec[0]["location"]."</td></tr>\n";
    foreach ($customs as $key => $value)
		{
        echo "
			<tr><td class='pro_area_1' valign='top'><div class='pro_area_2'><strong>".$value[0].":</strong></div></td><td class='pro_area_1'>".nl2br($value[1])."</td></tr>\n";
    }
	
  	
		if (@$rec[0]["sig"] != "") echo "
				<tr>
					<td id='bottomcontent' colspan='2'>
						<div class='pro_sig_name'>".$rec[0]["user_name"]."'s Signature:</div>
						<div class='pro_sig_area'>
							<div class='pro_signature'>".format_text(UPBcoding(filterLanguage($rec[0]["sig"], $_CONFIG)))."</div>
						</div></td>
				</tr>";
		echoTableFooter(SKIN_DIR);
    echo "////////////BASIC IMPLEMENTATION USING FIXED DATA\\\\\\\\\\\\\\\\\\\\\\\\";
    echoTableHeading("Topic Subscriptions", $_CONFIG);
    echo "<tr><th width='50%'>Thread</th><th>Thread Starter</th><th>Last Post</th></tr>";
    $catid=3;
    $threadid=2;
    $posts_tdb = new posts(DB_DIR."/", "posts.tdb");
    $posts_tdb->setFp("topics", $catid."_topics");
	  $posts_tdb->set_forum($fRec);
	  $tRecs1 = $posts_tdb->query("topics", "id>'0'", 1);
	  foreach ($tRecs1 as $tRec){
    $posts_tdb->set_topic(array($tRec));
	  echo "<tr><td class='area_2'>".$tRec["subject"]."</td><td class='area_2'>".$tRec["topic_starter"]."</td><td class='area_1'><span class='latest_topic'><span class='date'>".gmdate("M d, Y g:i:s a", user_date($tRec["last_post"]))."</span>
				<br />
				<strong>By:</strong> ";
				if ($tRec["user_id"] != "0") echo "<span class='link_2'><a href='profile.php?action=get&id=".$tRec["user_id"]."' style='color : #".$status_config['statuscolor'].";'>".$tRec["user_name"]."</a></span></td></tr>";
    }
    echoTableFooter(SKIN_DIR);
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
		echo "<form action='{$_SERVER['PHP_SELF']}' id='newentry' name='newentry' method='post' enctype=\"multipart/form-data\">";
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
				<td class='area_2'><input type='password' name='u_oldpass' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>New password:</strong></td>
				<td class='area_2'><input type='password' name='u_newpass' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>New password confirmation:</strong></td>
				<td class='area_2'><input type='password' name='u_newpass2' /></td>
			</tr>";
		if ($_COOKIE["power_env"] >= 2) {
			echo "
			<tr>
				<td class='area_1'><strong>email:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_email' value='".$rec[0]["email"]."' />&nbsp;".$rec[0]["email"]."</td>
			</tr>";
		} else {
			echo "
			<tr>
				<td class='area_1'><strong>email:</strong><br /><font size='1' face='$font_face'>Email the Forum Administrator to change your email address.</a></td>
				<td class='area_2'><input type='hidden' name='u_email' value='".$rec[0]["email"]."' />&nbsp;".$rec[0]["email"]."</td>
			</tr>";
		}
		if ((bool) $rec[0]["view_email"]) $email_checked = "CHECKED";
		else $email_checked = "";
		echo "
			<tr>
				<td class='area_1'>Make email address public in profile?&nbsp;&nbsp;&nbsp;
					<a href=\"javascript: window.open('privacy.php','','status=no, width=800,height=50'); void('');\">
					Privacy Policy</a></td>
				<td class='area_2'><input type='checkbox' name='show_email' value = '1' $email_checked /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>location:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_loca' value='".$rec[0]["location"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter(SKIN_DIR);

		$custom_avatar = (($rec[0]['posts'] >= $_REGIST['newuseravatars'] || $_COOKIE['power_env'] > 1) && $_REGIST['custom_avatars']);
		echoTableHeading("Avatar Options", $_CONFIG);
		echo "
			<tr>
				<th style='text-align:center;'>Current avatar</th>
				<th style='text-align:center;'>Select a local avatar</th>".(($custom_avatar) ? "
				<th style='text-align:center;'>".(($_REGIST['custom_avatars'] == '2') ? 'Upload' : 'Link')." an avatar</th>" : "")."
			</tr>
			<tr>
				<td class='area_1' valign='middle' style='width:40%;text-align:center;padding:20px;height:150px;'>";
		if (@$rec[0]["avatar"] != "") echo "<img src=\"".$rec[0]["avatar"]."\" border='0'><br />";
		else echo "<img src='images/avatars/noavatar.gif' alt='' title='' />";
		echo "</td>
				<td class='area_2' valign='middle' style='width:50%;text-align:center;padding:20px;height:150px;'>
					<table cellspacing='0px' style='width:100%;'>
						<tr>
							<td style='text-align:center;width:50%;'>
								<img src='images/avatars/blank.gif' id='myImage' alt='' title='' /></td>
							<td><select class='select' size='5' name='avatar' onchange='swap(this.options[selectedIndex].value)'>\n";

		returnimages();
		echo "</select></td></tr>
					</table>
				</td>";

    if($custom_avatar) {
		    echo "<td class='area_1' valign='middle' style='width:45%;text-align:center;padding:20px;height:150px;'><input type='".(($_REGIST['custom_avatars'] == '2' && $_CONFIG["avatarupload_size"] > 0) ? "file'" : "text' value=''")." name='avatar2' /><p><i>Max avatar size is ".$_CONFIG["avatar_width"]."x".$_CONFIG["avatar_height"]."px.<br />".(($_REGIST['custom_avatars'] == '2') ? 'Valid filetypes: jpg, jpeg and gif.<br />Maximum filesize is '.$_CONFIG["avatarupload_size"].'Kb.' : '')."</i></p></td></tr>";
		}
		echo "
			<tr>
				<td class='footer_3' colspan='".(($custom_avatar) ? '3' : '2')."'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter(SKIN_DIR);
		echoTableHeading("Other Information", $_CONFIG);
		echo "
			<tr>
				<td class='area_1' style='width:35%;'><strong>Homepage:</strong></td>
				<td class='area_2'><input type='text' size='50' name='u_site' value='";
    if ($rec[0]["url"] == '')
      echo "http://";
    else
      echo $rec[0]["url"];
    echo "' /></td>
			</tr>
			";
    
    for ($i=1;$i<=6;$i++)
    {
      $custom = $config_tdb->basicQuery('config',"name","custom_profile$i");
      if (trim($custom[0]['value']) != "")
      echo "<tr>
				<td class='area_1'><strong>{$custom[0]['value']}</strong></td>
				<td class='area_2'><textarea cols='60' name='custom_profile$i' />".$rec[0]["custom_profile$i"]."</textarea></td>
			</tr>";
    }
    echo "<tr>
				<td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>ICQ:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_icq' value='".$rec[0]["icq"]."' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>AIM:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_aim' value='".$rec[0]["aim"]."' /> </td>
			</tr>
			<tr>
				<td class='area_1'><strong>Yahoo!:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_yahoo' value='".$rec[0]["yahoo"]."' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>MSN/Windows Live Messenger:</strong></td>
				<td class='area_2'><input type='text' size='25' name='u_msn' value='".$rec[0]["msn"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td>
			</tr>
    <tr>
				<td class='area_1' valign='top'><strong>Signature:</strong></td>
				<td class='area_2'>".bbcodebuttons('u_sig','sig')."<textarea id='u_sig' name='u_sig' cols='45' rows='10'>".$rec[0]["sig"]."</textarea><br /><input type='button' onclick=\"javascript:sigPreview(document.getElementById('newentry'),'".$_COOKIE['id_env']."','set');\" value='Preview Signature' /></td></tr>
			<tr>
				<td class='area_1' valign='top'><div id='sig_title'><strong>Current Signature:</strong></div></td>
				<td class='area_2'><div style='display:inline;' id='sig_preview'>".format_text(filterLanguage(UPBcoding($rec[0]["sig"]), $_CONFIG))."</div></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Timezone Setting:</strong></td>
				<td class='area_2'>";
      print timezonelist($rec[0]["timezone"]);
			echo "</td></tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='reset' name='reset' value='Reset' onclick=\"javascript:sigPreview(document.getElementById('newentry'),'".$_COOKIE['id_env']."','reset');\" /><input type='submit' name='u_edit' value='Submit' /></td>
			</tr>";
    echoTableFooter(SKIN_DIR);
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
		<td class='area_2' onmouseover=\"this.className='area_2_over'\" onmouseout=\"this.className='area_2'\">
			<span class='link_1'>".$tRec[0]["subject"].$r_ext."</span>
			<div class='description'>Started By:&nbsp;<span style='color:#".$statuscolor."'>".$tRec[0]["topic_starter"]."</span></div>
			<div class='box_posts'><strong>Views:</strong>&nbsp;".$tRec[0]["views"]."</div>
			<div class='box_posts'><strong>Replies:</strong>&nbsp;".$tRec[0]["replies"]."</div></td>
		<td class='area_1' style='text-align:center;'>
			<img src='icon/".$tRec[0]["icon"]."' class='post_image'>
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
