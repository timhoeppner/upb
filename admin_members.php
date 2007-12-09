<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <A href='admin_members.php'>Manage Members</a>";
require_once("./includes/header.php");

if(!$tdb->is_logged_in() || $_COOKIE["power_env"] != 3) exitPage("you are not authorized to be here.");
if($_GET["action"] == "edit") {
    if(!isset($_GET["id"])) exitPage("No id selected");
    $rec = $tdb->get("users", $_GET["id"]);
    if(isset($_POST["a"])) {
        if(!isset($_POST["email"])) exitPage("please enter an email!");
        if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["email"])) exitPage("please enter a valid email!");
        if(strlen(chop($_POST["sig"])) > 200) exitPage("You cannot have more than 200 characters in the signature.");
        if(substr(trim(strtolower($_POST["url"])), 0, 7) != "http://") $_POST["url"] = "http://".$_POST["url"];
        if($_POST["timezone"]{0} == '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);

        $new = array();
        if($_POST["level"] != $rec[0]["level"]) $new["level"] = $_POST["level"];
        if($_POST["email"] != $rec[0]["email"]) $new["email"] = $_POST["email"];
        if($_POST["status"] != $rec[0]["status"]) $new["status"] = $_POST["status"];
        if($_POST["location"] != $rec[0]["location"]) $new["location"] = $_POST["location"];
        if($_POST["url"] != $rec[0]["url"]) $new["url"] = $_POST["url"];
        if($_POST["avatar"] != $rec[0]["avatar"]) $new["avatar"] = $_POST["avatar"];
        if($_POST["icq"] != $rec[0]["icq"]) $new["icq"] = $_POST["icq"];
        if($_POST["yahoo"] != $rec[0]["yahoo"]) $new["yahoo"] = $_POST["yahoo"];
        if($_POST["msn"] != $rec[0]["msn"]) $new["msn"] = $_POST["msn"];
        if(chop($_POST["sig"]) != $rec[0]["sig"]) $new["sig"] = chop($_POST["sig"]);
        if($_POST["timezone"] != $rec[0]["timezone"]) $new["timezone"] = $_POST["timezone"];
        if(!empty($new)) $tdb->edit("users", $_GET["id"], $new);
        echo "Successfully edited ".$rec[0]["user_name"]."!<br><a href='admin_members.php?page=".$_GET["page"]."'>Go Back to Member's list</a>";
    } else {
        echo "<form method='POST' action=".$PHP_SELF."?action=edit&id=".$_GET["id"]."&page=".$_GET["page"]."><input type='hidden' name='a' value='1'>";
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
        echo " <table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
  <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>".$rec[0]["user_name"]."&#160; </font><font size='$font_s' face='$font_face' color='$font_color_main'><a href='admin_members.php?action=pass&id=".$_GET['id']."'>Change Password?</a></font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>User Power:</b></font></td><td bgcolor='$table1' width='80%'><select size='1' name='level'>".createUserPowerMisc($rec[0]["level"], 7, TRUE)."</td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>E-mail Address:</b></font></td>
    <td bgcolor='$table1' width='80%'><input type='text' name='email' size='20' value='".$rec[0]["email"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Public E-mail?</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>";
        if($rec[0]["view_email"] == 1) echo "YES";
        else echo "NO";
        echo "</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Sign up on the Mailing List?</b></font></td>
    <td bgcolor='$table1' bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>";
        if($rec[0]["mail_list"] == 1) echo "YES";
        else echo "NO";

        $f = fopen(DB_DIR."/new_pm.dat", 'r');
        fseek($f, (((int)$rec[0]["id"] * 2) - 2));
        $tmp_new_pm = fread($f, 2);
        fclose($f);

        $lastvisit = getlastvisit($_GET['id']);
        
        echo "</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>No. of Unread Private Messages</b></font></td>
    <td bgcolor='$table1' bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$tmp_new_pm."</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Status</b></font></td>
    <td bgcolor='$table1' width='80%'><input type='text' name='status' size='20' value='".$rec[0]["status"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Location:</b></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='location' size='20' value='".$rec[0]["location"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Website:</font></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='website' size='20' value='".$rec[0]["url"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Avatar</b></font></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='avatar' size='20' value='".$rec[0]["avatar"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>ICQ Messenger</b></font></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='icq' size='20' value='".$rec[0]["icq"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Yahoo Messenger</b></font></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='yahoo' size='20' value='".$rec[0]["yahoo"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>MSN Messenger</b></font></td>
      <td bgcolor='$table1' width='80%'><input type='text' name='msn' size='20' value='".$rec[0]["msn"]."' /></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Signature</b></font></td>
      <td bgcolor='$table1' width='80%'><textarea rows='10' name='sig' cols='45' rows='10'>".$rec[0]["sig"]."</textarea></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Number of Posts:</b></font></td><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$rec[0]["posts"]."</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Last Login:</b></font></td><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".(gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d') ? '<i>today</i>' : (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))) ? '<i>yesterday</i>' : gmdate("Y-m-d", user_date($lastvisit))))."</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Registered Date:</b></font></td><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Time zone</b></font></td><td bgcolor='$table1' width='80%'><input type='text' name='timezone' size='20' value='".$rec[0]["timezone"]."' /></td></tr>
    <tr><td bgcolor='$table1' width='160%' colspan='2'><input type='submit' value='Submit' name='B1' /><input type='reset' value='Reset' name='B2' /></td></tr></table></form>";
    }
} elseif($_GET["action"] == "pass" && isset($_GET["id"])) {
    $user = $tdb->get("users", $_GET["id"]);
    if(isset($_POST["a"])) {
        if($_POST["pass"] != $_POST["pass2"]) exitPage("The passwords don't match!");
        if(strlen($_POST["pass"]) < 4) exitPage("The password has to be longer then 4 characters");
        $tdb->edit("users", $_GET["id"], array("password" => generateHash($_POST["pass"])));
        $msg = "You Password was changed by ".$_COOKIE["user_env"]." on the website ".$_CONFIG["homepage"]." to \"".$_POST["pass"]."\"";
        if(isset($_POST["reason"])) $msg .= "\n\n".$_COOKIE["user_env"]."'s reason was this:\n".$_POST["reason"];
        if (ini_get('sendmail_path') != "")
          mail($user[0]["email"], "Password Change Notification", "Password Changed by :".$_COOKIE["user_env"]."\n\n".$msg, "From: ".$_REGISTER["admin_email"]);
        echo "You successfully changed ".$user[0]["user_name"]."'s password to ".$_POST["pass"];
        redirect('admin_members.php',5);
    } else {
        echo "<form method='POST' action=".$PHP_SELF."?action=pass&id=".$_GET["id"]."><input type='hidden' name='a' value='1'>";
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
        echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
  <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>".$user[0]["user_name"]."</font></td></tr>
    <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>New Password:</font></td><td bgcolor='$table1'><input type='password' name='pass'></td></tr>
  <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>Confirm Password:</font></td><td bgcolor='$table1'><input type='password' name='pass2'></td></tr>
  <tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>Reason:</font></td><td bgcolor='$table1'><textarea name=reason></textarea></td></tr>
  <tr><td bgcolor='$table1' colspan='2'><input type='submit' value='Change Password'></td></tr></table></form>";
  if (ini_get('sendmail_path') != "")
    echo "<p align='center'>An E-mail will be sent, notifying the user of the change of password by <i>".$_COOKIE["user_env"]."</i></p>";
  else
    echo "<p align='center'>Please do not forget to inform the user of the change of password by <i>".$_COOKIE["user_env"]."</i></p>";
    }
} elseif($_GET["action"] == "delete") {
    if(!isset($_GET["id"])) exitPage("No id selected.");
    $rec = $tdb->get("users", $_GET["id"]);
    if($_POST["verify"] == "Ok") {
        $tdb->delete("users", $_GET["id"]);
        echo "Successfully deleted ".$rec[0]["user_name"].".<br>- <a href='admin_members.php'>Go Back</a>";
    } elseif($_POST["verify"] == "Cancel") {
        echo "<meta http-equiv='refresh' content='0;URL=admin_members.php'>";
    } else {
        ok_cancel("admin_members.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete <b><a href='profile.php?action=get&id=".$_GET["id"]."' targer='_blank'>".$rec[0]["user_name"]."</a></b>?");
    }
} else {
    if($_GET["page"] == "") $_GET["page"] = 1;
    $users = $tdb->listRec("users", ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"] + 1), $_CONFIG["topics_per_page"]);

    $c = $tdb->getNumberOfRecords("users");
    if ($c <= $_CONFIG["topics_per_page"]) $num_pages = 1;
    elseif (($c % $_CONFIG["topics_per_page"]) == 0) $num_pages = ($c / $_CONFIG["topics_per_page"]);
    else $num_pages = ($c / $_CONFIG["topics_per_page"]) + 1;
    $pageStr = createPageNumbers($_GET["page"], $num_pages, $_SERVER['QUERY_STRING']);

    //echo "<table border='0' cellspacing='0' cellpadding='4' width='".$_CONFIG["table_width_main"]."' align='center'><tr>
    //<td><font size='$font_m' face='$font_face' color='$font_color_main'>".$pageStr."</font></td></tr></table><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

    <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
    echo "<tr><td colspan='3' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>
    </table><table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

    <tr><td colspan='8' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Manage Members</font></b></td></tr>
    <tr><th bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'>ID</font></td>
        <th bgcolor='$table1' width=20%><font size='$font_m' face='$font_face' color='$font_color_main'>Username</font></td>
        <th bgcolor='$table1' width=15%><font size='$font_m' face='$font_face' color='$font_color_main'>User Power</font></td>
        <th bgcolor='$table1' width=20%><font size='$font_m' face='$font_face' color='$font_color_main'>Email</font></td>
        <th bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'>Posts</font></td>
        <th bgcolor='$table1' width=12%><font size='$font_m' face='$font_face' color='$font_color_main'>Last Login</font></td>
        <th bgcolor='$table1' width=12%><font size='$font_m' face='$font_face' color='$font_color_main'>Registered Date</font></td>
        <th bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'>Ban</font></td>
        <th bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'>Edit</font></td>
        <th bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'>Delete</font></td></tr>";
    if($users[0] == "") {
        echo "<tr><td bgcolor='$table1' colspan='3'><font size='$font_m' face='$font_face' color='$font_color_main'>No records found</font></td></tr>";
    } else {
        
        $bList = file(DB_DIR."/banneduser.dat");
        foreach($users as $user) {
            
            $lastvisit = getlastvisit($user['id']);
            //if(gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d')) $lastvisit =
            //(gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d') ? '<i>today</i>' : (gmdate('Y-m-d', $lastvisit) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))) ? '<i>yesterday</i>' : gmdate("Y-m-d", user_date($lastvisit))))

            //show each user
            echo "<tr><td bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'><center><b>".$user["id"]."</b></center></font></td>
                <td bgcolor='$table1' width=18%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='profile.php?action=get&id=".$user["id"]."'>".$user["user_name"]."</a></font></td>
                <td bgcolor='$table1' width=11%><font size='$font_m' face='$font_face' color='$font_color_main'>".createUserPowerMisc($user["level"], 4)."</font></td>";
            if($user['view_email']) echo "<td bgcolor='$table1' width=18%><font size='$font_m' face='$font_face' color='$font_color_main'>".$user["email"]."</font></td>";
            else echo "<td bgcolor='$table1' width=18%><font size='$font_m' face='$font_face' color='$font_color_main'><i>".$user["email"]."</i></font></td>";
            echo "<td bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'>".$user["posts"]."</font></td>
                <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'>";
                
            if (gmdate('Y-m-d', user_date($lastvisit)) == gmdate('Y-m-d')) 
              echo '<i>today</i>'; 
            elseif (gmdate('Y-m-d', user_date($lastvisit)) == gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m'), ((int)gmdate('d') - 1), gmdate('Y'))))
              echo '<i>yesterday</i>';
            elseif ($lastvisit == "")
              echo '<i>never</i>';
            else
              echo gmdate("Y-m-d", user_date($lastvisit))."</font></td>";
            echo "<td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'>".gmdate("Y-m-d", user_date($user["date_added"]))."</font></td>";


            echo "<td bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_banuser.php?ref=admin_members.php?page=".$_GET["page"]."&action=";
            if(!in_array($user["user_name"], $bList)) echo 'addnew&newword='.$user["user_name"]."'>"; else echo 'delete&word='.$user["user_name"]."'><b>Un</b>";
            echo "Ban</a></font></td>
                <td bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_members.php?action=edit&id=".$user["id"]."&page=".$_GET["page"]."'>Edit</a></font></td>
                <td bgcolor='$table1' width=7%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_members.php?action=delete&id=".$user["id"]."'>Delete</a></font></td></tr>";
        }
        
    }
    echo "</table>$skin_tablefooter";

    echo "<table border='0' cellspacing='0' cellpadding='4' width='".$_CONFIG["table_width_main"]."' align='center'><tr>
        <td><font size='$font_m' face='$font_face' color='$font_color_main'>".$pageStr."</font></td></tr>";

    echo '<tr><td><p align="left"><i>Italized e-mails means the e-mails are private, or not displayed to guests, members, or moderators.</i></p></td></tr></table><center>';
}
require_once("./includes/footer.php");
?>
