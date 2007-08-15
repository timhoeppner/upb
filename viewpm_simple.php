<?php
// Private Messaging System
// Add on to Ultimate PHP Board V2.0
// Original PM Version (before _MANUAL_ upgrades): 2.0
// Addon Created by J. Moore aka Rebles
// Using textdb Version: 4.2.3

require_once('./includes/class/func.class.php');
if(!$tdb->is_logged_in()) die('You are not properly logged in.');
if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) die('Invalid ID');

require_once('./includes/header_simple.php');
$where = 'PM:';
$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));


$pmRec = $PrivMsg->get("CuBox", $_GET["id"]);
echo '<center>';
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table WIDTH='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>
        <tr><td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>Sender:</font></td>
        <td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>".$pmRec[0]["subject"]."</font></td></tr>";

$table_color = $table1;
$table_font = $font1;

$user = $tdb->get("users", $pmRec[0]["from"]);
if($user[0]["sig"] != "") $user[0]["sig"] = "<table width='70%' class='signature' cellspacing='0' cellpadding='4'><tr><td>".UPBcoding(filterLanguage($user[0]["sig"], $_CONFIG["censor"]))."</td></tr></table>";

if(FALSE === mod_avatar::verify_avatar($user[0]['avatar'], $user[0]['avatar_hash'])) {
    $new_avatar = array();
    list($new_avatar['avatar_width'], $new_avatar['avatar_height']) = mod_avatar::calculate_dimensions($user[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
    $new_avatar['avatar_hash'] = mod_avatar::md5_file($user[0]['avatar']);
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

$message = format_text(filterLanguage(UPBcoding($pmRec[0]["message"]), $_CONFIG["censor"]));

echo "<tr><td bgcolor='$table_color' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'>
        <font color=$statuscolor><b>&nbsp;<a href='profile.php?action=get&id=".$pmRec[0]["from"]."'>".$user[0]["user_name"]."</a></b></font><br>&nbsp;$status";

//add avatar

if($user[0]["avatar"] != "") {
    $set_width = $avatar_width;
    $set_height = $avatar_height;
    if (@fclose(@fopen($user[0]["avatar"], "r")))  {
        list($width, $height, $type, $attr) = getimagesize($user[0]["avatar"]);
        if($width > $height) {
            $set_height = round(($avatar_width * $height) / $width);
        } elseif($width < $height) {
            $set_width = round(($avatar_height * $width) / $height);
        } elseif($width <= $avatar_width && $height <= $avatar_height) {
            $set_width = $width;
            $set_height = $height;
        }
    }
    echo "<br> <img src=\"".$user[0]["avatar"]."\" border='0' width='$set_width' height='$set_height'><br>";
}

//end avatar

echo "<br><font size='$font_s color=$font_color_main'>&nbsp;Posts: ".$user[0]["posts"]."
        <br>&nbsp;Registered: <br>&nbsp;&nbsp;&nbsp;".gmdate("Y-m-d", user_date($user[0]["date_added"]))."
        <br><br>";
if($user[0]["aim"] != "") echo "&nbsp;<a href='aim:goim?screenname=".$user[0]["aim"]."'><img src='images/aol.gif' border='0' alt='AIM: ".$user[0]["aim"]."'></a>&nbsp;&nbsp;";
if($user[0]["msn"] != "") echo "&nbsp;<a href='http://members.msn.com/".$user[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0' alt='MSN: ".$user[0]["msn"]."'>&nbsp;&nbsp;";
if($user[0]["icq"] != "") echo "&nbsp;<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user[0]["icq"]."&action=message'><img src='images/icq.gif' border='0' alt='ICQ: ".$user[0]["icq"]."'></a>&nbsp;&nbsp;";
if($user[0]["yahoo"] != "") echo "&nbsp;<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user[0]["yahoo"]."&m=g&t=0' alt='Y!: ".$user[0]["yahoo"]."'></a>";

echo "</font>
        </font></td><td bgcolor='$table_color' valign=top>

        <table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%'>
        <tr valign='top'>
        <td height='99%'><table cellspacing=0 cellpadding=0 width='100%' border=0><tbody>
        <tr>
        <td width='45%'><font face='$font_face' color='$font_color_main' size='$font_m'><img src='icon/".$pmRec[0]["icon"]."'> <font face='$font_face' color='$font_color_main' size='$font_s'>PM Sent: ".gmdate("M d, Y g:i:s a", user_date($pmRec[0]["date"]))."&nbsp;&nbsp;&nbsp;&nbsp; </font></font></td>
        <td width='55%' valign='middle'> <div align='right'>
         <a href='profile.php?action=get&id=".$user[0]["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_profile.JPG' alt='Profile' border='0'></a>
         <a href='".$user[0]["url"]."' target = '_blank'><img src='".$_CONFIG["skin_dir"]."/icons/pb_www.JPG' border='0' alt='homepage'></a>
         <a href='email.php?id=".$user[0]["id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_email.JPG' border='0' alt='email ".$user[0]["user_name"]."'></a>
        </div></td></tr>

        </tbody></tr></table>

        <table width=100% cellspacing=0 cellpadding=0><tr><td height=1 bgcolor='$divider'></td></tr></table><br><font size='$font_m' face='$font_face'>$message</font></td></tr><tr valign='bottom'>
        <td height='1%'><p> &nbsp; </p>".$user[0]["sig"]."</td>
        </tr>
        </table>
        </TD>
        </tr></table>$skin_tablefooter</center>";

require_once('./includes/footer_simple.php');
?>