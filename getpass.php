<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3

if(!isset($_GET["ref"])) $ref = "index.php";
else $ref = $_GET["ref"];

require_once("./includes/class/func.class.php"); 
$where = "Lost Password";

$e = false; 
if (isset($_POST["u_name"])) {
    $user = $tdb->query("users", "user_name='".$_POST['u_name']."'", 1, 1);
    if($user[0]['id'] != '') {
        $results = $tdb->basicQuery("getpass", "user_id", $user[0]['id'], 1, 1);
        if($results[0]['id'] != '') {
            $expire = alterDate($results[0]['time'], 2, 'days');
            if(mkdate() > $expire) {
                $tdb->delete('getpass', $results[0]['id']);
                unset($results);
            }
        }
        if($results[0]['id'] == '') {
            $passcode = rand();
            $request_ID = $tdb->add("getpass", array("passcode_HASH" => generateHash($passcode), time => mkdate(), "user_id" => $user[0]['id']));
            if(FALSE !== ($question_mark_where = strpos($_SERVER['REQUEST_URI'], '?'))) {
                $url = substr($_SERVER['$REQUEST_URI'], 0, $question_mark_where);
                
            } else $url = $_SERVER['REQUEST_URI'];
                
            mail($user[0]["email"], "Lost Password Confirmation", "The IP Address: ".$_SERVER['REMOTE_ADDR']." has requested a password retrieval from an account linked to this e-mail address.  If you did request this, visit here to confirm that you would like to change your password for ".$user[0]["user_name"]."\n\nhttp://".$HTTP_HOST.$url."?request_ID=".$request_ID."&passcode=".$passcode."\n\nBut you did not request a Password Retrieval, please alert an administrator, and give them the IP Address provided.", "From: ".$_REGIST['admin_email']); 
            $error = "A confirmation e-mail has been sent to the e-mail address attached to the username.";
            $e = true;
        } else $error = "Unable to send: A confirmation e-mail has already been sent to the e-mail address attched to the username with in the last 48 hours.";
    } else $error = "Unable to find the specified username";
} 
if(isset($_POST['passcode']) && isset($_POST['request_ID'])) {
    $results = $tdb->get('getpass', $_POST['request_ID']);
    $passcode_HASH = generateHash($_POST['passcode'], $results[0]['passcode_HASH']);
    if($passcode_HASH == $results[0]['passcode_HASH']) {
        if($_POST['pass1'] != $_POST['pass2']) {
            $_GET['passcode'] = $_POST['passcode'];
            $_GET['request_ID'] = $_POST['request_ID'];
            $error = "Passwords do not match";
        } else {
            $tdb->edit('users', $results[0]['user_id'], array("password" => generateHash($_POST['pass1'])));
            $tdb->delete('getpass', $_POST['request_ID']);
            $where = "Lost Password ".$_CONFIG["where_sep"]." Set New";
            require_once('includes/header.php');
            echo "Your password was successfully changed";
            require_once("includes/footer.php");
            redirect('login.php', 2);
            exit;
        }
    } else {
        $error = "Unable to confirm: Unvalid Passcode";
        $e = true;
    }
}
if(isset($_GET['passcode']) && isset($_GET['request_ID'])) {
    $_GET['passcode'] = trim($_GET['passcode']);
    $results = $tdb->get('getpass', $_GET['request_ID']);
    $expire = alterDate($results[0]['time'], 2, 'days');
    if(mkdate() < $expire) {
        $passcode_HASH = generateHash($_GET['passcode'], $results[0]['passcode_HASH']);
        if($passcode_HASH == $results[0]['passcode_HASH']) {
            
            $where = "Lost Password ".$_CONFIG["where_sep"]." Create New";
            require_once('./includes/header.php');
            echo '<form action="'.basename(__FILE__).'" method="POST"><center><input type="hidden" name="passcode" value="'.$_GET['passcode'].'"><input type="hidden" name="request_ID" value="'.$_GET["request_ID"].'">';
            echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
            echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'><tr><td colspan='2' bgcolor='white'> 
            <center><table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'> 
            <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Set new password</font></b></td></tr> 
            <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>New Password:</font></td><td bgcolor='$table1'><input type=password name='pass1' size=30></td></tr> 
            <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>New Password:</font></td><td bgcolor='$table1'><input type=password name='pass2' size=30></td></tr> 
            <tr><td bgcolor='$table1' colspan='2'><input type=submit value='Submit'></td></tr> 
            </table></form></tr></td></table>$skin_tablefooter"; 
            require_once('includes/footer.php');
            exit;
        } else {
            $error = "Unable to confirm: Unvalid Passcode";
            $e = true;
        }
    } else {
        $tdb->delete('getpass', $_GET['request_ID']);
        $error = "Unable to confirm: The request expired.  Please request again";
    }
}
$where = "Lost Password ".$_CONFIG["where_sep"]." Request";
require_once('./includes/header.php'); 
    if (isset($error)) { 
        echo "<font color='red' size='$font_m' face='$font_face'>$error</font>"; 
        if ($e) { require_once('./includes/footer.php'); exit; }
    }
if (!$tdb->is_logged_in()) {
    if(!isset($_POST['u_name'])) $_POST['u_name'] = '';
    echo "<br>"; 
    echo "<form action='".basename(__FILE__)."?ref=$ref' method=POST><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'> 
    <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Request pass</font></b></td></tr> 
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>User Name:</font></td><td bgcolor='$table1'><input type=text name=u_name value='".$_POST['u_name']."' size=30> Enter your username and a confirmation e-mail will be emailed to you.</td></tr> 
    <tr><td bgcolor='$table1' colspan='2'><input type=submit value='Submit'></td></tr> 
    </table>".$skin_tablefooter."</form>"; 
    
} else { 
    echo "<center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'><tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>You're already Logged in</font></b></td></tr> 
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><a href='logoff.php'>Log off</a></font></td></tr> 
    </table>$skin_tablefooter</center>"; 
} 

require_once("./includes/footer.php"); 
?>
