<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/upb.initialize.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <A href='admin_members.php'>Manage Members</a>";
	require_once("./includes/header.php");
	if (!$tdb->is_logged_in() || $_COOKIE["power_env"] < 3) exitPage("
		<div class='alert'><div class='alert_text'>
		<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>");
	if ($_GET["action"] == "edit") {
		if (!isset($_GET["id"])) exitPage("
				<div class='alert'><div class='alert_text'>
				<strong>Error!</strong></div><div style='padding:4px;'>No id selected!</div></div>");
		$rec = $tdb->get("users", $_GET["id"]);
		if (isset($_POST["a"])) {
			if (!isset($_POST["email"])) exitPage("
				<div class='alert'><div class='alert_text'>
				<strong>Error!</strong></div><div style='padding:4px;'>please enter a valid email!</div></div>");
			if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["email"])) exitPage("
				<div class='alert'><div class='alert_text'>
				<strong>Error!</strong></div><div style='padding:4px;'>please enter a valid email!</div></div>");
			if (strlen(chop($_POST["sig"])) > 200) exitPage("
				<div class='alert'><div class='alert_text'>
				<strong>Error!</strong></div><div style='padding:4px;'>You cannot have more than 200 characters in the signature.</div></div>");
			if (substr(trim(strtolower($_POST["url"])), 0, 7) != "http://") $_POST["url"] = "http://".$_POST["url"];
			if ($_POST["timezone"] {
				0 }
			== '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);
			$new = array();
			if ($_POST["level"] != $rec[0]["level"]) $new["level"] = $_POST["level"];
			if ($_POST["email"] != $rec[0]["email"]) $new["email"] = $_POST["email"];
			if ($_POST["status"] != $rec[0]["status"]) $new["status"] = $_POST["status"];
			if ($_POST["location"] != $rec[0]["location"]) $new["location"] = $_POST["location"];
			if ($_POST["url"] != $rec[0]["url"]) $new["url"] = $_POST["url"];
			if ($_POST["avatar"] != $rec[0]["avatar"]) $new["avatar"] = $_POST["avatar"];
			if ($_POST["icq"] != $rec[0]["icq"]) $new["icq"] = $_POST["icq"];
			if ($_POST["yahoo"] != $rec[0]["yahoo"]) $new["yahoo"] = $_POST["yahoo"];
			if ($_POST["msn"] != $rec[0]["msn"]) $new["msn"] = $_POST["msn"];
			if (chop($_POST["sig"]) != $rec[0]["sig"]) $new["sig"] = chop($_POST["sig"]);
			if ($_POST["timezone"] != $rec[0]["timezone"]) $new["timezone"] = $_POST["timezone"];
			if (!empty($new)) $tdb->edit("users", $_GET["id"], $new);
			echo "
				<div class='alert_confirm'>
				<div class='alert_confirm_text'>
				<strong>Successfully edited: ".$rec[0]["user_name"]."!</div><div style='padding:4px;'>
				<a href='admin_members.php?page=".$_GET["page"]."'>Go Back to Member's list</a>
				</div>
				</div>";
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
			</tr>";
			echoTableFooter($_CONFIG['skin_dir']);
			echo "<form method='POST' action=".$PHP_SELF."?action=edit&id=".$_GET["id"]."&page=".$_GET["page"]."><input type='hidden' name='a' value='1'>";
		echoTableHeading("Editing member: ".$rec[0]["user_name"]."", $_CONFIG);
			echo "
			<tr>
				<th colspan='2'>Complete the information below to edit this member</th>
			</tr>
			<tr>
				<td class='area_1' style='width:25%;padding:8px;'><strong>".$rec[0]["user_name"].":</strong></td>
				<td class='area_2'><span class='link_1'><a href='admin_members.php?action=pass&id=".$_GET['id']."'>Change Password?</a></span></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>User group:</strong></td>
				<td class='area_2'>";
          echo "<select size='1' name='level'>".createUserPowerMisc($rec[0]["level"], 7, TRUE);
        echo "</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>E-mail Address:</strong></td>
				<td class='area_2'><input type='text' name='email' size='20' value='".$rec[0]["email"]."' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Public E-mail?</strong></td>
				<td class='area_2'>";
			if ($rec[0]["view_email"] == 1) echo "YES";
			else echo "NO";
			echo "</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Sign up on the Mailing List?</strong></td>
				<td class='area_2'>";
			if ($rec[0]["mail_list"] == 1) echo "YES";
			else echo "NO";
			$f = fopen(DB_DIR."/new_pm.dat", 'r');
			fseek($f, (((int)$rec[0]["id"] * 2) - 2));
			$tmp_new_pm = fread($f, 2);
			fclose($f);
      $lastvisit = $rec[0]['lastvisit'];
			echo "</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>No. of Unread Private Messages:</strong></td>
				<td class='area_2'>".$tmp_new_pm."</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Status:</strong></td>
				<td class='area_2'><input type='text' name='status' size='20' value='".$rec[0]["status"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Location:</strong></td>
				<td class='area_2'><input type='text' name='location' size='20' value='".$rec[0]["location"]."' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Website:</td>
				<td class='area_2'><input type='text' name='website' size='20' value='".$rec[0]["url"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Avatar:</strong></td>
				<td class='area_2'><input type='text' name='avatar' size='20' value='".$rec[0]["avatar"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='bar_msnm' style='padding:8px;'><strong>MSN:</strong></td>
				<td class='area_2'><input type='text' name='msn' size='20' value='".$rec[0]["msn"]."' /></td>
			</tr>
			<tr>
				<td class='bar_yim' style='padding:8px;'><strong>YIM:</strong></td>
				<td class='area_2'><input type='text' name='yahoo' size='20' value='".$rec[0]["yahoo"]."' /></td>
			</tr>
			<tr>
				<td class='bar_icq' style='padding:8px;'><strong>ICQ:</strong></td>
				<td class='area_2'><input type='text' name='icq' size='20' value='".$rec[0]["icq"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;' valign='top'><strong>Signature:</strong></td>
				<td class='area_2'><textarea rows='10' name='sig' cols='45' rows='10'>".$rec[0]["sig"]."</textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Number of posts:</strong></td>
				<td class='area_2'>".$rec[0]["posts"]."</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Last login:</strong></td>
				<td class='area_2'>";
        if (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d'))
          echo '<i>today</i>';
        else if (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))))
          echo '<i>yesterday</i>';
        else
          echo gmdate("Y-m-d", user_date($lastvisit));
        echo "</td></tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Registered Date:</strong></td>
				<td class='area_2'>".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Time zone:</strong></td>
				<td class='area_2'><input type='text' name='timezone' size='20' value='".$rec[0]["timezone"]."' /></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='submit' value='Submit' name='B1' /><input type='reset' value='Reset' name='B2' /></td>
			</tr>";
      echoTableFooter($_CONFIG['skin_dir']);
      echo "</form>";
		}
	} elseif($_GET["action"] == "pass" && isset($_GET["id"])) {
		$user = $tdb->get("users", $_GET["id"]);
		if (isset($_POST["a"])) {
			if ($_POST["pass"] != $_POST["pass2"]) exitPage("The passwords don't match!");
			if (strlen($_POST["pass"]) < 4) exitPage("The password has to be longer then 4 characters");
			$tdb->edit("users", $_GET["id"], array("password" => generateHash($_POST["pass"])));
			$msg = "You Password was changed by ".$_COOKIE["user_env"]." on the website ".$_CONFIG["homepage"]." to \"".$_POST["pass"]."\"";
			if (isset($_POST["reason"])) $msg .= "\n\n".$_COOKIE["user_env"]."'s reason was this:\n".$_POST["reason"];
			$email_fail = false;
      if(!@mail($user[0]["email"], "Password Change Notification", "Password Changed by :".$_COOKIE["user_env"]."\n\n".$msg, "From: ".$_REGISTER["admin_email"]))
        $email_fail = true;
			echoTableHeading("Password changed!", $_CONFIG);
      echo "
		<tr>
			<td class='area_1'><div class='description'><strong>";
      echo "You successfully changed ".$user[0]["user_name"]."'s password to ".$_POST["pass"]."</strong>";
			if ($email_fail === true)
        echo "<p>The automated email was unable to be sent.<p>Please email them at ".$user[0]['email']." to inform them of the change of password";
      echo "</div></td></tr>";
      echoTableFooter(SKIN_DIR);
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
			</tr>";
			echoTableFooter($_CONFIG['skin_dir']);
			echo "<form method='POST' action=".$PHP_SELF."?action=pass&id=".$_GET["id"]."><input type='hidden' name='a' value='1'>";
		echoTableHeading("Setting a new password for: ".$user[0]["user_name"]."", $_CONFIG);
			echo "
			<tr>
				<th colspan='2'>Complete the information below to change the password for this member</th>
			</tr>
			<tr>
				<td class='area_1' style='width:25%;padding:8px;'><strong>New Password</strong></td>
				<td class='area_2'><input type='password' name='pass'></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Confirm Password</strong></td>
				<td class='area_2'><input type='password' name='pass2'></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Reason</strong></td>
				<td class='area_2'><textarea name=reason></textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='2'>An E-mail will be sent, notifying the user about the change of their password by <i>".$_COOKIE["user_env"]."</i></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='submit' value='Change Password'></td>
			</tr>";
    echoTableFooter($_CONFIG['skin_dir']);
    echo "</form>";
		}
	} elseif($_GET["action"] == "delete") {
		if (!isset($_GET["id"])) exitPage("No id selected.");
		$rec = $tdb->get("users", $_GET["id"]);
		if ($_POST["verify"] == "Ok") {
			$tdb->delete("users", $_GET["id"]);
			echo "
				<div class='alert_confirm'>
				<div class='alert_confirm_text'>
				<strong>Redirecting:</div><div style='padding:4px;'>
				Successfully deleted ".$rec[0]["user_name"].".<br /><a href='admin_members.php'>Go Back</a>
				</div>
				</div>";
		} elseif($_POST["verify"] == "Cancel") {
			echo "<meta http-equiv='refresh' content='0;URL=admin_members.php'>";
		} else {
			ok_cancel("admin_members.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete <strong><a href='profile.php?action=get&id=".$_GET["id"]."' targer='_blank'>".$rec[0]["user_name"]."</a></strong>?");
		}
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
			</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
		print '<a name="skip_nav">&nbsp;</a>';
		echoTableHeading("Search", $_CONFIG);
        ?><tr><td class='area_1' style='padding:8px;'><form action="admin_members.php#skip_nav" method="GET">Username: <input name="u" type="text" value="<?php print ((isset($_GET['u'])) ? $_GET['u'] : ''); ?>"><p><input type="submit" name="action" value="Search">&nbsp;&nbsp;<input type="submit" name="action" value="Clear"<?php print (($_GET['action'] == 'Search') ? '' : ' DISABLED'); ?>></form></td></tr><?php
        echoTableFooter(SKIN_DIR);
		if ($_GET["page"] == "") $_GET["page"] = 1;
		$start = ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"] + 1);
		if($_GET['action'] != 'Search') {
    		$users = $tdb->listRec("users", $start, $_CONFIG["topics_per_page"]);
    		$c = $tdb->getNumberOfRecords("users");
		} else {
		    $users = $tdb->query('users', "user_name?'{$_GET['u']}'", $start, $_CONFIG['topics_per_page']);
		}
		if ($c <= $_CONFIG["topics_per_page"]) $num_pages = 1;
		elseif (($c % $_CONFIG["topics_per_page"]) == 0) $num_pages = ($c / $_CONFIG["topics_per_page"]);
		else $num_pages = ($c / $_CONFIG["topics_per_page"]) + 1;
		$pageStr = createPageNumbers($_GET["page"], $num_pages, $_SERVER['QUERY_STRING']);
		echo "<table class='pagenum_container' cellspacing='1'>
			<tr>
				<td style='text-align:left;height:23px;'><span class='pagination_current'>Pages: </span>".$pageStr."</td>
			</tr>
		</table>";
		echoTableHeading("Current member management options", $_CONFIG);
		echo "
			<tr>
				<th style='width:5%;'>ID#</th>
				<th style='width:20%;'>Username</th>
				<th style='width:15%;text-align:center;'>User group</th>
				<th style='width:20%;'>Email</th>
				<th style='width:7%;text-align:center;'>Posts</th>
				<th style='width:12%;text-align:center;'>Last Login</th>
				<th style='width:12%;text-align:center;'>Registered</th>
				<th style='width:7%;text-align:center;'>Ban</th>
				<th style='width:7%;text-align:center;'>Edit</th>
				<th style='width:7%;text-align:center;'>Delete</th>
			</tr>";
		if ($users[0] == "") {
			echo "
			<tr>
				<td colspan='10'>No records found</td>
			</tr>";
		} else {

			$bList = file(DB_DIR."/banneduser.dat");
			foreach($users as $user) {
			   $lastvisit = $user['lastvisit'];
				//if(gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d')) $lastvisit =
				//(gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d') ? '<i>today</i>' : (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))) ? '<i>yesterday</i>' : gmdate("Y-m-d", user_date($lastvisit))))
				//show each user
				echo "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>".$user["id"]."</strong></td>
				<td class='area_2'><span class='link_1'><a href='profile.php?action=get&id=".$user["id"]."'>".$user["user_name"]."</a></span></td>
				<td class='area_1' style='text-align:center;'>".createUserPowerMisc($user["level"], 4)."</td>";
				if ($user['view_email']) echo "
				<td class='area_2'>".$user["email"]."</td>";
				else echo "
				<td class='area_2'><i>".$user["email"]."</i></td>";
				echo "
				<td class='area_1' style='text-align:center;'>".$user["posts"]."</td>
				<td class='area_2' style='text-align:center;'>";
        if ($lastvisit == 0)
          echo "<i>never</i>";
        else if (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d'))
          echo '<i>today</i>';
        else if (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))))
          echo "<i>yesterday</i>";
        else
          echo gmdate("Y-m-d", user_date($lastvisit))."</td>";
				echo "<td class='area_2' style='text-align:center;'>".gmdate("Y-m-d", user_date($user['date_added']))."</td>";
        echo "<td class='area_2' style='text-align:center;'>";
        if ($user['level'] != 9)
        {
          echo "<a href='admin_banuser.php?ref=admin_members.php?page=".$_GET["page"]."&action=";
				  if (!in_array($user["user_name"], $bList)) echo 'addnew&newword='.$user["user_name"]."'>";
				  else echo 'delete&word='.$user["user_name"]."'><strong>Un</strong>";
			 	  echo "Ban</a>";
        }
        echo "</td>";
				echo "<td class='area_1' style='text-align:center;'>";
        if (($user['level'] == 9 and $user['id'] == $_COOKIE['id_env']) or ($user['level'] != 9))
          echo "<a href='admin_members.php?action=edit&id=".$user["id"]."&page=".$_GET["page"]."'>Edit</a>";
        echo "</td>";
				echo "<td class='area_2' style='text-align:center;'>";
        if ($user['level'] != 9)
          echo "<a href='admin_members.php?action=delete&id=".$user["id"]."'>Delete</a>";
        echo "</td>
			</tr>";
			}
		}
		echo "
			<tr>
				<td class='footer_3' colspan='10'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='10'>An <i>italized</i> e-mail states that this member has chosen to have his/her email address non-viewable to all but admins.</td>
			</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
		echo "
		<table class='pagenum_container' cellspacing='1'>
			<tr>
				<td style='text-align:left;height:23px;'><span class='pagination_current'>Pages: </span>".$pageStr."</td>
			</tr>
		</table>";
	}
	require_once("./includes/footer.php");
?>
