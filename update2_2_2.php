<?php
require_once("./includes/upb.initialize.php");
$from_version = "2.2.1";
$to_version = "2.2.2";

$where = "Updating v2.2.1 to v2.2.2";
require_once("./includes/header.php");
if(UPB_VERSION == $from_version) {
	$pass = true;
	if(FALSE === ($file = file_get_contents("./config.php"))) $pass = false;
	$file = explode("\n", $file);
	if(is_array($file) foreach($file as $line) {
		if(strpos($line, "UPB_VERSION")) {
			$line = 'define("UPB_VERSION", "2.2.2");';
		}
		$file = implode("\n", $file);
			if(FALSE !== ($f = fopen("./config.php", 'w'))) {
			if(FALSE === fwrite($f, $file)) $pass = false;
			fclose($f);
		} else $pass = false;
		if($pass = true) {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum has been successfully upgraded to version 2.2.2"));
		} else {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The update script encountered an error.  Please contact myupb.com for support.", ALERT_MSG));
		}
	}
} else {
	print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum must be version 2.2.1 to update to version 2.2.2.  You are currently running ".UPB_VERSION.".", ALERT_MSG));
}
require_once("./includes/footer.php");
?>
