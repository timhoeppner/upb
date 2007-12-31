<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	// Ultimate PHP Board Register
	require_once('./includes/class/func.class.php');
	$where = "Register";
	$required = "#ff0000";
	if ($tdb->is_logged_in()) exitPage('You\'re already logged in.', true);
	if (empty($_POST["show_email"])) $_POST["show_email"] = "";
	if (empty($_POST["email_list"])) $_POST["email_list"] = "";
	if (!isset($_POST["submit"])) $_POST["submit"] = "";
	require_once('./includes/inc/encode.inc.php');
	session_start();
	if ($_POST["submit"] == "Submit") {
		if ($_POST['s_key'] !== $_SESSION["u_keycheck"]) {
			exitPage("Please enter the secrity code <b>exactly</b> as it appears...", true);
		}
		$_SESSION = array();
		setcookie(session_name(), '', time()-42000, '/');
		session_destroy();
		$_POST["u_login"] = strip_tags($_POST["u_login"]);
		$_POST["u_login"] = trim($_POST["u_login"]);
		if ($_POST["u_login"] == '' || $_POST["u_email"] == '') exitPage("You did not fill in all required fields! (*)", true);
		$q = $tdb->query("users", "user_name='".$_POST["u_login"]."'", 1, 1);
		if ($_POST["u_login"] == $q[0]["user_name"]) exitPage("The username you chose is already in use!", true);
		unset($q);
		$q = $tdb->query("users", "email='".$_POST["u_email"]."'", 1, 1);
		if ($_POST["u_email"] == $q[0]["email"]) exitPage("The email address you chose is already in use!", true);
		unset($q);
		$length = "3";
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		$num_vowels = count($vowels);
		$num_cons = count($cons);
		for($i = 0; $i < $length; $i++) {
			$u_pass .= $cons[rand(0, $num_cons - 1)].$vowels[rand(0, $num_vowels - 1)];
		}
		if ($_POST["show_email"] != "1") $_POST["show_email"] = 0;
		if (strlen($_POST["u_sig"]) > 200) exitPage("You cannot have more than 200 characters in your signature.", true);
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["u_email"])) exitPage("please enter a valid email!", true);
			$email = explode("@", $_POST["u_email"]);
		if(function_exists('checkdnsrr')) {
			if (!checkdnsrr($email[1], "MX")) exitPage("Please enter a valid email! No mail server seems to exist at <b>www.".$email[1]."</b>", true);
		}
		if (substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://" . $_POST["u_site"];
		if ($_POST["timezone"] {
			0 }
		== '+') $_POST["timezone"] = substr($_POST["timezone"], 1);
		$id = $tdb->add("users", array("user_name" => $_POST["u_login"], "password" => generateHash($u_pass), "level" => 1, "email" => $_POST["u_email"], "view_email" => $_POST["show_email"], "mail_list" => $_POST["email_list"], "location" => $_POST["u_loca"], "url" => $_POST["u_site"], "avatar" => $_POST["avatar"], "icq" => $_POST["u_icq"], "aim" => $_POST["u_aim"], "yahoo" => $_POST["u_yahoo"], "msn" => $_POST["u_msn"], "sig" => chop($_POST["u_sig"]), "posts" => 0, "date_added" => mkdate(), "timezone" => $_POST["u_timezone"]));
		// If each user sends and receives one PM a day, their table will last 67.2 years
		$temp_tdb = new tdb(DB_DIR."/", "privmsg.tdb");
		$pmT_num = ceil($id / 100);
		if (FALSE === $temp_tdb->isTable($pmT_num)) $temp_tdb->createTable($pmT_num, array(array("box", "string", 6), array("from", "number", 7), array("to", "number", 7), array("icon", "string", 10), array("subject", "memo"), array("date", "number", 14), array("message", "memo"), array("id", "id")));
		$temp_tdb->cleanup();
		unset($temp_tdb);
		$f = fopen(DB_DIR."/lastvisit.dat", 'a');
		fwrite($f, str_repeat(' ', 14));
		fclose($f);
		$f = fopen(DB_DIR."/new_pm.dat", 'a');
		fwrite($f, " 0");
		fclose($f);
		$register_msg = str_replace("<login>", $_POST['u_login'], $_REGISTER["register_msg"]);
		$register_msg = str_replace("<password>", $u_pass, $register_msg);
		if (!@mail($_POST["u_email"], $_REGISTER["register_sbj"], $register_msg, "From: ".$_REGISTER["admin_email"])) error_log ("Unable to send register email conformation to user: ".$_POST["u_login"], 3, "./logs/error.log");
		require_once('./includes/header.php');
		print "You are now registered!<BR><BR>An email has been sent to your email account with a random password, <br>which you can change at any time. It should arrive within 2 - 5 minutes. <br><br>Thank you for registering!";
		require_once('./includes/footer.php');
		redirect("login.php", "5");
		exit;
	} else {
		require_once('./includes/header.php');
		// security mod
		$string = md5(rand(0, microtime() * 1000000));
		$verify_string = substr($string, 3, 7);
		$key = md5(rand(0, 999));
		$encid = urlencode(md5_encrypt($verify_string, $key));
		// rather than the hidden field we have
		$_SESSION['u_keycheck'] = $verify_string;
		echo "<form action='register.php' method=POST>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "
			<tr>
				<th colspan='2' style='text-align:left;'><span style='color:$required;'>*</span> is a required field</th>
			</tr>
			<tr>
				<td class='area_1' style='width:45%;'> <strong>User Name:</strong> <span style='color:$required;'>*</span><br />Used for logging in and is your identity throughout the forum.</td>
				<td class='area_2'><input type=text name='u_login' size=40></td>
			</tr>
			<tr>
				<td class='area_1'>
					<strong>E-mail Address:</strong> <span style='color:$required;'>*</span><br />
					<span style='description'>Must be a valid email address (you@host.com). A random password is sent to the email address that you provide. If you use a hotmail email account, please be aware that there have been alot of missing activation emails. This is a hotmail problem.</span></td>
				<td class='area_2'><input type=text name=u_email size=40></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Confirm E-mail Address:</strong> <span style='color:$required;'>*</span><br />Must be a valid email address (you@host.com)</font></td>
				<td class='area_2'><input type=text name=u_email size=40></td>
			</tr>
			<tr>
				<td class='area_1'>
					<strong>Make email address visible to everyone?</strong></td>
				<td class='area_2'><input type=checkbox name=show_email value='1'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Security Code:</strong> <span style='color:$required;'>*</span><br />Please enter the code in the image: (all lower case)<br /><br />
					<a href='register.php'>Load new image?</a> (will reset this page)</td>
				<td class='area_2'><img src='./includes/image.php?id=$encid&key=$key'><br /><input type=text name=s_key maxlength=7 size=12></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
		$skin_tablefooter";
		echoTableHeading("Other Information", $_CONFIG);
		echo "
			<tr>
				<th colspan='2' style='text-align:left;'>These areas can also be completed at a later time in your account settings.</th>
			</tr>
			<tr>
				<td class='area_1' style='width:45%;'><strong>Location:</strong><br />Where are you from? (it can be anything)</td>
				<td class='area_2'><input type=text name='u_loca' size='4';</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Website URL:</strong><br />please include the http:// in front of url</td>
				<td class='area_2'><input type=text name=u_site size=40></td>
			</tr>
			<tr>
				<td class='area_1'>
					<strong>Avatar:</strong></td>
				<td class='area_2'>Choose an avatar in your UserCP after logging in.</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='bar_icq'><strong>ICQ:</strong><br />If you have ICQ put your number here</td>
				<td class='area_2'><input type=text name=u_icq size=40></td>
			</tr>
			<tr>
				<td class='bar_aim'><strong>AIM:</strong><br />If you have AOL Instant messanger, please type your SN</td>
				<td class='area_2'><input type=text name=u_aim size=40></td>
			</tr>
			<tr>
				<td class='bar_yim'><strong>Yahoo!:</strong><br />If you have Yahoo! messanger, please type your SN</td>
				<td class='area_2'><input type=text name=u_yahoo size=40></td>
			</tr>
			<tr>
				<td class='bar_msnm'><strong>MSN:</strong><br />If you have MSN Instant messanger please type your SN</td>
				<td class='area_2'><input type=text name=u_msn size=40></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Signature:</strong><br />Your signature is appended to each of your messages</td>
				<td class='area_2'><textarea name=u_sig cols=45 rows=10></textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>timezone setting</strong><br />Set to how many hours you are from GMT.<br>Example: The North American Pacific Coast is \"-8\", whereas the city of Rome is just \"1\", or for London, just \"0\"</font></font></td>
				<td class='area_2'><input type='text' name='u_timezone' value='0'></td>
			</tr>";
		echo "
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit name=submit value='Submit'></td>
			</tr>
		$skin_tablefooter
		</form>";
		require_once('./includes/footer.php');
		if (empty($_COOKIE["user_env"])) $user = "guest";
		else $user = $_COOKIE["user_env"];
		$month = date("m", time());
		$year = date("Y", time());
		if ($HTTP_SERVER_VARS['REMOTE_HOST'] == "") $visitor_info = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		else $visitor_info = $HTTP_SERVER_VARS['REMOTE_HOST'];
		$base = "http://" . $HTTP_SERVER_VARS['SERVER_NAME'] . $HTTP_SERVER_VARS['PHP_SELF'];
		$x1 = "host {$HTTP_SERVER_VARS['REMOTE_ADDR']} |grep Name";
		$x2 = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		$fp = fopen(DB_DIR."/iplog", "a");
		$date = "$month $year";
		fputs($fp, "$visitor_info -{$HTTP_SERVER_VARS['HTTP_USER_AGENT']} - $user - <br>Date/Time: $date $x1:$base:--------------------------------Next Person<p><br>\r\n");
		fclose($fp);
	}
?>