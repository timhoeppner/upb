<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	session_start();
  if (!headers_sent()) {
		switch (basename($_SERVER['PHP_SELF'])) {
			case 'register.php':
			case 'profile.php':
			case 'newpost.php':
			case 'newpm.php':
			header ("Cache-control: private");
			break;
			default:
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header ("Cache-Control: no-cache, must-revalidate");
			header ("Pragma: no-cache");
			break;
		}
	}
	if (!defined('DB_DIR')) die('The constant, DB_DIR has not been defined.  Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
	if (!is_array($_CONFIG)) die('UPB Arrays have not been initialized.  Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
	if ($_CONFIG['skin_dir'] == '') die('SKIN_DIR not set ("'.SKIN_DIR.'").  This may be an indication that your config data was not set.');
	$banned_addresses = file(DB_DIR.'/banneduser.dat');
	foreach($banned_addresses as $address)
	if (trim($address) == $HTTP_SERVER_VARS['REMOTE_ADDR']) {
		if (!headers_sent()) {
			setcookie("banned", "User is banned", time()+9999 * 99999 * 999999);
			header("location: http://www.google.com/");
		}
		exit;
	}
	if (isset($_COOKIE["user_env"])) {
		$banned_addresses = file(DB_DIR.'/banneduser.dat' );
		foreach($banned_addresses as $address )
		if (trim($address) == $_COOKIE["user_env"]) {
			if (!headers_sent()) {
				setcookie("banned", "User is banned", time()+9999 * 99999 * 999999);
				header("location: about:blank");
			}
			exit;
		}
	}
	if (isset($_COOKIE["banned"])) {
		if (!headers_sent()) header("location: about:blank");
		exit;
	}
	$mt = explode(' ', microtime());
	$script_start_time = $mt[0] + $mt[1];
	if ($tdb->is_logged_in() && INSTALLATION_MODE === FALSE) {
		$refresh = false;
		if (!isset($_COOKIE["lastvisit"])) {
			$r = $tdb->basicQuery("users",'id',$_COOKIE['id_env']);
      //NEW VERSION
      $ses_info = $r['lastvisit'];
      if ($ses_info == 0)
        $ses_info = mkdate();
      $tdb->edit("users",$_COOKIE["id_env"],array('lastvisit'=>mkdate()));
      
			if (!headers_sent()) {
        $uniquekey = generateUniqueKey();
				$tdb->edit('users', $_COOKIE['id_env'], array('uniquekey' => $uniquekey));
				//setcookie("thisvisit", $v_date);
				setcookie("lastvisit", $ses_info); //time of this login/view
				setcookie("previousvisit",$r['lastvisit']); //time of previous login/view
				setcookie("timezone", $_COOKIE["timezone"], (time() + (60 * 60 * 24 * 7)));
				if (isset($_COOKIE["remember"])) {
					setcookie("remember", 1, (time() + (60 * 60 * 24 * 7)));
					setcookie("user_env", $_COOKIE["user_env"], (time() + (60 * 60 * 24 * 7)));
					setcookie("uniquekey_env", $uniquekey, (time() + (60 * 60 * 24 * 7)));
					setcookie("power_env", $_COOKIE["power_env"], (time() + (60 * 60 * 24 * 7)));
					setcookie("id_env", $_COOKIE["id_env"], (time() + (60 * 60 * 24 * 7)));
				}
			}
			$refresh = true;
		}
		if ($refresh && $_GET["a"] != 1 && $_POST["a"] != 1 && $_GET["s"] != 1 && $_POST["s"] != 1) redirect($_SERVER['PHP_SELF']."?".$QUERY_STRING, 0);
	} else {
		if (!isset($_COOKIE["timezone"]) && !headers_sent()) setcookie("timezone", "0", (time() + (60 * 60 * 24 * 7)));
	}
	if (isset($_COOKIE['password_env'])) {
		setcookie('password_env', '', time() - 3600);
		redirect($_SERVER['PHP_SELF']."?".$QUERY_STRING, 0);
	}
	$h_f = fopen(DB_DIR."/hits_today.dat", "r");
	$hits = explode(":", fread($h_f, filesize(DB_DIR."/hits_today.dat")));
	fclose($h_f);
	$h_f = fopen(DB_DIR."/hits_record.dat", "r");
	$hits_r = explode(":", fread($h_f, filesize(DB_DIR."/hits_record.dat")));
	fclose($h_f);
	$day = date("d");
	if (date("d", $hits[0]) != $day) {
		//in place for debugging
		//echo "<font size=1>xxx</font>";
		$hits[0] = time();
		$hits[1] = 0;
	}
	$hits[1] += 1;
	$hits_today = $hits[1];
	if ($hits_today > $hits_r[1]) {
		//New record
		$hits_r[0] = date("M j, Y");
		$hits_r[1] = $hits_today;
		$h_f = fopen(DB_DIR."/hits_record.dat", "w");
		fwrite($h_f, implode(":", $hits_r));
		fclose($h_f);
	}
	$hits_date = $hits_r[0];
	$hits_record = $hits_r[1];
	$h_f = fopen(DB_DIR."/hits_today.dat", "w");
	flock($h_f, 2);
	fwrite($h_f, implode(":", $hits));
	flock($h_f, 3);
	fclose($h_f);
	if (!defined('SKIN_DIR')) die('The constant, SKIN_DIR has not been defined. Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
	$login = "";
	if (!$tdb->is_logged_in()) {
		$login = "You are not logged in.";
		$loginlink = "login.php?ref=";
		$pm_display = "login.php?ref=pmsystem.php";
	} else {
		lastread();
    $login = "Welcome, ".$_COOKIE["user_env"]."!";
		$loginlink = "logoff.php";
		$pm_display = "pmsystem.php";
		$f = fopen(DB_DIR."/new_pm.dat", 'r');
		fseek($f, (((int)$_COOKIE["id_env"] * 2) - 2));
		$new_pm = fread($f, 2);
		fclose($f);
		if ((int)$new_pm != 0) $pm_alert .= "-&nbsp;<a href='pmsystem.php?section=inbox'><strong>".$new_pm."</strong> new PMs in your inbox</a>";
		else $pm_alert .= "-&nbsp;No new messages";
		$mark_all_read .= "<a href='setallread.php'>Mark all posts read</a>";
		if ($_COOKIE["power_env"] == 3) $adminlink .= "<a href='admin.php'>Admin Panel</a>&nbsp;&middot;";
	}
	//Start Header
	echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>".(($where == '') ? $_CONFIG['title'] : (strip_tags(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where))))."</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<link rel='stylesheet' type='text/css' href='".$_CONFIG["skin_dir"]."/css/style.css' />
<script type='text/javascript' src='./includes/scripts/formsubmit.js'></script>
<script type='text/javascript' src='./includes/scripts/form_field_limiter.js'></script>
<!--<script type='text/javascript' src='./includes/scripts/hover.js'></script>-->
<script type='text/javascript' src='./includes/scripts/images_switch.js'></script>
<script language=\"Javascript\" src=\"./includes/bbcode.js\"></script>
<script language=\"Javascript\" src=\"./includes/ajax.js\"></script>
<script language=\"Javascript\" src=\"./includes/scripts.js\"></script>
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='".$_CONFIG["skin_dir"]."/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />
	<div id='tabstyle_1'>
		<ul>";
	if ($tdb->is_logged_in()) echo "
			<li><a href='index.php' title='Forum Home'><span>Forum Home</span></a></li>
			<li><a href='".$_CONFIG["homepage"]."' title='Site Home'><span>Site Home</span></a></li>
			<li><a href='showmembers.php' title='Members'><span>Members</span></a></li>
			<li><a href='search.php' title='Search'><span>Search</span></a></li>
			<li><a href='board_faq.php' title='Help Faq'><span>Help Faq</span></a></li>";
	else echo "
			<li><a href='index.php' title='Forum Home'><span>Forum Home</span></a></li>
			<li><a href='".$_CONFIG["homepage"]."' title='Site Home'><span>Site Home</span></a></li>
			<li><a href='search.php' title='Search'><span>Search</span></a></li>
			<li><a href='board_faq.php' title='Help Faq'><span>Help Faq</span></a></li>";
	echo "
		</ul>
	</div>
	<div style='clear:both;'></div>
		";
	echoTableHeading($_CONFIG['title'], $_CONFIG);
	echo "
		<tr>
			<td class='area_welcome'><div class='welcome_text'>";
	if ($tdb->is_logged_in()) echo "
				<strong>$login</strong>&nbsp;&nbsp;
				$adminlink
				<a href='$loginlink'>Logout</a>
				&middot;&nbsp;<a href='profile.php'>User CP</a>
				&middot;&nbsp;<a href='$pm_display'>Messenger</a>
				$pm_alert";
	else echo "
				<strong>$login</strong>
				Please <a href='register.php'><strong>Register</strong></a>
				or <a href='$loginlink'><strong>Login</strong></a>";
	echo "
			</div></td>
		</tr>";
		echoTableFooter($_CONFIG['skin_dir']);
	//login information
	
  if (!$tdb->is_logged_in() && isset($_COOKIE['user_env']) && isset($_COOKIE['uniquekey_env']) && isset($_COOKIE['id_env'])) {
		$redirect = urlencode($_SERVER['REQUEST_URI']);
		echo "
	<div class='alert'>
		<div class='alert_text'><strong>Attention:</strong></div>
		<div style='padding:4px;'>You or another person logged in on a different computer since the last time you've visited.
			<br />
			<a href=\"logoff.php?ref={$redirect}\">Don't show this message anymore</a> or <a href=\"login.php?ref={$redirect}\">Login</a>.</div>
	</div>";
	}
	echo "

	<div class='breadcrumb'><span class='breadcrumb_home'><a href='index.php'>".$_CONFIG["title"]."</a></span>";
	if (isset($where)) echo "&nbsp;<span class='breadcrumb_page'>".$_CONFIG["where_sep"]." ".$where."</span>";
	echo "
	</div>";
	//End Header
	//begining INSTALLATION MODE
	if (INSTALLATION_MODE === TRUE && (FALSE === eregi('admin', $_SERVER['PHP_SELF'])) && (FALSE === strpos($_SERVER['PHP_SELF'], 'install')) && (FALSE === strpos($_SERVER['PHP_SELF'], 'update')) && (FALSE === strpos($_SERVER['PHP_SELF'], 'upgrade'))) {
		echo 'The forum is in installation mode. Cannot continue.';
		if ($tdb->is_logged_in() && $_COOKIE['power_env'] === 3) echo 'You may access the <a href="admin.php">Admin Panel</a> to switch INSTALLATION_MODE off.';
		require('./includes/footer.php');
		exit;
	}
	if ($_GET['SHOW'] == 'COOKIES') {
		print '<pre>';
		foreach($GLOBALS["_COOKIE"] as $varname => $varvalue) {
			print $varname."\t= ".$varvalue."\n";
		}
		print '</pre>';
		//echo "\$user_env = ".$_COOKIE["user_env"]."<br>";
		//if(isset($_COOKIE['pass_env'])) echo "\$pass_env = ".$_COOKIE['pass_env']."<br>";
		//if(isset($_COOKIE['password_env'])) echo "\$password_env = ".$_COOKIE["password_env"]."<br>strlen(\$password_env):".strlen($_COOKIE["password_env"])."<br>";
		//echo "\$uniquekey_env = ".$_COOKIE["uniquekey_env"]."<br>\$power_env = ".$_COOKIE["power_env"]."<br>\$id_env = ".$_COOKIE["id_env"]."<br><br>";
		//echo "\$remember = ".$_COOKIE['remember']."<br>";
		//echo "\$lastvisit = ".gmdate("M d, Y g:i:s a", $_COOKIE["lastvisit"])." (".$_COOKIE["lastvisit"].")<br><br>";
	}
?>
