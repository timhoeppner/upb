<?
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_iplog.php'>Ip Address Logs</a>";
require_once("./includes/header.php");

if(!isset($_COOKIE["user_env"]) || !isset($_COOKIE["uniquekey_env"]) || !isset($_COOKIE["power_env"]) || !isset($_COOKIE["id_env"])) exitPage("you are not logged in<meta http-equiv='refresh' content='2;URL=login.php?ref=admin_iplog.php'>");
if(!$tdb->is_logged_in() || $_COOKIE["power_env"] != 3) exitPage("you are not authorized to be here.");
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 cellpadding=3 bgcolor='$border' WIDTH='".$_CONFIG["table_width_main"]."'>

        <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
echo "<tr><td bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  include "admin_navigation.php";  echo"</center></font></b></td></tr>

        <tr><td bgcolor='$table1'>";

$f = fopen(DB_DIR."/iplog", "r");
if(filesize(DB_DIR."/iplog") > (1024 * 10)) {
    fseek($f, filesize(DB_DIR."/iplog") - (1024 *10));
}
$log = fread($f, (1024* 10));
fclose($f);
echo "$log</tr></td></table>$skin_tablefooter";


require_once("./includes/footer.php");

?>
