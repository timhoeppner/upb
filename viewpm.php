<?php
// Private Messaging System
// Add on to Ultimate PHP Board V2.0
// Original PM Version (before _MANUAL_ upgrades): 2.0
// Addon Created by J. Moore aka Rebles
// Using textdb Version: 4.2.3

require_once('./includes/class/func.class.php');
require_once('./includes/inc/post.inc.php');
$where = "<a href='pmsystem.php'>Private Msging</a>";

if($_POST["action"] == "close") die('<html><body Onload="window.close()"> </body></html>');
if($tdb->is_logged_in() && isset($_GET["id"]) && is_numeric($_GET["id"]) && ($_GET["section"] == "inbox" || $_GET["section"] == "outbox")) {
    $PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
    $PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));
    $options = "";
    if($_GET["section"] == "inbox") {
        $options = "<input type='submit' name='action' value='Reply' onclick='check_submit()'> <input type='submit' name='action' value='Delete' onclick='check_submit()'>  <input type='submit' name='action' value='Block User' onclick='check_submit()'>";
        $person = "Sender";
        $users_id = "from";
        $other = "to";
    } elseif($_GET["section"] == "outbox") {
        $person = "Sent to";
        $users_id = "to";
        $other = "from";
    } else {
        exitPage("Invalid Box.", true);
    }

    if(isset($_POST["action"])) {
        if($_POST["action"] == "Reply") {
            redirect("newpm.php?ref=viewpm.php&r_id=".$_GET["id"], "0");
        } elseif($_POST["action"] == "Delete") {
            $where .= " ".$_CONFIG["where_sep"]." Delete a PM";
            require_once('./includes/header.php');
            $PrivMsg->delete("CuBox", $_GET["id"]);
            echo "Sucessfully deleted PM.";
            require_once("footer.php");
            redirect("pmsystem.php?section=".$_GET["section"], 2);
        } elseif($_POST["action"] == "<< Last Message") {
            $pmRec = $PrivMsg->getLastRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"]));
            redirect($PHP_SELF."?section=".$_GET["section"]."&id=".$pmRec[0]["id"].$extra, "0");
            $_GET["id"] = $pmRec[0]["id"];
        } elseif($_POST["action"] == "Next Message >>") {
            $pmRec = $PrivMsg->getNextRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"]));
            redirect($PHP_SELF."?section=".$_GET["section"]."&id=".$pmRec[0]["id"].$extra, "0");
            $_GET["id"] = $pmRec[0]["id"];
        } elseif($_POST["action"] == "Block User") {
            redirect("pmblocklist.php?action=add&section=".$_GET["section"]."&ref=viewpm.php&id=".$_GET["id"], "0");
        } else {
            $where = "<a href='pmsystem.php'>Private Msging</a>";
            exitPage("You should not be here (Invalid Action)", true);
        }
        exit;
    }

    $next_disabled = "";
    $back_disabled = "";
    if(FALSE === ($PrivMsg->getNextRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"])))) $next_disabled = "DISABLED";
    if(FALSE === ($PrivMsg->getLastRec("CuBox", $_GET["id"], array("box" => $_GET["section"], $other => $_COOKIE["id_env"])))) $back_disabled = "DISABLED";

    if(!isset($pmRec) || $pmRec == "" || !is_array($pmRec)) $pmRec = $PrivMsg->get("CuBox", $_GET["id"]);
    $where = "<a href='pmsystem.php'>Private Msging</a> ".$_CONFIG["where_sep"]." <a href='pmsystem.php?section=".$_GET["section"]."'>".ucfirst($_GET["section"])."</a> ".$_CONFIG["where_sep"]." ".$pmRec[0]["subject"];
    require_once('./includes/header.php');

    $pm_navegate = "<table border='0' width='".$_CONFIG["table_width_main"]."' align='center'><tr><td align='right'><p align='right'><form action='".$PHP_SELF."?section=".$_GET["section"]."&id=".$_GET["id"].$extra."' method='POST' onSubmit='submitonce(this)' enctype='multipart/form-data'><input type='submit' name='action' value='<< Last Message' onclick='check_submit()' $back_disabled> $options <input type='submit' name='action' value='Next Message >>' onclick='check_submit()' $next_disabled></form></a></p></td></tr></table><br>";
    echo $pm_navegate;

    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table WIDTH='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>
    <tr><td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>$person:</font></td>
    <td bgcolor='$header' align=left valign=center><font size='$font_m' face='$font_face' color='$font_color_header'>".$pmRec[0]["subject"]."</font></td></tr>";

    $table_color = $table1;
    $table_font = $font1;
    $user = $tdb->get("users", $pmRec[0][$users_id]);
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

    echo "<tr><td bgcolor='$table_color' rowspan='2' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'>
    <font color=$statuscolor><b>&nbsp;<a href='profile.php?action=get&id=".$user[0]["id"]."'>".$user[0]["user_name"]."</a></b></font><br>&nbsp;$status";

    if($user[0]["avatar"] != "") echo "<br> <img src=\"".$user[0]["avatar"]."\" border='0' width='".$user[0]['avatar_width']."' height='".$user[0]['avatar_height']."'><br>";

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
    <td width='45%'><font face='$font_face' color='$font_color_main' size='$font_m'><img src='icon/".$pmRec[0]["icon"]."'> <font face='$font_face' color='$font_color_main' size='$font_s'>PM Sent: ".gmdate("M d, Y g:i:s a", user_date($pmRec["date"]))."&nbsp;&nbsp;&nbsp;&nbsp; </font></font></td>
    <td width='55%' valign='middle'> <div align='right'>
     <a href='profile.php?action=get&id=".$pmRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_profile.JPG' alt='Profile' border='0'></a>
     <a href='".$user[0]["url"]."' target = '_blank'><img src='".$_CONFIG["skin_dir"]."/icons/pb_www.JPG' border='0' alt='homepage'></a>
     <a href='email.php?id=".$pmRec["user_id"]."'><img src='".$_CONFIG["skin_dir"]."/icons/pb_email.JPG' border='0' alt='email ".$pmRec["user_name"]."'></a>
    </div></td></tr>

    </tbody></tr></table>

    <table width=100% cellspacing=0 cellpadding=0><tr><td height=1 bgcolor='$divider'></td></tr></table><br><font size='$font_m' face='$font_face'>$message</font></td></tr><tr valign='bottom'>
    <td height='1%'><p> &nbsp; </p>".$user[0]["sig"]."</td>
    </tr>
    </table>
    </TD>
    </tr></table>$skin_tablefooter";

    echo $pm_navegate;
} else {
    require_once('./includes/header.php');
    if(FALSE === $tdb->is_logged_in()) echo "You are not even logged in";
    elseif($_GET["section"] != "inbox" && $_GET["section"] != "outbox") echo "Invalid Box.";
    elseif(!isset($_GET["id"]) || !is_numeric($_GET["id"])) echo "Invalid ID.";
    else echo "Unknown Error";
}
require_once('./includes/footer.php');
?>