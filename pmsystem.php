<?php
// Private Messaging System
// Add on to Ultimate PHP Board V2.0
// Original PM Version (before _MANUAL_ upgrades): 2.0
// Addon Created by J. Moore aka Rebles
// Using textdb Version: 4.2.3

require_once('./includes/class/func.class.php');

$where = "<a href='pmsystem.php'>Private Msg</a>";
if(isset($_GET["section"]) && $_GET["section"] != "") $where .= " ".$_CONFIG["where_sep"]." ".ucfirst($_GET["section"]);
require_once('./includes/header.php');
if(!isset($_COOKIE["user_env"]) || !isset($_COOKIE["uniquekey_env"]) || !isset($_COOKIE["power_env"]) || !isset($_COOKIE["id_env"])) exitPage('You are not logged in.');
if(!$tdb->is_logged_in()) exitPage('Invalid Login!');
require_once('./includes/inc/privmsg.inc.php');

$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));

if($_GET["section"] != "outbox") $pmRecs = $PrivMsg->query("CuBox", "box='inbox'&&to='".$_COOKIE["id_env"]."'");
else $pmRecs = $PrivMsg->query("CuBox", "box='outbox'&&from='".$_COOKIE["id_env"]."'");
if(!empty($pmRecs) && $pmRecs[0] != '') $pmRecs = array_reverse($pmRecs);
elseif($_GET['section'] != '') {
    echo '<font face="$font_face" size="$font_m" color="$font_color_main"><center>No Messages in your '.$_GET["section"].'</center></font>';
    require_once('./includes/footer.php');
    exit;
}
$count = count($pmRecs);
if($_GET["section"] == "inbox") {
    if($new_pm != 0) {
        $f = fopen(DB_DIR."/new_pm.dat", 'r+');
        fseek($f, (((int)$_COOKIE["id_env"] * 2) - 2));
        fwrite($f, " 0");
        fclose($f);
    }

    if(isset($_POST['action']) && $_POST['action'] == "Delete PMs") {
        $num = 0;
        $delete = array();
        for($i=0;$i<$count;$i++) {
            if(isset($_POST[$pmRecs[$i]["id"]."_del"])) {
                $PrivMsg->delete("CuBox", $pmRecs[$i]["id"], false);
                $num++;
                $delete[] = $i;
            }
        }
        $PrivMsg->reBuild("CuBox");
        if($num > 0) {
            echo "<p align='center'>Successfully Deleted $num Private Msg(s)</p>";
            $count -= $num;
            for($i=0;$i<count($delete);$i++) {
                unset($pmRecs[$delete[$i]]);
            }
        } else {
            echo "<p align='center'>No Private Msg(s) Successfully Deleted...</p>";
        }
        unset($num);
    }

    $none = TRUE;
    $echo = "";
    $blockedids = getUsersPMBlockedList($_COOKIE["id_env"]);
    foreach($pmRecs as $pmRec) {
        if($pmRec["id"] != "") {
            if($none) $none = FALSE;
            if($pmRec["date"] > $_COOKIE["lastvisit"]) $new = "new";
            else $new = " ";
            $user = $tdb->get("users", $pmRec["from"]);
            if($user[0]["level"] == "1") {
                if(TRUE !== (in_array($pmRec["from"], $blockedids))) $ban_text = "<a href='pmblocklist.php?action=add&ref=pmsystem.php&section=".$_GET["section"]."&user_id=".$pmRec["from"]."'>Block</a>";
                else $ban_text = "<font color='red'><b>BLOCKED!</b></font>";
            } else {
                $ban_text = "<font color='red'><b>Admin/Mod</b></font>";
            }

            $echo .= "<tr>
                <td bgcolor='$table1' width=4%><font face='$font_face' size='$font_s' color='red'>$new</font></td>
                <td bgcolor='$table1' width=12%><center><input type='checkbox' name='".$pmRec["id"]."_del' value='CHECKED'></center></td>
                <td bgcolor='$table1' width=12% align='center'><p align='center'><font size='$font_m' color='$font_color_main'>$ban_text</font></p></td>
                <td bgcolor='$table1' width=34%><font face='$font_face' size='$font_m' color='$font_color_main'><img src='./icon/".$pmRec["icon"]."'> <a href='viewpm.php?section=".$_GET["section"]."&id=".$pmRec["id"]."'>".$pmRec["subject"]."</a></font></td>
                <td bgcolor='$table1' width=40%><font face='$font_face' size='$font_m' color='$font_color_main'>Sent by <a href='profile.php?action=get&id=".$pmRec["from"]."'>".$user[0]["user_name"]."</a> on ".gmdate("M d, Y g:i:s a", user_date($pmRec["date"]))."</font></td>
                </tr>";
            unset($new, $ban_text);
        } else {
            $none++;
        }
    }

    if($none) {
        $echo = "<tr><td bgcolor='$table1' width=100% colspan='5'><font face='$font_face' size='$font_m' color='$font_color_main'><center>No Messages in your ".$_GET["section"]."</center></font></td></tr>";
        $disable = "DISABLED";
    } else $disable = "";

    echo "<form name='main' action='".$PHP_SELF."?section=".$_GET["section"]."' method='POST' onSubmit='submitonce(this)' enctype='multipart/form-data'><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table cellspacing='1' cellpadding='3'  width='".$_CONFIG["table_width_main"]."' bgcolor='$border' align='center'>
        <tr>
        <td bgcolor='$header' width=4%><font face='$font_face' size='$font_m' color='$font_color_header'> </font></td>
        <td bgcolor='$header' width=12%><font face='$font_face' size='$font_m' color='$font_color_header'><input type='submit' name='action' value='Delete PMs' $disable></font></td>
        <td bgcolor='$header' width=12%><font face='$font_face' size='$font_m' color='$font_color_header'>Block User</font></td>
        <td bgcolor='$header' width=34%><font face='$font_face' size='$font_m' color='$font_color_header'>Title:</font></td>
        <td bgcolor='$header' width=40%><font face='$font_face' size='$font_m' color='$font_color_header'>By:</font></td>
        </tr>";

    echo $echo;

    echo "</table>$skin_tablefooter</center><br><br><font face='$font_face' size='$font_s' color='$font_main_color'><i>You are not allowed to block Administrators/Moderators</i></font></form><center>";
} elseif($_GET["section"] == "outbox") {
    $none = 0;

    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>
        <tr>
        <td bgcolor='$header' width=40%><font face='$font_face' size='$font_m' color='$font_color_header'>Title:</font></td>
        <td bgcolor='$header' width=60%><font face='$font_face' size='$font_m' color='$font_color_header'>By:</font></td>
        </tr>";

    foreach($pmRecs as $pmRec) {
        if($pmRec["id"] != "") {
            $user = $tdb->get("users", $pmRec["to"]);
            echo "<tr>
                <td bgcolor='$table1' width=40%><font face='$font_face' size='$font_m' color='$font_color_main'><img src='./icon/".$pmRec["icon"]."'> <a href='viewpm.php?section=".$_GET["section"]."&id=".$pmRec["id"]."'>".$pmRec["subject"]."</a></font></td>
                <td bgcolor='$table1' width=60%><font face='$font_face' size='$font_m' color='$font_color_main'>Sent to <a href='profile.php?action=get&id=".$pmRec["to_id"]."'>".$user[0]["user_name"]."</a> on ".gmdate("M d, Y g:i:s a", user_date($pmRec["date"]))."</font></td>
                </tr>";
            unset($pmRec);
        } else {
            $none++;
        }
        unset($pmRec);
    }

    if($none == $count) {
        echo "<tr><td bgcolor='$table1' width=100% colspan='2'><font face='$font_face' size='$font_m' color='$font_color_main'><center>No Messages in your ".$_GET["section"]."</center></font></td></tr>";
        $disable = "DISABLED";
    }
    echo "</table>$skin_tablefooter";
} else {
    $old_pm = ($count - $new_pm);
    echo "<center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
        <tr><td  bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Private Msg Menu</font></b></td></tr>
        <tr><td bgcolor='$table1' width=100% valign=top><font size='$font_m' face='$font_face' color='$font_color_main'><a href='pmsystem.php?section=inbox'>View Inbox</a> <b>$new_pm</b> New Private Msg(s) and <b>$old_pm</b> Old Private Msg(s)
        <br><a href='pmsystem.php?section=outbox'>View Outbox</a>
        <br><a href='pmblocklist.php'>Manage Blocked Users</a>
        <br><a href='pmblocklist.php?action=adduser'>Block a User</a>
        </tr></td></table>$skin_tablefooter";
}
require_once("./includes/footer.php");
?>