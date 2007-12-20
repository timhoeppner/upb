<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once('./includes/class/func.class.php');
$where = "User CP";

if(isset($_POST["u_edit"])) {
    if(!($tdb->is_logged_in())) {
        echo "<html><head><meta http-equiv='refresh' content='2;URL=login.php?ref=profile.php'></head></html>";
        exit;
    } else {
        $rec = array();
        if(!isset($_POST["u_email"])) exitPage("please enter your email!", true);
        if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["u_email"])) exitPage("please enter a valid email!", true);
        if(strlen($_POST["u_sig"]) > 200) exitPage("You cannot have more than 200 characters in your signature.", true);
        $user = $tdb->get("users", $_COOKIE["id_env"]);
        if(strlen($_POST["u_newpass"]) > 0) {
            if($user[0]['password'] != generateHash($_POST['u_oldpass'], $user[0]['password'])) exitPage('You old password does not match the one on file!', true);
            if($_POST["u_newpass"] != $_POST["u_newpass2"]) exitPage("your pass and pass confirm are not matching!", true);
            if(strlen($_POST["u_newpass"]) < 4) exitPage("your password has to be longer then 4 characters", true);
            $rec["password"] = generateHash($_POST["u_newpass"]);
            setcookie("user_env", "");
            setcookie("uniquekey_env", "");
            setcookie("power_env", "");
            setcookie("id_env", "");
            $ht = "<meta http-equiv='refresh' content='2;URL=login.php'>";
        } else $ht = "<meta http-equiv='refresh' content='2;URL=profile.php'>";

        if($user[0]["email"] != $_POST["u_email"]) $rec["email"] = $_POST["u_email"];
        if($user[0]["u_sig"] != chop($_POST["u_sig"])) $rec["sig"] = chop($_POST["u_sig"]);

        if(substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://".$_POST["u_site"];
        if($user[0]["url"] != $_POST["u_site"]) $rec["url"] = $_POST["u_site"];

        if($_POST["u_timezone"]{0} == '+') $_POST["u_timezone"] = substr($_POST["u_timezone"], 1);

        if($_POST["show_email"] != "1") $_POST["show_email"] = "0";
        if($_POST["email_list"] != "1") $_POST["email_list"] = "0";
        if($user[0]["view_email"] != $_POST["show_email"]) $rec["view_email"] = $_POST["show_email"];
        if($user[0]["mail_list"] != $_POST["email_list"]) $rec["mail_list"] = $_POST["email_list"];
        if($user[0]["location"] != $_POST["u_loca"]) $rec["location"] = $_POST["u_loca"];
        if(FALSE === mod_avatar::verify_avatar($_POST['avatar'], $user[0]['avatar_hash'])) {
            $new_avatar = mod_avatar::new_parameters($_POST['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
            $rec = array_merge($rec, $new_avatar);
            unset($new_avatar);
        }
        if($user[0]["icq"] != $_POST["u_icq"]) $rec["icq"] = $_POST["u_icq"];
        if($user[0]["aim"] != $_POST["u_aim"]) $rec["aim"] = $_POST["u_aim"];
        if($user[0]["yahoo"] != $_POST["u_yahoo"]) $rec["yahoo"] = $_POST["u_yahoo"];
        if($user[0]["msn"] != $_POST["u_msn"]) $rec["msn"] = $_POST["u_msn"];
        if($user[0]["timezone"] != $_POST["u_timezone"]) {
            $rec["timezone"] = $_POST["u_timezone"];
            setcookie("timezone", $_POST["u_timezone"], (time() + (60*60*24*7)));
        }
        $tdb->edit("users", $_COOKIE["id_env"], $rec);

        exitPage('Your profile has been changed successfully.'.$ht, true);
    }
} elseif(isset($_GET["action"])) {
    if(!isset($_GET["id"])) {
        echo "<html><head><meta http-equiv='refresh' content='0;URL=index.php'></head></html>";
        exit;
    } else {
        $rec = $tdb->get("users", $_GET["id"]);
        if(FALSE === mod_avatar::verify_avatar($rec[0]['avatar'], $rec[0]['avatar_hash'])) {
        	$new_avatar = mod_avatar::new_parameters($rec[0]['avatar'], $_CONFIG['avatar_width'], $_CONFIG['avatar_height']);
            $tdb->edit('users', $rec[0]['id'], $new_avatar);
            $rec[0] = array_merge($rec[0], $new_avatar);
            unset($new_avatar);
        }
        if($rec[0]["level"] == '1') {
            $statuscolor = $_STATUS["userColor"];
            if($rec[0]["posts"] >= $_STATUS["member_post1"]) $status = $_STATUS["member_status1"];
            elseif($rec[0]["posts"] >= $_STATUS["member_post2"]) $status = $_STATUS["member_status2"];
            elseif($rec[0]["posts"] >= $_STATUS["member_post3"]) $status = $_STATUS["member_status3"];
            elseif($rec[0]["posts"] >= $_STATUS["member_post4"]) $status = $_STATUS["member_status4"];
            elseif($rec[0]["posts"] >= $_STATUS["member_post5"]) $status = $_STATUS["member_status5"];
        } elseif($rec[0]["level"] == '2') {
            $statuscolor = $_STATUS["modColor"];
            if($rec[0]["posts"] >= $_STATUS["mod_post1"]) $status = $_STATUS["mod_status1"];
            elseif($rec[0]["posts"] >= $_STATUS["mod_post2"]) $status = $_STATUS["mod_status2"];
            elseif($rec[0]["posts"] >= $_STATUS["mod_post3"]) $status = $_STATUS["mod_status3"];
            elseif($rec[0]["posts"] >= $_STATUS["mod_post4"]) $status = $_STATUS["mod_status4"];
            elseif($rec[0]["posts"] >= $_STATUS["mod_post5"]) $status = $_STATUS["mod_status5"];
        } elseif($rec[0]["level"] == '3') {
            $statuscolor = $_STATUS["adminColor"];
            if($rec[0]["posts"] >= $_STATUS["admin_post1"]) $status = $_STATUS["admin_status1"];
            elseif($rec[0]["posts"] >= $_STATUS["admin_post2"]) $status = $_STATUS["admin_status2"];
            elseif($rec[0]["posts"] >= $_STATUS["admin_post3"]) $status = $_STATUS["admin_status3"];
            elseif($rec[0]["posts"] >= $_STATUS["admin_post4"]) $status = $_STATUS["admin_status4"];
            elseif($rec[0]["posts"] >= $_STATUS["admin_post5"]) $status = $_STATUS["admin_status5"];
        } else {
            $status = 'Member';
            $statuscolor = $_STATUS["membercolor"];
        }
        if($rec[0]["status"] != "") $status = $rec[0]["status"];
        require_once('./includes/header.php');

        echo "<center>";
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

        echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
  <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Profile</font></b></td></tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>login</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$rec[0]["user_name"]."</font></td>
  </tr>
    <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>status</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$statuscolor'>$status</font></td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>email</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>";

        if((bool)$rec[0]["view_email"]) echo "<a href='mailto:".$rec[0]["email"]."'>".$rec[0]["email"]."</a>";
        else echo "not public";

        if(@$rec[0]["location"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>location</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$rec[0]["location"]."</font></td>
  </tr>";
        echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>avatar</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
    <img src=\"".$rec[0]["avatar"]."\" width=\"".$rec[0]['avatar_width']."\" height=\"".$rec[0]['avatar_height']."\"><br>";

        echo "<tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>PM Status</b></font></td>
        <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>";
        require_once('./includes/inc/privmsg.inc.php');
        $blockedList = getUsersPMBlockedList($_GET["id"]);
        if($_GET["id"] == $_COOKIE["id_env"]) {
            echo "You cannot send yourself a Private Msg";
        } elseif($_COOKIE["id_env"] == "" || $_COOKIE["id_env"] == "0") {
            echo "You must login before you can send this user a PM";
        } elseif(in_array($_COOKIE["id_env"], $blockedList)) {
            echo "You are Blocked";
        } else {
            echo "<a href='newpm.php?to=".$_GET["id"]."' target='_blank'>you are not Blocked.  Send PM?</a>";
        }
        echo "</font></td></tr>";
        if(@$rec[0]["url"] != "" || $rec[0]["url"] != "http://") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>homepage</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='".$rec[0]["url"]."' target='_blank'>".$rec[0]["url"]."</a></font></td>
  </tr>";

        if(@$rec[0]["icq"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>icq</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$rec[0]["icq"]."&action=message'><img src='images/icq.gif' border='0'>&nbsp;".$rec[0]["icq"]."</a></font></td>
  </tr>";

        if(@$rec[0]["aim"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>aim</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='aim:goim?screenname=".$rec[0]["aim"]."'><img src='images/aol.gif' border='0'>&nbsp;".$rec[0]["aim"]."</a></font></td>
  </tr>";
        if(@$rec[0]["msn"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>msn</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='http://members.msn.com/".$rec[0]["msn"]."' target='_blank'><img src='images/msn.gif' border='0'>&nbsp;".$rec[0]["msn"]."</a></font></td>
  </tr>";
        if(@$rec[0]["yahoo"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Y!</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='http://edit.yahoo.com/config/send_webmesg?.target=".$rec[0]["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$rec[0]["yahoo"]."&m=g&t=0'>&nbsp;".$rec[0]["yahoo"]."</a></font></td>
  </tr>";

        if(@$rec[0]["sig"] != "") echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>signature</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".format_text(UPBcoding(filterLanguage($rec[0]["sig"], $_CONFIG["censor"])))."</font></td>
  </tr>";

        echo "  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>date registered</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".gmdate("Y-m-d", user_date($rec[0]["date_added"]))."</font></td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>total number of posts</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$rec[0]["posts"]."</font></td>
  </tr>";
        echo "</table>$skin_tablefooter";

        require_once('./includes/footer.php');
    }
} else {
    if(!($tdb->is_logged_in())) {
        echo "<html><head><meta http-equiv='refresh' content='2;URL=login.php?ref=profile.php'></head></html>";
        exit;
    } else {
        $rec = $tdb->get("users", $_COOKIE["id_env"]);
        require_once('./includes/header.php');
        @$rec[0]["sig"] = str_replace("<BR>", "\n", $rec[0]["sig"]);
        @$rec[0]["sig"] = str_replace("<br>", "\n", $rec[0]["sig"]);
        @$rec[0]["sig"] = str_replace("<Br>", "\n", $rec[0]["sig"]);
        @$rec[0]["sig"] = str_replace("<bR>", "\n", $rec[0]["sig"]);
        echo "<form action='$PHP_SELF' method='post'><center>";
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

        echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
  <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Edit Profile</font></b></td></tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>login</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>".$rec[0]["user_name"]."
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Old password</b><font size='$font_s' face='$font_face' color='$font_color_main'><br><i>Submit your old password only if you are changing your password</i></font></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='password' name='u_oldpass'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>New password</b></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='password' name='u_newpass'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><B>New password confirmation</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='password' name='u_newpass2'></font>
    </td>
  </tr>";

        if($_COOKIE["power_env"] >= 2) {
            echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>email</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_email' value='".$rec[0]["email"]."'>&nbsp;".$rec[0]["email"]."</font>
    </td>
  </tr>";
        } else {
            echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>email</b><br><font size='1' face='$font_face'>Email the Forum Administrator to change your email address.</a></font></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='hidden' name='u_email' value='".$rec[0]["email"]."'>&nbsp;".$rec[0]["email"]."</font>
    </td>
  </tr>";
        }

        if((bool) $rec[0]["view_email"]) $email_checked = "CHECKED";
        else $email_checked = "";
        if((bool) $rec[0]["mail_list"]) $mail_checked = "CHECKED";
        else $mail_checked = "";
        echo "<td bgcolor='$table1'>
<font size='$font_m' face='$font_face' color='$font_color_main'>
Make email address public in profile?&nbsp;&nbsp;&nbsp;
<a href=\"javascript: window.open('privacy.php','','status=no, width=800,height=50'); void('');\">
<!--<a href=\"privacy.php\" target=\"_blank\">-->
<font size='$font_s' face='$font_face'>
Privacy Policy</a></font></td>
<td bgcolor='$table1'><input type=checkbox name='show_email' value = '1' $email_checked></td>
</tr>

<tr>
<td bgcolor='$table1'>
<font size='$font_m' face='$font_face' color='$font_color_main'>
Add email to UPB discussion forums mailing list?</td>
<td bgcolor='$table1'><input type=checkbox name=email_list value='1' $mail_checked></td>
</tr>

  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>location</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_loca' value='".$rec[0]["location"]."'></font>
    </td>
  </tr>
";
 /*<tr>
    <td bgcolor='$table1' width=80% valign='top'><font size='$font_m' face='$font_face' color='$font_color_main'><b>avatar</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href=\"javascript: window.open('about_image.php','','status=no, width=400,height=300'); void('');\">
<font size='1' face='$font_face'>read this for avatar info!</a></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
<input type='text' name='avatar' value='".$rec[0]["avatar"]."'></font>&nbsp;&nbsp;&nbsp;";
        if(@$rec[0]["avatar"] != "") echo "<img src=\"".$rec[0]["avatar"]."\" border='0' width='".$rec[0]['avatar_width']."' height='".$rec[0]['avatar_height']."'><br>";

        echo "
    </td></tr>";
*/
//START RIPPER
echo "
<tr>
	

		<td bgcolor='$table1' width=80% valign='top'><font size='$font_m' face='$font_face' color='$font_color_main'>Current avatar<br>Select a new avatar";

		if(@$rec[0]["avatar"] != "") echo "<img src=\"".$rec[0]["avatar"]."\" border='0' width='".$rec[0]['avatar_width']."' height='".$rec[0]['avatar_height']."'><br />";
	else echo "<img src='images/avatars/noavatar.gif' alt='' title='' />";

		echo "</td>
		<td bgcolor='$table1' width=20% valign='top'><table cellspacing='0px' style='width:100%;'>
			<tr>
				<td style='text-align:center;width:50%;'>

<img src='images/avatars/blank.gif' name='myImage' alt='' title='' /></td>
<td>


<select size='5' name='avatar' onChange='swap(this.options[selectedIndex].value)'>";

function returnimages($dirname="images/avatars/") {
$pattern="\.(jpg|jpeg|png|gif|bmp)$";
$files = array();
$curimage=0;
if($handle = opendir($dirname)) {
	while(false !== ($file = readdir($handle))){
			if(eregi($pattern, $file)){
				echo "<option value ='images/avatars/".$file."'>".$file."</option>";
				$curimage++;
			}
	}

	closedir($handle);
}
return($files);
}

echo "" . "\n";
returnimages();

	echo "</select></td></tr></table></td>
	</tr>";//END RIPPER
  echo "<tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>homepage</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_site' value='".$rec[0]["url"]."'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>icq</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_icq' value='".$rec[0]["icq"]."'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>aim</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_aim' value='".$rec[0]["aim"]."'> </font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>Yahoo!</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_yahoo' value='".$rec[0]["yahoo"]."'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>msn</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type='text' name='u_msn' value='".$rec[0]["msn"]."'></font>
    </td>
  </tr>
  <tr>
    <td bgcolor='$table1' valign='top' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><B>signature</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <textarea name='u_sig' cols=45 rows=10>".$rec[0]["sig"]."</textarea>
    </font></td>
  </tr>
  <tr>
    <td bgcolor='$table1' valign='top' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><B>Timezone Setting</b></font></td>
    <td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
    <select name='u_timezone' id='u_timezone'>";
    $timezones = array();
		$timezones["-12"] = "(GMT -12:00) Eniwetok, Kwajalein";
$timezones["-11"] = "(GMT -11:00) Midway Island, Samoa";
$timezones["-10"] = "(GMT -10:00) Hawaii";
$timezones["-9"] = "(GMT -9:00) Alaska";
$timezones["-8"] = "(GMT -8:00) Pacific Time (US &amp; Canada)";
$timezones["-7"] = "(GMT -7:00) Mountain Time (US &amp; Canada)";
$timezones["-6"] = "(GMT -6:00) Central Time (US &amp; Canada), Mexico City";
$timezones["-5"] = "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima";
$timezones["-4"] = "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz";
$timezones["-3.5"] = "(GMT -3:30) Newfoundland";
$timezones["-3"] = "(GMT -3:00) Brazil, Buenos Aires, Georgetown";
$timezones["-2"] = "(GMT -2:00) Mid-Atlantic";
$timezones["-1"] = "(GMT -1:00 hour) Azores, Cape Verde Islands";
$timezones["0"] = "(GMT) Western Europe Time, London, Lisbon, Casablanca";
$timezones["1"] = "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris";
$timezones["2"] = "(GMT +2:00) Kaliningrad, South Africa";
$timezones["3"] = "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg";
$timezones["3.5"] = "(GMT +3:30) Tehran";
$timezones["4"] = "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi";
$timezones["4.5"] = "(GMT +4:30) Kabul";
$timezones["5"] = "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent";
$timezones["5.5"] = "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi";
$timezones["6"] = "(GMT +6:00) Almaty, Dhaka, Colombo";
$timezones["7"] = "(GMT +7:00) Bangkok, Hanoi, Jakarta";
$timezones["8"] = "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong";
$timezones["9"] = "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk";
$timezones["9.5"] = "(GMT +9:30) Adelaide, Darwin";
$timezones["10"] = "(GMT +10:00) Eastern Australia, Guam, Vladivostok";
$timezones["11"] = "(GMT +11:00) Magadan, Solomon Islands, New Caledonia";
$timezones["12"] = "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka";

      foreach ($timezones as $key => $value)
      {
        echo $key;
        echo "<option value='$key' ";
        if ($key == $rec[0]["timezone"])
          echo "selected";
        echo ">$value</option>";
      }
      echo "</select>";
    echo "</font></td>
  </tr>
  <tr>
    <td colspan=2 bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'>
      <input type=reset name='reset' value='Reset'>
      <input type='submit' name='u_edit' value='Submit'>
    </font></td>
  </tr>
</table>$skin_tablefooter
</form>";
        require_once('./includes/footer.php');
    }
}
?>
