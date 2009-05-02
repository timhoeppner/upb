<?php
require_once("./includes/upb.initialize.php");
$from_version = "2.2.3";
$to_version = "2.2.4";

$where = "Updating to $to_version";
require_once("./includes/header.php");
echo "Updating to v$to_version";

if (file_exists('./includes/script-styles.js'))
  unlink('./includes/script-styles.js');
/*$config_tdb->delete('custom_avatars');
$config_tdb->add('avatarupload_size', '10', 'regist', 'number', 'text', '8', '2', 'Size Limits For Avatar Uploads', 'In kilobytes, type in the maximum size allowed for avatar uploads<br><i>Note: Setting to 0 will only allow linked avatars</i>');
$config_tdb->add('avatarupload_dim', '100', 'regist', 'number', 'text', '8', '3', 'Dimension Limits For Avatar Uploads', 'In pixels, type in the maximum size allowed for avatar uploads<br>e.g.100 will allow avatars up to 100x100px. If one dimension exceeds this limit the avatar will be resized<br><i>Note: Setting to 0 will only allow linked avatars</i>');
$config_tdb->add('custom_avatars', '1', 'regist', 'number', 'dropdownlist', '8', '4', 'Custom Avatars', 'Allow users to link or upload their own avatars instead of choosing them locally in images/avatars/<br>Choosing Upload allows <b>both</b> link and upload avatars', 'a:3:{i:0;s:7:"Disable";i:1;s:4:"Link";i:2;s:6:"Upload";}');*/



/*
if(UPB_VERSION == $from_version) {
$pass = true;
$lines = explode("\n", file_get_contents('config.php'));
if ($lines === false) 
$pass = false;
else
{
    for($i=0;$i<count($lines);$i++) {
    if(FALSE === strpos($lines[$i], 'UPB_VERSION')) continue;
        $lines[$i] = "define('UPB_VERSION', '2.2.4', true);";
        break;
    }
    $f = fopen('config.php', 'w');
    fwrite($f, implode("\n", $lines));
    fclose($f);
}
if($pass == true) {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum has been successfully upgraded to version 2.2.4", ALERT_MSG));
		} else {
			print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The update script encountered an error.  Please contact myupb.com for support.", ALERT_MSG));
		}
// $config_tdb->add('avataruploadsize', '10', 'regist', 'number', 'text', '8', '2', 'Size Limits For Avatar Uploads', 'In kilobytes, type in the maximum size allowed for avatar uploads<br><i>Note: Setting to 0 will only allow linked avatars</i>');

}
else {
	print str_replace("__TITLE__", ALERT_GENERIC_TITLE, str_replace("__MSG__", "The forum must be version 2.2.3 to update to version 2.2.4.  You are currently running ".UPB_VERSION.".", ALERT_MSG));
}
require_once("./includes/footer.php");*/
?>