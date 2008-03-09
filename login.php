<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	// Ultimate PHP Board Login
	require_once('./includes/class/func.class.php');
	$where = "Login";
	$show = 0;
	$e = 0;
	if (isset($_POST["u_name"]) && isset($_POST["u_pass"])) {
		// Attempt to login
		if (($r = $tdb->login_user($_POST["u_name"], $_POST["u_pass"], $key)) === FALSE) {
			$error = "
		<div class='alert'>
			<div class='alert_text'>
				<strong>Access Denied!</strong></div><div style='padding:4px;'>Either your Username or your Password was incorrect.</div>
		</div><br />";
		} else {
			//lastvisit info

			//NEW VERSION
      $ses_info = $r['lastvisit'];
      if ($ses_info == 0)
        $ses_info = mkdate();
      $tdb->edit("users",$r["id"],array('lastvisit'=>mkdate()));

			if (headers_sent()) $error_msg = 'Could not login: headers sent.';
			else
			{
				setcookie("lastvisit", $ses_info);
				//end lastvisit info
				$r['uniquekey'] = generateUniqueKey();
				$tdb->edit('users', $r['id'], array('uniquekey' => $r['uniquekey']));
				if ($_POST["remember"] == "YES") {
					setcookie("remember", '1', (time() + (60 * 60 * 24 * 7)));
					setcookie("user_env", $r["user_name"], (time() + (60 * 60 * 24 * 7)));
					setcookie("uniquekey_env", $r["uniquekey"], (time() + (60 * 60 * 24 * 7)));
					setcookie("power_env", $r["level"], (time() + (60 * 60 * 24 * 7)));
					setcookie("id_env", $r["id"], (time() + (60 * 60 * 24 * 7)));
				} else {
					setcookie("remember", '');
					setcookie("user_env", $r["user_name"]);
					setcookie("uniquekey_env", $r["uniquekey"]);
					setcookie("power_env", $r["level"]);
					setcookie("id_env", $r["id"]);
				}
				setcookie("timezone", $r["timezone"], (time() + (60 * 60 * 24 * 7)));
				if ($_GET["ref"] == "") $_GET["ref"] = "index.php";
				$error = "
					<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Redirecting:</strong></div><div style='padding:4px;'>Logged on successfully as user:
					<br />
					".$r["user_name"]."
					</div>
					</div>
					<meta http-equiv='refresh' content='2;URL=".$_GET["ref"]."'>";
			}
			$e = 1;
		}
	}
	require_once('./includes/header.php');
	if (!$tdb->is_logged_in() != "") {
		if (isset($error)) {
			echo "$error";
			if ($e == 1) exitPage("");
			}
		if ($_COOKIE["remember"] != "") $remember = "checked";
		else $remember = "";
		echo "
	<form action='login.php?ref=".$_GET["ref"]."' method=POST>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "
			<tr>
				<td class='area_1' style='width:40%;text-align:right;'><strong>User Name:</strong></td>
				<td class='area_2'><input class='txtBox' type=text name=u_name size=30 value=".$_POST["u_name"]."></td>
			</tr>
			<tr>
				<td class='area_1' style='text-align:right;'><strong>Password:</strong></td>
				<td class='area_2'><input class='txtBox' type=password name=u_pass size=30></td>
			</tr>
			<tr>
				<td class='area_1' style='text-align:right;'>&nbsp;</td>
				<td class='area_2'><input type='checkbox' name='remember' value='YES' id='rememberme' ".$remember."><label for='rememberme'>&nbsp;&nbsp;Remember me?</label></td>
			</tr>
			<tr>
				<td class='footer_3a' style='text-align:center;' colspan='2'><input type='submit' class='txtBox' value='Login'>&nbsp;&nbsp;&nbsp;<a href='getpass.php'>(Lost Password?)</a> <a href='register.php'>(Need to Register?)</a></td>
			</tr>";
      echoTableFooter($_CONFIG['skin_dir']);
      echo "</form>";
	} else {
		echo "
		<div class='alert'>
			<div class='alert_text'>
				<strong>You are already logged in:</strong></div><div style='padding:4px;'><a href='logoff.php'>Log off</a></div>
		</div>";
	}
	require_once('./includes/footer.php');
?>
