<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once('./includes/class/func.class.php');
	$where = "User CP";
	if (isset($_POST["u_edit"])) {
		if (!($tdb->is_logged_in())) {
			echo "<html><head><meta http-equiv='refresh' content='2;URL=login.php?ref=profile.php'></head></html>";
			exit;
		} else {
			$rec = array();
			if (!isset($_POST["u_email"])) exitPage("please enter your email!", true);
			if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["u_email"])) exitPage("please enter a valid email!", true);
				$_POST['u_sig'] = stripslashes($_POST['u_sig']);
			if (strlen($_POST["u_sig"]) > 200) exitPage("You cannot have more than 200 characters in your signature.", true);
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
			if ($user[0]["u_sig"] != chop($_POST["u_sig"])) $rec["sig"] = chop($_POST["u_sig"]);
			if (substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://".$_POST["u_site"];
			if ($user[0]["url"] != $_POST["u_site"]) $rec["url"] = $_POST["u_site"];
			if ($_POST["u_timezone"] {
				0 }
			== '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);
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
			exitPage('Your profile has been changed successfully.'.$ht, true);
		}
	} elseif(isset($_GET["action"])) {
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
			if ($rec[0]["level"] == '1') {
				$statuscolor = $_STATUS["userColor"];
				if ($rec[0]["posts"] >= $_STATUS["member_post1"]) $status = $_STATUS["member_status1"];
				elseif($rec[0]["posts"] >= $_STATUS["member_post2"]) $status = $_STATUS["member_status2"];
				elseif($rec[0]["posts"] >= $_STATUS["member_post3"]) $status = $_STATUS["member_status3"];
				elseif($rec[0]["posts"] >= $_STATUS["member_post4"]) $status = $_STATUS["member_status4"];
				elseif($rec[0]["posts"] >= $_STATUS["member_post5"]) $status = $_STATUS["member_status5"];
			} elseif($rec[0]["level"] == '2') {
				$statuscolor = $_STATUS["modColor"];
				if ($rec[0]["posts"] >= $_STATUS["mod_post1"]) $status = $_STATUS["mod_status1"];
				elseif($rec[0]["posts"] >= $_STATUS["mod_post2"]) $status = $_STATUS["mod_status2"];
				elseif($rec[0]["posts"] >= $_STATUS["mod_post3"]) $status = $_STATUS["mod_status3"];
				elseif($rec[0]["posts"] >= $_STATUS["mod_post4"]) $status = $_STATUS["mod_status4"];
				elseif($rec[0]["posts"] >= $_STATUS["mod_post5"]) $status = $_STATUS["mod_status5"];
			} elseif($rec[0]["level"] == '3') {
				$statuscolor = $_STATUS["adminColor"];
				if ($rec[0]["posts"] >= $_STATUS["admin_post1"]) $status = $_STATUS["admin_status1"];
				elseif($rec[0]["posts"] >= $_STATUS["admin_post2"]) $status = $_STATUS["admin_status2"];
				elseif($rec[0]["posts"] >= $_STATUS["admin_post3"]) $status = $_STATUS["admin_status3"];
				elseif($rec[0]["posts"] >= $_STATUS["admin_post4"]) $status = $_STATUS["admin_status4"];
				elseif($rec[0]["posts"] >= $_STATUS["admin_post5"]) $status = $_STATUS["admin_status5"];
			} else {
				$status = 'Member';
				$statuscolor = $_STATUS["membercolor"];
			}
			if ($rec[0]["status"] != "") $status = $rec[0]["status"];
			require_once('./includes/header.php');
			echo "";
			echoTableHeading("Viewing profile for ".$rec[0]["user_name"]."", $_CONFIG);
			echo "
				<tr>
					<td id='leftcontent'>
						<div style='text-align:center;font-weight:bold;'>
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
				echo "You are Blocked";
			} else {
				echo "<a href='newpm.php?to=".$_GET["id"]."' target='_blank'>Send private message?</a>";
			}
			echo "</div>
						</div>
						<div class='pro_container' style='margin-top:10px;'>
							<div class='pro_area_1'><div class='pro_area_2'><strong>Joined: </strong></div>".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."</div>
							<div class='pro_area_1'><div class='pro_area_2'><strong>Posts made: </strong></div>".$rec[0]["posts"]."</div>";

			if (@$rec[0]["url"] != "" || $rec[0]["url"] != "http://") echo "
				<div class='pro_area_1'><div class='pro_area_2'><strong>Homepage: </strong></div>
											<a href='".$rec[0]["url"]."' target='_blank'>".$rec[0]["url"]."</a>&nbsp;</div>";
			echo "</div>
				<br />
				<div class='pro_container'>
							<div class='pro_area_1' style='white-space:nowrap;'><div class='pro_area_2'><strong>Status: </strong></div>
								<span style='color:#".$statuscolor."'><strong>$status &nbsp;&nbsp;&nbsp;</strong></span></div>
							<div class='pro_area_1'><div class='pro_area_2'><strong>Email: </strong></div>";
			if ((bool)$rec[0]["view_email"]) echo "<a href='mailto:".$rec[0]["email"]."'>".$rec[0]["email"]."</a>";
			else echo "not public";
			echo "</div>";
			if (@$rec[0]["location"] != "") echo "
							<div class='pro_area_1'><div class='pro_area_2'><strong>Location: </strong></div>".$rec[0]["location"]."</div>";
			echo "</div></td>
					<td id='rightcontent' valign='top'>
							<div class='pro_contact'>";
			if (@$rec[0]["icq"] != "") echo "
								<strong>icq</strong><a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$rec[0]["icq"]."&action=message'><img src='images/icq.gif' border='0'>&nbsp;".$rec[0]["icq"]."</a>";
			if (@$rec[0]["aim"] != "") echo "
								<strong>aim</strong><a href='aim:goim?screenname=".$rec[0]["aim"]."'><img src='images/aol.gif' border='0'>&nbsp;".$rec[0]["aim"]."</a>";
			if (@$rec[0]["msn"] != "") echo "
								<strong>msn</strong><a href='http://members.msn.com/".$rec[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0'>&nbsp;".$rec[0]["msn"]."</a>";
			if (@$rec[0]["yahoo"] != "") echo "
								<strong>Y!</strong><a href='http://edit.yahoo.com/config/send_webmesg?.target=".$rec[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$rec[0]["yahoo"]."&m=g&t=0'>&nbsp;".$rec[0]["yahoo"]."</a>";
			echo "</div>
					<div class='pro_blank'>Just imagine what could go here!</div>
					<br />
					<div class='pro_blank2'>Or here!</div>
				";
			echo "</td>
					</tr>";
			if (@$rec[0]["sig"] != "") echo "
					<tr>
						<td id='bottomcontent' colspan='2'>
							<div class='pro_sig_name'>".$rec[0]["user_name"]."'s Signature:</div>
							<div class='pro_sig_area'>
								<div class='pro_signature'>".format_text(UPBcoding(filterLanguage($rec[0]["sig"], $_CONFIG["censor"])))."</div>
							</div></td>
					</tr>";
			echo "
			$skin_tablefooter";
			require_once('./includes/footer.php');
		}
	} else {
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
			echo "<form action='$PHP_SELF' method='post'>";
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
				</tr>
			$skin_tablefooter";
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
				</tr>
			$skin_tablefooter";
			echoTableHeading("Other Information", $_CONFIG);
			echo "
				<tr>
					<td class='area_1' style='width:45%;'><strong>homepage:</strong></td>
					<td class='area_2'><input type='text' name='u_site' value='".$rec[0]["url"]."'></td>
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
					<td class='area_1' valign='top'><strong>signature:</strong></td>
					<td class='area_2'><textarea name='u_sig' cols=45 rows=10>".$rec[0]["sig"]."</textarea></td>
				</tr>
				<tr>
					<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
				</tr>
				<tr>
					<td class='area_1'><strong>timezone setting:</strong><br />Set to how many hours you are from GMT.<br />Example: The North American Pacific Coast is \"-8\", whereas the city of Rome is just \"1\", or for London, just \"0\"</td>
					<td class='area_2'><input type='text' name='u_timezone' value='".$rec[0]["timezone"]."'></td>
				</tr>
				<tr>
					<td class='footer_3a' colspan='2' style='text-align:center;'><input type=reset name='reset' value='Reset'><input type='submit' name='u_edit' value='Submit'></td>
				</tr>
			$skin_tablefooter
			</form>";
			require_once('./includes/footer.php');
		}
	}
?>
