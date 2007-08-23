<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3

require_once("./includes/class/func.class.php");
$rec = $tdb->get("users", $id);

if(!(isset($_COOKIE["power_env"]) && isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["id_env"]))) exitPage("you are not even logged in<meta http-equiv='refresh' content='2;URL=login.php?ref=email.php?id=$id'>");
if(!$tdb->is_logged_in()) exitPage("you are not authorized to be here.");
if(isset($subject) && isset($message)) {
    $where = "<a href='showmembers.php'>Members List</a> ".$_CONFIG["where_sep"]." Send email";
    require_once('./includes/header.php');
    $self = $tdb->get("users", $_COOKIE["id_env"]);
    $from = @$self[0]["email"]; //Email address you want it to 'appear' to come from
    $mailheader = "From: $from\r\n";
    $mailheader .= "Reply-To: $from\r\n";
    $mailbody = $message;
    $mailforms = mail(@$rec[0]["email"], $subject, $mailbody, $mailheader);

    if($mailforms) {
        echo "The email was sent successfully to ".$rec[0]["user_name"]."...";
        require_once("./includes/footer.php");
        redirect("index.php", 2);
    } else echo "The email was NOT sent to ".$rec[0]["user_name"]." due to an error...";
} else {
    $where = "<a href='showmembers.php'>Members List</a> ".$_CONFIG["where_sep"]." Send email";
    require_once('./includes/header.php');
    echo "<form name='form1' method='post' action='$PHP_SELF' >
            <div align='center'>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "
    <table width='565' cellspacing='1' cellpadding='1' height='347'>
    <tr><td width='159'><div align='center'>To:</div></td>
    <td width='221'><div align='left'> ".$rec[0]["user_name"]." </div></td>
    <td width='173'>&nbsp;</td></tr>
    <tr><td width='159'><div align='center'>Your subject: </div></td>
    <td width='221'><div align='left'><input type='text' name='subject' value='$subject'></div></td>
    <td width='173'><div align='center'><font color='#FF0000'> required</font></div></td></tr>
    <tr><td width='159'><div align='center'>Your message: </div></td>
    <td width='221'><div align='left'><textarea name='message' cols='30' rows='7'>$message</textarea></div></td>
    <td width='173'><div align='center'><font color='#FF0000'>required</font></div></td></tr>
    <tr><td colspan='3'><div align='center'><input type='submit' value='Send'><input type='reset' value='Reset' name='Reset'></div></td></tr>
    <tr><td width='159'><div align='center'></div></td>
    <td width='221'><div align='center'><input type='hidden' name='id' value='$id'></div></td>
    <td width='173'><div align='center'></div></td></tr></table>$skin_tablefooter</div></form>";
}
require_once("./includes/footer.php");
?>