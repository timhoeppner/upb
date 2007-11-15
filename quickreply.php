<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2
require_once('./includes/class/func.class.php');
require_once('./includes/inc/post.inc.php');
require_once("./includes/class/upload.class.php");
require_once("./includes/class/posts.class.php");
//include $_CONFIG["skin_dir"]."/css/skin.css";
include $_CONFIG["skin_dir"]."/coding.php";

$output = "<link rel=\"stylesheet\" href=\"".$_CONFIG["skin_dir"]."/css/skin.css\" type=\"text/css\">";
$fRec = $tdb->get("forums", $_POST["id"]);
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$posts_tdb->setFp("topics", $_POST["id"]."_topics");
$posts_tdb->setFp("posts", $_POST["id"]);
$tRec = $posts_tdb->get("topics", $_POST["t_id"]);
$posts_tdb->set_topic($tRec);
$posts_tdb->set_forum($fRec);
$tdb->setFp('users', 'members');
if (!isset($a)) $a = 0;

if(!($tdb->is_logged_in())) {
    $_COOKIE["user_env"] = "guest";
    $_COOKIE["power_env"] = 0;
    $_COOKIE["id_env"] = 0;
}

    $msg = htmlentities(stripslashes($_POST["newentry"]));        
      $tdb->edit("forums", $_POST["id"], array("posts" => ((int)$fRec[0]["posts"] + 1)));
      $rec = $posts_tdb->get("topics", $_POST["t_id"]);
      
      $posts_tdb->edit("topics", $_POST["t_id"], array("replies" => ((int)$rec[0]["replies"] + 1), "last_post" => mkdate(), "user_name" => $_COOKIE["user_env"], "user_id" => $_COOKIE["id_env"], "monitor" => ""));
        
    $pre = $rec[0]["p_ids"].",";
    
    clearstatcache();
    $posts_tdb->sort("topics", "last_post", "DESC");
    clearstatcache();
    
    $p_id = $posts_tdb->add("posts", array(
        "icon" => $_POST["icon"], 
        "user_name" => $_COOKIE["user_env"], 
        "date" => mkdate(), 
        "message" => $msg, 
        "user_id" => $_COOKIE["id_env"], 
        "t_id" => $_POST["t_id"], 
        "upload_id" => $uploadId
    ));
    
    $posts_tdb->edit("topics", $_POST["t_id"], array("p_ids" => $pre.$p_id));
    
    if($_COOKIE["power_env"] != "0")
    {
      $user = $tdb->get("users",$_COOKIE["id_env"]);
      $tdb->edit("users", $_COOKIE["id_env"], array("posts" => ((int)$user[0]["posts"] + 1)));
    }
    $posts_tdb->cleanUp();
$fRec = $tdb->get("forums", $_POST["id"]);
$posts_tdb->setFp("topics", $_POST["id"]."_topics");
$posts_tdb->setFp("posts", $_POST["id"]);
$tRec = $posts_tdb->get("topics", $_POST["t_id"]);
$posts_tdb->set_topic($tRec);
$posts_tdb->set_forum($fRec);
$tdb->setFp('users', 'members');
if(!($tdb->is_logged_in())) {
    $posts_tdb->set_user_info("guest", "password", "0", "0");
    $_COOKIE["power_env"] = 0;
} else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
    $page=1;
    //$page1 = range(1,20);
    //$page2 = range(21,40);
    //$page3 = range(31,40);
    $postids = $tRec[0]['p_ids'];
    $postnums = explode(",",$postids);
    $count = count($postnums);
    $num_pages = ceil($count/$_CONFIG["posts_per_page"]);
    //$math = "COUNT: $count PAGES = $divided";
    $page = $num_pages;
    $pRecs = $posts_tdb->getPosts("posts", (($_CONFIG["posts_per_page"]*$page)-$_CONFIG["posts_per_page"]), $_CONFIG["posts_per_page"]);
      
      //var_dump($pRecs);
$query = "id={$_POST['id']}&t_id={$_POST['t_id']}";
$p = createPageNumbers($page, $num_pages, $query,true);
$pagelinks = $posts_tdb->d_posting_qr($p,$page);
$output .= "<table width='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>";

//show header of topic
$output .= "<tr><td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>Author:</font></td>
<td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>Topic: ".$tRec[0]["subject"]."</font></td></tr>";
$x = +1;
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
     
      $edit = "<a href=\"javascript:changediv('{$_POST["id"]}','{$_POST["t_id"]}','{$pRec["id"]}','{$_POST["t_id"]}-{$pRec["id"]}');\"><img src='".$_CONFIG["skin_dir"]."/icons/pb_edit.JPG' alt='Edit Post' border='0'></a>";
    }
    else $edit = "";

    if((($_COOKIE["id_env"] == $pRec["user_id"] && $tdb->is_logged_in()) || (int)$_COOKIE["power_env"] >= 2) && $pRec['id'] != $first_post) $delete = "<a href='delete.php?action=delete&t=0&id=".$_POST["id"]."&t_id=".$_POST["t_id"]."&p_id=".$pRec["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_delete.JPG' border='0'></a>";
    else $delete = "";

    if((int)$_COOKIE["power_env"] >= (int)$fRec[0]["reply"]) $quote = "<a href='newpost.php?id=".$_POST["id"]."&t=0&quote=1&t_id=".$_POST["t_id"]."&p_id=".$pRec["id"]."&page=".$page."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_quote.JPG' border='0' alt='Quote'></a>";
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

    $msg = format_text(filterLanguage(UPBcoding(stripslashes($pRec["message"])), $_CONFIG["censor"]));
    $originalmsg = $pRec["message"];
    //echo $_SERVER['HTTP_USER_AGENT'];
    if (substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE") > 0)
    {
      //echo "COUNT = ".substr_count($_SERVER['HTTP_USER_AGENT'],"MSIE");
      if (substr_count($_SERVER['HTTP_USER_AGENT'],"Opera") == 0)
        $originalmsg = nl2br($originalmsg);
    }
    
    $output .= "<tr><td bgcolor='$table_color' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'>&nbsp;";
    if($pRec["user_id"] != "0") $output .= "<b><a href='profile.php?action=get&id=".$pRec["user_id"]."'>".$pRec["user_name"]."</a></b>";
    else $output .= $pRec["user_name"];
    $output .= "</font><br>&nbsp;<font color=".$statuscolor.">".$status."</font>";

    if(@$user[0]["avatar"] != "") $output .= "<br> <img src=\"".$user[0]["avatar"]."\" border='0' width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."'><br>";

    if($pRec["user_id"] != "0") $output .= "<p><font size='$font_s'>&nbsp;Posts: ".$user[0]["posts"]."
    <br>&nbsp;Registered: <br>&nbsp;&nbsp;&nbsp;".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
    <br><br>";
    if($pm != "") $output .= $pm."<br>";
    if($user[0]["aim"] != "") $output .= "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
    if($user[0]["msn"] != "") $output .= "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'>&nbsp;&nbsp;";
    if($user[0]["icq"] != "") $output .= "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
    if($user[0]["yahoo"] != "") $output .= "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";
    $output .= "</p></font></font>";
    $output .= "<div name='edit{$_GET['t_id']}-{$pRec['id']}' id='edit{$_GET['t_id']}-{$pRec['id']}'>";
    if(!empty($pRec['edited_by']) && !empty($pRec['edited_by_id']) && !empty($pRec['edited_date'])) 
      $output .= '<table width="95%" border="1" cellspacing="0" cellpadding="3"><tr><td>Last edited by: <a href="profile.php?action=get&id='.$pRec['edited_by_id'].'" target="_new">'.$pRec['edited_by'].'</a> on '.gmdate("M d, Y g:i:s a", user_date($pRec['edited_date'])).'</td></tr></table>';
    $output .= "</div>";
    
    $output .= "</td><td bgcolor='$table_color' valign=top>
    <font size='$font_m' face='$font_face' color='$table_font'>

    <table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%'>
    <tr valign='top'>
    <td height='99%'><table cellspacing=0 cellpadding=0 width='100%' border=0><tbody>
    <tr>
    <td width='45%'><font face=verdana color=#ffffff size=2><img src='icon/".$pRec["icon"]."'> <font color='black' size='$font_s'>Posted: ".gmdate("M d, Y g:i:s a", user_date($pRec["date"]))."&nbsp;&nbsp;&nbsp;&nbsp; </font></font></td>
    <td width='55%' valign='middle'> <div align='right'><font face=verdana color=#ffffff size=2><font color=yellow size=1>$edit $delete $quote ";
    if($pRec["user_id"] != "0") $output .= "<a href='profile.php?action=get&id=".$pRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_profile.JPG' alt='Profile' border='0'></a></font> <a href='".$user[0]["url"]."' target = '_blank'><img src='".$_CONFIG["skin_dir"]."/icons/pb_www.JPG' border='0' alt='homepage'></a> <a href='email.php?id=".$pRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_email.JPG' border='0' alt='email ".$pRec["user_name"]."'></a>";

    
    $output .= "</font></div></td>
    </tr></table>

    <table width=100% cellspacing=0 cellpadding=0><tr><td height=1 bgcolor='$divider'></td></tr></table><br>";
    //The first div contains the filtered and bbcode formatted post as it would appear on the page.
    //The second div (which is hidden) contains the post as stored in the database with the BBtags.
    //This allows the textarea to be populated with the original text as it is stored in the database and also allows changes to be made to the edited post immediately without reload if a subsequent quick edit is needed
    
    $output .= "<div id='{$_POST['t_id']}-{$pRec['id']}' name='{$_POST['t_id']}-{$pRec['id']}'>$msg</div>
    <div style='display:none;' id='{$_POST['t_id']}-{$pRec['id']}h' name='{$_POST['t_id']}-{$pRec['id']}h'>$originalmsg</div>
    </td></tr><tr valign='bottom'>
    <td height='1%'><p> &nbsp; </p>$sig</td>
    </tr>
    </table>
    </TD>
    </tr>
    ";
    
}
$output .= "</table>$skin_tablefooter</div><!--divider-->$pagelinks";
    
    echo $output;
    
?>
