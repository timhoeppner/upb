<?php
// Private Messaging System
// Add on to Ultimate PHP Board V2.0
// Original PM Version (before _MANUAL_ upgrades): 2.0
// Addon Created by J. Moore aka Rebles
// Using textdb Version: 4.2.3

require_once('./includes/class/func.class.php');
if(!isset($_COOKIE["user_env"]) || !isset($_COOKIE["uniquekey_env"]) || !isset($_COOKIE["power_env"]) || !isset($_COOKIE["id_env"])) exitPage('You are not logged in.', true);
if(!$tdb->is_logged_in()) exitPage('Invalid Login!', true);
require_once('./includes/inc/privmsg.inc.php');

$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));

if($action == "add") {
    $where = "<a href='pmsystem.php'>Private Msg</a> ".$_CONFIG["where_sep"]." Manage Blocked Users";
    $echo = "<br>";
    $void = "";

    if(!isset($_GET["user_id"]) && isset($_GET["id"])) {
        $rec = $PrivMsg->get("CuBox", $_GET["id"]);
        $_GET["user_id"] = $rec[0]["from"];
    }
    if($_GET["user_id"] == "" || !isset($_GET["user_id"])) exitPage("You must select a name!", true);

    $user = $tdb->get("users", $_GET["user_id"]);
    if($user[0]["level"] != 1) {
        $void .= 1;
        $echo .= "You cannot Block ".$user[0]["user_name"].", He/She is an Administrator/Moderator<br>";
    }

    if(FALSE !== ($blockedIds = getUsersPMBlockedList($_COOKIE["id_env"]))) {
        //print_r($blockedIds);
        if(true === (in_array($user[0]["id"], $blockedIds))) {
            $void .= 1;
            $echo .= $user[0]["user_name"]." is already blocked<br>";
        }
    } else {
        if($void == "") {
            $blockedIds = array();
            addUsersPMBlockedList($_COOKIE["id_env"]);
        }
    }

    if($void == "") {
        if($blockedIds[0] == "") $new = $user[0]["id"];
        else $new = implode (",", $blockedIds).",".$user[0]["id"];
        //print_r("<br>".$new);
        if(!editUsersPMBlockedList($_COOKIE["id_env"], $new)) {
            exitPage("<b>Error</b>:  An unexpected error occured when editing PMBlockedList file, <b>USER NOT FOUND</b><br>", true);
        }
        $echo = "Successfully Blocked <b>".$user[0]["user_name"]."</b>!<br>";
        if($after == "done") {
            exitPage($echo, true);
        } elseif($action == "close" || $after == "close") {
            require_once('./includes/header.php');
            echo $echo;
            require_once('./includes/footer.php');
            redirect("viewpm.php?action=close", "3");
        } elseif($ref != "") {
            require_once('./includes/header.php');
            echo $echo;
            require_once('./includes/footer.php');
            redirect($ref."?section=$section&id=".$_GET["id"], "2");
        } else {
            $action = "";
        }
    } else {
        exitPage(strlen($void)." error(s) occured:<br>".$echo, true);
    }
    unset($rec, $user, $ck, $k, $void, $new, $f, $i);
} elseif($action == "Unblock") {
    $blockedIds = getUsersPMBlockedList($_COOKIE["id_env"]);
    deleteWhiteIndex($blockedIds);

    $keep = array();
    $count = count($blockedIds);
    $num = 0;
    for($i=0;$i<$count;$i++) {
        if(!isset($_POST[$blockedIds[$i]])) {
            $keep[] = $blockedIds[$i];
        } elseif(isset($_POST[$blockedIds[$i]])) {
            $num++;
        }
    }
    if($keep[0] == "") $new = "";
    elseif($keep[1] == "") $new = $keep[0];
    else $new = implode(",", $keep);
    $blockedIds = $keep;
    editUsersPMBlockedList($_COOKIE["id_env"], $new);
    if($num != 0) {
        $echo = "<p align='center'>Successfully unblocked <b>$num</b> user";
        if($num > 1) $echo .= "s";
        $echo .= "</p>";
    } else {
        $echo .="<p align='center'><b>No users were unblocked!</b></p>";
    }
    $action = "";
} elseif($action == "adduser") {
    $where = "<a href='pmsystem.php'>Private Msg</a> ".$_CONFIG["where_sep"]." <a href='pmblocklist.php'>Manage Blocked Users</a> ".$_CONFIG["where_sep"]." Add User";
    require_once('./includes/header.php');
    echo "<center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'><tr><td colspan='2' bgcolor='white'>";
    echo $error;
    $blockedIds = explode(",", getUsersPMBlockedList($_COOKIE["id_env"]));

    $select = $tdb->createUserSelectFormObject("user_id", true, true, true, "", $blockedIds);
    echo "<form action='".$PHP_SELF."' method='GET' onSubmit='submitonce(this)' enctype='multipart/form-data'><input type='hidden' name='action' value='add'><br><center>$select <input type='submit' value='Add User'></center></form>
        <br><font face='$font_face' size='$font_s' color='$font_main_color'><i>You are not allowed to block Administrators/Moderators</i></font>";
    echo "</tr></td></table>$skin_tablefooter";
}
if($action == "") {
    $where = "<a href='pmsystem.php'>Private Msg</a> ".$_CONFIG["where_sep"]." Manage Blocked Users";
    require_once('./includes/header.php');
    if(!isset($echo))  $echo = "<Br>";
    echo $echo;

    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>
          <tr>
            <td bgcolor='$header' align='right'width='80%'><font face='$font_face' size='$font_m' color='$font_color_header'>Users</font></td>
            <td bgcolor='$header' align='center'width='20%'><p align='center'><font face='$font_face' size='$font_m' color='$font_color_header'>UnBlock?</font></p></td></tr>
              <form action='$PHP_SELF' method='POST' onSubmit='submitonce(this)' enctype='multipart/form-data'><input type='hidden' name='action' value='unban'>";

    $none = 0;
    $count = 0;
    if(FALSE !== ($blockedIds = getUsersPMBlockedList($_COOKIE["id_env"]))) {
        $count = count($blockedIds);
        for($i=0;$i<$count;$i++) {
            if($blockedIds[$i] != "") {
                $user = $tdb->get("users", $blockedIds[$i]);
                echo "<tr>
                    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='profile.php?action=get&id=".$user[0]["id"]."' target='_blank'>".$user[0]["user_name"]."</a></font></td>
                    <td bgcolor='$table1' width=20% align='center'><input type='checkbox' name='$blockedIds[$i]' value='CHECKED'></td></tr>";
            } else {
                $none++;
            }
        }
    }
    if($none == $count) {
        echo "<tr><td bgcolor='$table1' align='center' colspan='2'><p align='center'><font size='$font_m' face='$font_face' color='$font_color_main'>No Blocked Users</font></p></td></tr>";
        $disable = "DISABLED";
    } else $disable = "";
    echo "<tr><td bgcolor='$table1' width='100%' colspan='2'><p align='right'><input type='submit' name='action' value='Unblock' $disable></form>
        </td></tr>$skin_tablefooter";
}
require_once('./includes/footer.php');
?>