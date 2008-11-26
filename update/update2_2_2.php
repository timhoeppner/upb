<?php
require_once("./includes/upb.initialize.php");
$to_version = "2.2.3";

$where = "Updating to $to_version";
require_once("./includes/header.php");
echo "Updating to $to_version";

if(UPB_VERSION == $from_version) {
$pass = true;
$lines = explode("\n", file_get_contents('config.php'));
if ($lines === false) 
$pass = false;
else
{
    for($i=0;$i<count($lines);$i++) {
    if(FALSE === strpos($lines[$i], 'UPB_VERSION')) continue;
        $lines[$i] = "define('UPB_VERSION', '2.2.3', true);";
        break;
    }
    $f = fopen('config.php', 'w');
    fwrite($f, implode("\n", $lines));
    fclose($f);
}
if($pass == true) {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum has been successfully upgraded to version 2.2.2", ALERT_MSG));
		} else {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The update script encountered an error.  Please contact myupb.com for support.", ALERT_MSG));
		}
 
}
else {
	print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum must be version 2.2.1 to update to version 2.2.2.  You are currently running ".UPB_VERSION.".", ALERT_MSG));
}
require_once("./includes/footer.php");
?>