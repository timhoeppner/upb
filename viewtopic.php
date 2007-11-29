<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

// Ultimate PHP Board Topic display
require_once('./includes/class/func.class.php');
require_once('./includes/inc/post.inc.php');
require_once("./includes/class/upload.class.php");
require_once("./includes/class/posts.class.php");
$fRec = $tdb->get("forums", $_GET["id"]);
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);
$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
$posts_tdb->edit("topics", $_GET["t_id"], array("views" => ((int)$tRec[0]["views"] + 1)));

$posts_tdb->set_topic($tRec);
$posts_tdb->set_forum($fRec);
if(!($tdb->is_logged_in())) {
    $posts_tdb->set_user_info("guest", "password", "0", "0");
    $_COOKIE["power_env"] = 0;
} else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);

$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"]." ".$tRec[0]["subject"];
require_once('./includes/header.php');

if((int)$_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have enough Power to view this topic");

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) exitPage("Invalid Forum ID");
if(!isset($_GET["t_id"]) || !is_numeric($_GET["t_id"])) exitPage("Invalid Topic ID");

/* upb_session_start(); //unfinished session new topic
if($_SESSION['newTopics']['f'.$_GET['id']]['t'.$_GET['t_id']] == 1) unset($_SESSION['newTopics']['f'.$_GET['id']]['t'.$_GET['t_id']]);
if($_SESSION['newTopics']['f'.$_GET['id']]['t'.$_GET['t_id']] == 0 && $_SESSION['newTopics']['lastVisitForums'][$_GET['id']] > $tRec[0]['last_post']) unset($_SESSION['newTopics']['f'.$_GET['id']]['t'.$_GET['t_id']]);
if($tRec[0]['last_post'] > $_SESSION['newTopics']['lastVisitForums'][$_GET['id']]) $_SESSION['newTopics']['f'.$_GET['id']]['t'.$_GET['t_id']] = 0;
//if($tRec[0]['last_post'] > $_SESSION['newTopics']['lastVisitForums'][$_GET['id']]) echo 'true'; else echo  'false';
*/

if($_GET["page"] == "") $_GET["page"] = 1;

$pRecs = $posts_tdb->getPosts("posts", (($_CONFIG["posts_per_page"]*$_GET["page"])-$_CONFIG["posts_per_page"]), $_CONFIG["posts_per_page"]);
if (!count($pRecs) > 0)
  redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".($_GET["page"]-1), "0");

if(empty($pRecs)) exitPage("Posts not found");

$num_pages = ceil(($tRec[0]["replies"] + 1) / $_CONFIG["posts_per_page"]);
$p = createPageNumbers($_GET["page"], $num_pages, $_SERVER['QUERY_STRING']);
if($_GET['page'] == 1) $first_post = $pRecs[0]['id'];
else $first_post = 0;
$x = +1;
echo "<div id='pagelink1' name='pagelink1'>";
$posts_tdb->d_posting($p);
echo "</div>";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<div id='posts' name='posts'>";
echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>";

//show header of topic
echo "<tr><td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>Author:</font></td>
<td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>Topic: ".$tRec[0]["subject"]."</font></td></tr>";


$final = "";
$counter = count($pRecs) - 1;
foreach($pRecs as $key => $pRec) {
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
    
    unset($user, $status, $statuscolor);
    $sig = '';
    $status = '';
    $statuscolor = '';
    $pm = "";
    if($pRec["user_id"] != "0") {
        $user = $tdb->get("users", $pRec["user_id"]);
        if($user[0]["sig"] != "") {
            $sig = format_text(filterLanguage(UPBcoding($user[0]["sig"]), $_CONFIG["censor"]));
            $sig = "<table width='70%' class='signature' cellspacing='0' cellpadding='4'><tr><td>$sig</td></tr></table>";
        }
        if(FALSE === mod_avatar::verify_avatar($user[0]['avatar'], $user[0]['avatar_hash'])) {
            $new_avatar = mod_avatar::new_parameters($user[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
            $tdb->edit('users', $user[0]['id'], $new_avatar);
            $user[0] = array_merge($user[0], $new_avatar);
            unset($new_avatar);
        }

        if($user[0]["level"] == "1") {
            $statuscolor = $_STATUS["userColor"];
            if($user[0]["posts"] >= $_STATUS["member_post1"]) $status = $_STATUS["member_status1"];
            if($user[0]["posts"] >= $_STATUS["member_post2"]) $status = $_STATUS["member_status2"];
            if($user[0]["posts"] >= $_STATUS["member_post3"]) $status = $_STATUS["member_status3"];
            if($user[0]["posts"] >= $_STATUS["member_post4"]) $status = $_STATUS["member_status4"];
            if($user[0]["posts"] >= $_STATUS["member_post5"]) $status = $_STATUS["member_status5"];
        } elseif($user[0]["level"] == "2") {
            $statuscolor = $_STATUS["modColor"];
            if($user[0]["posts"] >= $_STATUS["mod_post1"]) $status = $_STATUS["mod_status1"];
            if($user[0]["posts"] >= $_STATUS["mod_post2"]) $status = $_STATUS["mod_status2"];
            if($user[0]["posts"] >= $_STATUS["mod_post3"]) $status = $_STATUS["mod_status3"];
            if($user[0]["posts"] >= $_STATUS["mod_post4"]) $status = $_STATUS["mod_status4"];
            if($user[0]["posts"] >= $_STATUS["mod_post5"]) $status = $_STATUS["mod_status5"];
        } elseif($user[0]["level"] == "3") {
            $statuscolor = $_STATUS["adminColor"];
            if($user[0]["posts"] >= $_STATUS["admin_post1"]) $status = $_STATUS["admin_status1"];
            if($user[0]["posts"] >= $_STATUS["admin_post2"]) $status = $_STATUS["admin_status2"];
            if($user[0]["posts"] >= $_STATUS["admin_post3"]) $status = $_STATUS["admin_status3"];
            if($user[0]["posts"] >= $_STATUS["admin_post4"]) $status = $_STATUS["admin_status4"];
            if($user[0]["posts"] >= $_STATUS["admin_post5"]) $status = $_STATUS["admin_status5"];
        } else {
            $status = "Member";
            $statuscolor = $_STATUS["membercolor"];
        }
        if($user[0]["status"] != "") $status = $user[0]["status"];

        if(isset($_COOKIE["id_env"]) && $pRec["user_id"] != $_COOKIE["id_env"]) {
            $user_blList = getUsersPMBlockedList($pRec["user_id"]);
            if(TRUE !== (in_array($_COOKIE["id_env"], $user_blList))) $pm = "<a href='newpm.php?to=".$pRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/sendpm.jpg' border='0' alt='Send User a PM'></a>";
        }
    }
    if(($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) 
    {
     
      $edit = "<a href=\"javascript:getPost('{$pRec["id"]}','{$_GET["t_id"]}','{$pRec["user_id"]}','{$_GET["t_id"]}-{$pRec["id"]}');\"><img src='".$_CONFIG["skin_dir"]."/icons/pb_edit.JPG' alt='Edit Post' border='0'></a>";
    }
    else $edit = "";

    if((($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) && $pRec['id'] != $first_post) $delete = "<a href='delete.php?action=delete&t=0&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$pRec["id"]."&page=".$_GET["page"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_delete.JPG' border='0'></a>";
    else $delete = "";

    if((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $quote = "<a href='newpost.php?id=".$_GET["id"]."&t=0&quote=1&t_id=".$_GET["t_id"]."&p_id=".$pRec["id"]."&page=".$_GET["page"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_quote.JPG' border='0' alt='Quote'></a>";
    else $quote = "";
    
    // Find out if there is an attachment for this post
    $uploadId = (int) $pRec["upload_id"];
    
    if($uploadId > 0) {
        // We have an attachment, query the database for the info
        $tdb->setFp("uploads", "uploads");
        
        $q = $tdb->get("uploads", $uploadId, array("name", "downloads"));
        
        // Make sure the attachment exists
        if($q !== false) {
            $attachName = $q[0]["name"];
            $attachDownloads = $q[0]["downloads"];
            
            $pRec["message"] = "[img]images/attachment.gif[/img] Attachment: [url=downloadattachment.php?id={$uploadId}]{$attachName}[/url] (Downloaded [b]{$attachDownloads}[/b] times)\n\n" . $pRec["message"];
        }
    }

    $msg = format_text(filterLanguage(UPBcoding($pRec["message"]), $_CONFIG["censor"]));
    $originalmsg = $pRec["message"];
    //echo $_SERVER['HTTP_USER_AGENT'];
    if (substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE") > 0)
    {
      //echo "COUNT = ".substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE");
      if (substr_count($_SERVER['HTTP_USER_AGENT'],"Opera") == 0)
        $originalmsg = nl2br($originalmsg);
    }
    
    echo "<tr><td bgcolor='$table_color' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'>&nbsp;";
    if($pRec["user_id"] != "0") echo "<a name='".$pRec['id']."'><b><a href='profile.php?action=get&id=".$pRec["user_id"]."'>".$pRec["user_name"]."</a></b>";
    else echo $pRec["user_name"];
    echo "</font><br>&nbsp;<font color=".$statuscolor.">".$status."</font>";

    if(@$user[0]["avatar"] != "") echo "<br> <img src=\"".$user[0]["avatar"]."\" border='0' width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."'><br>";

    if($pRec["user_id"] != "0") echo "<p><font size='$font_s'>&nbsp;Posts: ".$user[0]["posts"]."
    <br>&nbsp;Registered: <br>&nbsp;&nbsp;&nbsp;".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
    <br><br>";
    if($pm != "") echo $pm."<br>";
    if($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
    if($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'>&nbsp;&nbsp;";
    if($user[0]["icq"] != "") echo "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
    if($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
    echo "</p></font></font>";
    echo "<div name='edit{$_GET['t_id']}-{$pRec['id']}' id='edit{$_GET['t_id']}-{$pRec['id']}'>";
    if(!empty($pRec['edited_by']) && !empty($pRec['edited_by_id']) && !empty($pRec['edited_date'])) 
      echo '<table width="95%" border="1" cellspacing="0" cellpadding="3"><tr><td>Last edited by:<br> <a href="profile.php?action=get&id='.$pRec['edited_by_id'].'" target="_new">'.$pRec['edited_by'].'</a> on '.gmdate("M d, Y g:i:s a", user_date($pRec['edited_date'])).'</td></tr></table>';
    echo "</div>";
    
    echo "</a></td><td bgcolor='$table_color' valign=top>
    <font size='$font_m' face='$font_face' color='$table_font'>

    <table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%'>
    <tr valign='top'>
    <td height='99%'><table cellspacing=0 cellpadding=0 width='100%' border=0><tbody>
    <tr>
    <td width='45%'><font face=verdana color=#ffffff size=2><img src='icon/".$pRec["icon"]."'> <font color='black' size='$font_s'>Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec["date"]))."&nbsp;&nbsp;&nbsp;&nbsp; </font></font></td>
    <td width='55%' valign='middle'> <div align='right'><font face=verdana color=#ffffff size=2><font color=yellow size=1>$edit $delete $quote ";
    if($pRec["user_id"] != "0") echo "<a href='profile.php?action=get&id=".$pRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_profile.JPG' alt='Profile' border='0'></a></font> <a href='".$user[0]["url"]."' target = '_blank'><img src='".$_CONFIG["skin_dir"]."/icons/pb_www.JPG' border='0' alt='homepage'></a> <a href='email.php?id=".$pRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_email.JPG' border='0' alt='email ".$pRec["user_name"]."'></a>";

    
    echo "</font></div></td>
    </tr></table>

    <table width=100% cellspacing=0 cellpadding=0><tr><td height=1 bgcolor='$divider'></td></tr></table><br>";    
    echo "<div id='{$_GET['t_id']}-{$pRec['id']}' name='{$_GET['t_id']}-{$pRec['id']}'>$msg</div>
    </td></tr><tr valign='bottom'>
    <td height='1%'><p> &nbsp; </p>".$sig."</td>
    </tr>
    </table>
    </TD>
    </tr>
    ";
    
}
echo "</table>$skin_tablefooter";
echo "</div>";//START QUICK REPLY SEGMENT

if (!($_COOKIE["power_env"] < $fRec[0]["post"] && $_GET["t"] == 1 || $_COOKIE["power_env"] < $fRec[0]["reply"] && $_GET["t"] == 0))
{
  echo "<div id='quickreplyform' name='quickreplyform'>";
  echo "<form name='quickreply' action='newpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."' method='POST' name='quickreply'>\n";
  echoTableHeading("Quick Reply", $_CONFIG);
  echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='center'>";
  foreach ($_GET as $key => $value)
    echo "<input type='hidden' id='$key' name='$key' value='$value'>\n"; 
  echo "<input type='hidden' id='user_id' name='user_id' value='{$_COOKIE['id_env']}'>\n";
  echo "<input type='hidden' id='icon' name='icon' value='icon1.gif'>\n";
  echo "<input type='hidden' id='username' name='username' value='{$_COOKIE["user_env"]}'>\n";
	echo "<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>$hed</font></b></td></tr>\n
		<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>User Name:</font></td><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$_COOKIE["user_env"]."</td></tr>\n
		<tr><td bgcolor='$table1' valign='top'><font size='$font_m' face='$font_face' color='$font_color_main'>Message:</font>\n
		</td><td bgcolor='$table1'>\n
    <textarea id=\"newentry\" name=\"newentry\" cols=\"60\" rows=\"18\"></textarea>\n
    </td></tr>\n";
  echo "<tr><td bgcolor='$table1' colspan=2 align='center'>\n
    <input type='button' name='quickreply' value='Quick Reply' onclick=\"javascript:getReply(document.getElementById('quickreply'))\">\n
    <input type='submit' name='submit' value='Go Advanced'>\n</td></tr></form></font>".$skin_tablefooter;
  echo "</div>";
}
//END QUICK REPLY SEGMENT

echo "<div id='pagelink2' name='pagelink2'>";
$posts_tdb->d_posting($p);
echo "</div>";

if((int)$_COOKIE["power_env"] >= 2) {
    echo "<p align=center><font size='$font_m' face='$font_face' color='$font_color_main'>
    <a href='delete.php?action=delete&t=1&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/deletetopic.gif' border='0'></a>";
    if($tRec[0]["locked"] == "0") echo "<a href='managetopic.php?action=CloseTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/closetopic.gif' border='0'></a>";
    else echo "<a href='managetopic.php?action=OpenTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/opentopic.gif' border='0'></a>";
    echo "<a href='managetopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/manage.gif' border='0'></a></p>";
}
$tdb->cleanup();
unset($tdb);
require('./includes/footer.php');
?>
