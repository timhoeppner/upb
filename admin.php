<?php
// Ultimate PHP Board
// Author: MyUPB Team
// Website: http://www.myupb.com
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once("./includes/class/func.class.php");
$where = "Admin Panel";
require_once('./includes/header.php');

if(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"])) {
    if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
        
        echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
        <tr><td bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Admin Options</font></b></td></tr>
        <tr><td bgcolor='$header' valign=top>";
        
        require_once("admin_navigation.php");
        echo "</td></tr>
        </table>";
        echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
        <tr><td bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Mod Options</font></b></td></tr>
        <tr><td bgcolor='$header' valign=top>";
        
        require_once("mod_admin_navigation.php");
        echo "</td></tr>
        </table>";
        echo "$skin_tablefooter<br><br> 
        <IFRAME SRC='index.php' WIDTH=".$_CONFIG["table_width_main"]." HEIGHT='300'></IFRAME>";
    } else  echo "you are not authorized to be here.";
} else echo "you are not even logged in or not authorized to be here<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>";
require_once("./includes/footer.php");
?>
