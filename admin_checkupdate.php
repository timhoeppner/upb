<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." Checking for updates";
require_once('./includes/header.php');

if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

<tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
echo "<tr><td colspan='3' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

<tr><td><div align='center'><IFRAME SRC='http://www.myupb.com/upbcheckupdate.php?ver=".UPB_VERSION."' WIDTH='".TABLE_WIDTH_MAIN."' HEIGHT='100'></IFRAME></div></td></tr></table>$skin_tablefooter";
} else {
    echo "you are not authorized to be here.";
}

require_once("./includes/footer.php");
?>
