<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

// Ultimate PHP Board Topic display
require_once('./includes/class/func.class.php');
require_once('./includes/header_simple.php');

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) exitPage("Invalid Forum ID", false, true, true);
if(!isset($_GET["t_id"]) || !is_numeric($_GET["t_id"])) exitPage("Invalid Topic ID", false, true, true);

require_once('./includes/class/posts.class.php');
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);
$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
$posts_tdb->set_topic($tRec);

$fRec = $tdb->get("forums", $_GET["id"]);

if(!($tdb->is_logged_in())) {
    $posts_tdb->set_user_info("guest", "password", "0", "0");
    $_COOKIE["power_env"] = 0;
} else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
if((int)$_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have enough Power to view this topic");

if($_GET["page"] == "") $_GET["page"] = 1;
$pRecs = $posts_tdb->getPosts("posts", (($_CONFIG["posts_per_page"]*$_GET["page"])-$_CONFIG["posts_per_page"]), $_CONFIG["posts_per_page"]);

$num_pages = ceil(($tRec[0]["replies"] + 1) / $_CONFIG["topics_per_page"]);

if($pRecs[0]["id"] == "") {
    echo "<font size='$font_m' face='$font_face' color='$font_color_main'>Topic not found.</font>";
} else {
    if($num_pages == 1){
        $p = "Pages: $num_pages";
    } else {
        $p = "Pages: ";
        for($m=1;$m<=$num_pages;$m++) {
            if($_GET["page"] == $m) $p .= "$m ";
            else $p .= "<a href='viewtopic_simple.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=$m'>$m</a> ";
        }
    }
    echo $p;
    echo "<table width='100%' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>";
    
    $x = +1;
    $y = 0;
    foreach($pRecs as $pRec) {
    // display each post in the current topic
    if($x == 0) {
        $table_color = $table1;
        $table_font = $font1;
        $x++;
    } else {
        $table_color = $table2;
        $table_font = $font2;
        $x--;
    }

    $msg = format_text(filterLanguage(UPBcoding($pRec["message"]), $_CONFIG["censor"]));
    
    echo "<tr><td bgcolor='$table_color' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'>
    <b>".$pRec["user_name"]."</b>";
    
    
    
    echo "</td><td bgcolor='$table_color' valign=top>
    <font size='$font_m' face='$font_face' color='$table_font'>
    $msg
    </font></td></tr>";
    $y++;
}

echo "</table>";



}

require_once('./includes/footer_simple.php');
?>
