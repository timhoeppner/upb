<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	if (!defined('DB_DIR')) die('The constant, DB_DIR has not been defined.  Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
	$banned_addresses = file(DB_DIR.'/banneduser.dat');
	foreach($banned_addresses as $address)
	if (trim($address) == $HTTP_SERVER_VARS['REMOTE_ADDR']) {
		header ("location: http://www.catholicninjas.org/superfuntime/");
	}
	if (isset($_COOKIE["user_env"])) {
		$banned_addresses = file(DB_DIR.'/banneduser.dat' );
		foreach($banned_addresses as $address)
		if (trim($address) == $_COOKIE["user_env"]) header("location: http://www.whitetrash.nl/pmf");
	}
	if (isset($_COOKIE["banned"])) header("location: http://www.whitetrash.nl/pmf");
	$h_f = fopen(DB_DIR."/hits.dat", "r");
	$hits = fread($h_f, filesize(DB_DIR."/hits.dat"));
	fclose($h_f);
	settype($hits, "double");
	$hits++;
	$h_f = fopen(DB_DIR."/hits.dat", "w");
	fwrite($h_f, $hits);
	fclose($h_f);
	include $_CONFIG["skin_dir"]."/coding.php";
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	// Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	// always modified
	header ("Cache-Control: no-cache, must-revalidate");
	// HTTP/1.1
	header ("Pragma: no-cache");
	echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>".$_CONFIG["title"]."</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<link rel='stylesheet' type='text/css' href='".$_CONFIG["skin_dir"]."/css/style_simple.css' />
<script type='text/javascript' src='".$_CONFIG["skin_dir"]."/scripts/formsubmit.js'></script>
<script type='text/javascript' src='".$_CONFIG["skin_dir"]."/scripts/form_field_limiter.js'></script>
<script type='text/javascript' src='".$_CONFIG["skin_dir"]."/scripts/add_emoticon.js'></script>
<script language='JavaScript'>
function PopUp(where) {
window.open(\"where\", \"This PM has been Recieved Within the Last 5 Minutes\", \"toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,width=500,height=350\");
}
		<!--
		var counter=0;
		function check_submit() {
		counter++;
		if (counter>1) {
		alert('You cannot submit the form again! Please Wait.');
		return false;
		}
		}
		-->
</script>
</head>
<body>";
?>