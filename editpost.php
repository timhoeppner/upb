<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3

require_once("./includes/class/func.class.php");
require_once("./includes/class/posts.class.php");

$posts_tdb = new posts(DB_DIR, "posts.tdb");

$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);
//$fRec = $tdb->get("forums", $_GET["id"]);
//$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
$pRec = $posts_tdb->get("posts", $_GET["p_id"]);

$where = "Edit Post";
//$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"]." <a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Edit Post";
require_once("./includes/header.php");

if(!(isset($_GET["id"]) && isset($_GET["t_id"]) && isset($_GET["p_id"]))) exitPage("Not enough information to perform this function.");
if(!($tdb->is_logged_in())) exitPage("You are not logged in, therefore unable to perform this action.");

if($pRec[0]["user_id"] != $_COOKIE["id_env"] && $_COOKIE["power_env"] < 2) exitPage("You are not authorized to edit this post.");

if(!empty($_POST) and $_POST["submit"] == "Edit") {
    $posts_tdb->edit("posts", $_GET["p_id"], array("message" => htmlentities(stripslashes($_POST["message"])), "edited_by_id" => $_COOKIE["id_env"], "edited_by" => $_COOKIE["user_env"], "edited_date" => mkdate()));
    echo "Successfully edited post, redirecting...";
    require_once("./includes/footer.php");
    redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], 2);
    exit;
} else {
    $message = $pRec[0]["message"];
    if (!empty($_POST) and $_POST['submit'] == "Go Advanced")  
      $message = $_POST['newedit'];
    echo "   
    <form action='editpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$_GET["p_id"]."' METHOD='POST' name='newentry'>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='center'>";
    //<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Edit Post</font></b></td></tr>
    echo "<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Message:</font></b></td></tr>
<tr>";//<td bgcolor='$table1' valign='top'>
    
   // <br><br><br><br><center>";		
    //echo "<table border=1><tr><td valign=top>";
//toolMapImage();
    //echo "</tr></td></table>";
    //echo "</center>
            
    //</td>
    echo "<td bgcolor='$table1'>".bbcodebuttons()."<textarea id=\"message\" name=\"message\" cols=\"60\" rows=\"18\">".$message."</textarea>
    
    <br><br>
        <table cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='left'>
    <tr>
    <td valign='top' bgcolor='$table1' >
    <table border='0' width='100%'><tr><td><font size='$font_m' face='$font_face' color='$font_color_main'>Smilies:</font></td>";
    $bdb = new tdb(DB_DIR.'/','bbcode.tdb');
    $bdb->setFp("smilies","smilies");
    $smilies = $bdb->query("smilies","id>'0'&&type='more'");
    if ($smilies)
    echo "<td align='right'><font size='$font_m' face='$font_face' color='$font_color_main'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></font></td>";
    echo "</tr>
<tr><td colspan='2' bgcolor='$table1'>
<font size='$font_m' face='$font_face' color='$font_color_main'>".getSmilies()."</td></tr></table></td></tr></table>
            
            <tr><td bgcolor='$table1' colspan=2><input type='submit' name='submit' value='Edit'></td></tr>
            </table>$skin_tablefooter
            </form>";
        }
require_once("./includes/footer.php");
?>
