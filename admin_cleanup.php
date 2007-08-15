<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_cleanup.php'>Clean up (old search files)</a>";
require_once('./includes/header.php');

if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
    if($_GET["action"] == "cleanup") {
        echo "Cleaning up...<br>";
        $dbdir = opendir(DB_DIR);
        while($p = readdir($dbdir)) {
            if(substr($p, 0, 7) == "search_") {
                unlink(DB_DIR.$p);
                echo "$p<br>";
            }
        }
        echo "Done!";
        redirect($PHP_SELF, 2);
    } else {
    
        $files = 0;
        $size = 0;
        $dbdir = opendir(DB_DIR);
        while($p = readdir($dbdir)) {
            if(substr($p, 0, 7) == "search_") {
                $files++;
                $size += filesize(DB_DIR.$p);
            }
        }
        
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
        echo "<table cellspacing=1 bgcolor='$border' WIDTH='".$_CONFIG["table_width_main"]."'>

        <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
        echo "<tr><td bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

        <tr><td bgcolor='$table1'>";
        if($files > 0) echo "There is $files old search files. The files are taking up ".round($size / 1024, 2)." KB on the server.<br><a href='admin_cleanup.php?action=cleanup'>Remove the old search files</a>";
        else echo "There are no old search files.";
        echo "</tr></td></table>$skin_tablefooter";
    }
} else {
    echo "you are not authorized to be here.";
}

require_once("./includes/footer.php");
?>
