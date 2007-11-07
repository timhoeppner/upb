<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

// Ultimate PHP Board Login

require_once('./includes/class/func.class.php');
$where = "Login";

$show = 0;
$e = 0;
if(isset($_POST["u_name"]) && isset($_POST["u_pass"])) {
    // Attempt to login
    if(($r = $tdb->login_user($_POST["u_name"], $_POST["u_pass"], $key)) === FALSE) {
        $error = "Either your Username or your Password was incorrect.";
    } else {
        
        $now = mkdate();
        $ses_info = lastvisit($r['id']);
        if($ses_info == '') $ses_info = $now;

        if(headers_sent()) $error_msg = 'Could not login: headers sent.';
        else {
            setcookie("lastvisit", $ses_info);
            //end lastvisit info
            $r['uniquekey'] = generateUniqueKey();
            $tdb->edit('users', $r['id'], array('uniquekey' => $r['uniquekey']));

            if($_POST["remember"] == "YES") {
                setcookie("remember", '1', (time() + (60*60*24*7)));
                setcookie("user_env", $r["user_name"], (time() + (60*60*24*7)));
                setcookie("uniquekey_env", $r["uniquekey"], (time() + (60*60*24*7)));
                setcookie("power_env", $r["level"], (time() + (60*60*24*7)));
                setcookie("id_env", $r["id"], (time() + (60*60*24*7)));
            } else {
                setcookie("remember", '');
                setcookie("user_env", $r["user_name"]);
                setcookie("uniquekey_env", $r["uniquekey"]);
                setcookie("power_env", $r["level"]);
                setcookie("id_env", $r["id"]);
            }
            setcookie("timezone", $r["timezone"], (time() + (60*60*24*7)));
            if ($_GET["ref"] == "") $_GET["ref"] = "index.php";
            $error = "<font color='#000020'>Logged on successfully as user: ".$r["user_name"].", redirecting...</font>
        <meta http-equiv='refresh' content='2;URL=".$_GET["ref"]."'>";
        }
        $e = 1;
    }
}

require_once('./includes/header.php');
if (!$tdb->is_logged_in()) {
    if (isset($error)) {
        echo "<font color='red' size='$font_m' face='$font_face'>$error</font>";
        if ($e == 1) exitPage("");
    }
    if($_COOKIE["remember"] != "") $remember = "checked";
    else $remember = "";

    echo "<form action='login.php?ref=".$_GET["ref"]."' method=POST><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
    <tr><td id=category colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Login</font></b></td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>User Name:</font></td><td bgcolor='$table1'><input class='txtBox' type=text name=u_name size=30 value=".$_POST["u_name"]."></td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Password:</font></td><td bgcolor='$table1'><input class='txtBox' type=password name=u_pass size=30></td></tr>
    <tr><td bgcolor='$table1' colspan='2'><font size='$font_m' face='$font_face' color='$font_color_main'>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='remember' value='YES' id='rememberme' ".$remember."><label for='rememberme'>&nbsp;&nbsp;Remember me?</label></font></td></tr>
    <tr><td bgcolor='$table1' colspan='2'><input type=submit class='txtBox' value='Login'>&nbsp;&nbsp;&nbsp;<font size='1'><b><a href='getpass.php'>(Lost Password?)</a></b> <b><a href='register.php'>(Need to Register?)</a></b></font></td></tr>
    </table>$skin_tablefooter</form>";
} else {
    echo "<center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
    <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>You are already Logged in</font></b></td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><a href='logoff.php'>Log off</a></font></td></tr>
    </table>$skin_tablefooter";
}

require_once('./includes/footer.php');
?>
