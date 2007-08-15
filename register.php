<?
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

// Ultimate PHP Board Register
require_once('./includes/class/func.class.php');
$where = "Register";
if($tdb->is_logged_in()) exitPage('You\'re already logged in.', true);
if(empty($_POST["show_email"])) $_POST["show_email"] = "";
if(empty($_POST["email_list"])) $_POST["email_list"] = "";
if(!isset($_POST["submit"])) $_POST["submit"] = "";

require_once('./includes/inc/encode.inc.php');
session_start();
if($_POST["submit"] == "Submit") {
    if($_POST['s_key'] !== $_SESSION["u_keycheck"]) {
        exitPage("Please enter the secrity code <b>exactly</b> as it appears...", true);
    }
    $_SESSION = array();
    setcookie(session_name(), '', time()-42000, '/');
    session_destroy();

    $_POST["u_login"] = strip_tags($_POST["u_login"]);
    $_POST["u_login"] = trim($_POST["u_login"]);

    if($_POST["u_login"] == '' || $_POST["u_email"] == '') exitPage("You did not fill in all required fields! (*)", true);

    $q = $tdb->query("users", "user_name='".$_POST["u_login"]."'", 1, 1);
    if($_POST["u_login"] == $q[0]["user_name"]) exitPage("The username you chose is already in use!", true);
    unset($q);

    $q = $tdb->query("users", "email='".$_POST["u_email"]."'", 1, 1);
    if($_POST["u_email"] == $q[0]["email"]) exitPage("The email address you chose is already in use!", true);
    unset($q);

    $length = "3";
    $vowels = array("a", "e", "i", "o", "u");
    $cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");

    $num_vowels = count($vowels);
    $num_cons = count($cons);

    for($i = 0; $i < $length; $i++) {
        $u_pass .= $cons[rand(0, $num_cons - 1)].$vowels[rand(0, $num_vowels - 1)];
    }

    if($_POST["show_email"] != "1") $_POST["show_email"] = 0;
    if(strlen($_POST["u_sig"]) > 200) exitPage("You cannot have more than 200 characters in your signature.", true);
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["u_email"])) exitPage("please enter a valid email!", true);

    $email = explode("@", $_POST["u_email"]);
    if(!checkdnsrr($email[1], "MX")) exitPage("Please enter a valid email! No mail server seems to exist at <b>www.".$email[1]."</b>", true);
    if(substr(trim(strtolower($_POST["u_site"])), 0, 7) != "http://") $_POST["u_site"] = "http://" . $_POST["u_site"];

    if($_POST["timezone"]{0} == '+') $_POST["timezone"] = substr($_POST["timezone"], 1);

    $id = $tdb->add("users", array("user_name" => $_POST["u_login"], "password" => generateHash($u_pass), "level" => 1, "email" => $_POST["u_email"], "view_email" => $_POST["show_email"], "mail_list" => $_POST["email_list"], "location" => $_POST["u_loca"], "url" => $_POST["u_site"], "avatar" => $_POST["avatar"], "icq" => $_POST["u_icq"], "aim" => $_POST["u_aim"], "yahoo" => $_POST["u_yahoo"], "msn" => $_POST["u_msn"], "sig" => chop($_POST["u_sig"]), "posts" => 0, "date_added" => mkdate(), "timezone" => $_POST["u_timezone"]));

    // If each user sends and receives one PM a day, their table will last 67.2 years
    $temp_tdb = new tdb(DB_DIR."/", "privmsg.tdb");
    $pmT_num = ceil($id / 100);
    if(FALSE === $temp_tdb->isTable($pmT_num)) $temp_tdb->createTable($pmT_num, array(array("box", "string", 6), array("from", "number", 7), array("to", "number", 7), array("icon", "string", 10), array("subject", "memo"), array("date", "number", 14), array("message", "memo"), array("id", "id")));
    $temp_tdb->cleanup();
    unset($temp_tdb);

    $f = fopen(DB_DIR."/lastvisit.dat", 'a');
    fwrite($f, str_repeat(' ', 14));
    fclose($f);
    $f = fopen(DB_DIR."/new_pm.dat", 'a');
    fwrite($f, " 0");
    fclose($f);
    $register_msg = str_replace("<login>", $_POST['u_login'], $_REGISTER["register_msg"]);
    $register_msg = str_replace("<password>", $u_pass, $register_msg);
    if(!@mail($_POST["u_email"], $register_sbj, $register_msg, "From: ".$_REGISTER["admin_email"])) error_log ("Unable to send register email conformation to user: ".$_POST["u_login"], 3, "./logs/error.log");

    require_once('./includes/header.php');
    print "You are now registered!<BR><BR>An email has been sent to your email account with a random password, <br>which you can change at any time. It should arrive within 2 - 5 minutes. <br><br>Thank you for registering!";
    require_once('./includes/footer.php');
    redirect("login.php", "5");
    exit;
} else {
    require_once('./includes/header.php');
    // security mod
    $string = md5(rand(0, microtime()*1000000));
    $verify_string = substr($string, 3, 7);
    $key = md5(rand(0,999));
    $encid = urlencode(md5_encrypt($verify_string, $key));

    // rather than the hidden field we have
    $_SESSION['u_keycheck'] = $verify_string;

    echo "<form action='register.php' method=POST>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
        <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Register <font size='$font_s'>(note: required fields are marked with a <span style='color:red'>*</span>)</font></font></b></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><span style='color:red'>*</span> User Name:<br><font size='$font_s'>Used for logging in and is your identity throughout the forum</font></font></td><td bgcolor='$table1' width=10%><input type=text name='u_login' size=40></td></tr>
        <tr>
        <td bgcolor='$table1'>
        <font size='$font_m' face='$font_face' color='$font_color_main'><span style='color:red'>*</span> E-mail Address:<br>
        <font size='$font_s'>Must be a valid email address (you@host.com). A random Password is sent to the email address that you provide. If you do not provide a valid email address, you will not be able to log in.</font></font></td>
        <td bgcolor='$table1'><input type=text name=u_email size=40></td></tr>
        
        <tr>
        <td bgcolor='$table1'>
        <font size='$font_m' face='$font_face' color='$font_color_main'>
        Make email address public in profile?&nbsp;&nbsp;&nbsp;
        <a href=\"javascript: window.open('privacy.php','','status=no, width=800,height=50'); void('');\">
        <!--<a href=\"privacy.php\" target=\"_blank\">-->
        <font size='1' face='$font_face'>
        Privacy Policy</a></font></td>
        <td bgcolor='$table1'><input type=checkbox name=show_email value='1'></td>
        </tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><span style='color:red'>*</span> Confirm E-mail Address:<br><font size='$font_s'>Must be a valid email address (you@host.com)</font></font></td><td bgcolor='$table1'><input type=text name=u_email size=40></td></tr>
        <tr><td rowspan='2' bgcolor='$table1'><span style='color:red'>*</span> <font size='$font_m' face='$font_face' color='$font_color_main'>Security Code<br><font size='$font_s'>Please enter the code in the image: (all lower case) <a href='register.php'>Load new image</a></font></font></td>
        <td bgcolor='$table1'><img src='./includes/image.php?id=$encid&key=$key'></td></tr><tr><td bgcolor='$table1'><input type=text name=s_key maxlength=7 size=12></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Location:<br><font size='$font_s'>Where are you from? (it can be anything)</font></font></td><td bgcolor='$table1'><input type=text name=u_loca size=40></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Website URL:<br><font size='$font_s'>please include the http:// in front of url</font></font></td><td bgcolor='$table1'><input type=text name=u_site size=40></td></tr>
        <tr>
        <td bgcolor='$table1'>
        <font size='$font_m' face='$font_face' color='$font_color_main'>Avatar URL:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href=\"javascript: window.open('about_image.php','','status=no, width=400,height=300'); void('');\">
        <font size='1' face='$font_face'>read this for avatar info!</a><br>
        <font size='$font_s'>Please leave the http:// infront of url</font></font></td>
        <td bgcolor='$table1'><input type=text name=avatar size=40 value=\"images/avatars/noavatar.gif\"></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>ICQ:<br><font size='$font_s'>If you have ICQ put your number here</font></font></td><td bgcolor='$table1'><input type=text name=u_icq size=40></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>AIM:<br><font size='$font_s'>If you have AOL Instant messanger, please type your SN (optional)</font></font></td><td bgcolor='$table1'><input type=text name=u_aim size=40></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Yahoo!:<br><font size='$font_s'>If you have Yahoo! messanger, please type your SN (optional)</font></font></td><td bgcolor='$table1'><input type=text name=u_yahoo size=40></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>MSN:<br><font size='$font_s'>If you have MSN Instant messanger please type your SN (optional)</font></font></td><td bgcolor='$table1'><input type=text name=u_msn size=40></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Signature:<br><font size='$font_s'>Your signature is appended to each of your messages</font></font></td><td bgcolor='$table1'><textarea name=u_sig cols=45 rows=10></textarea></td></tr>
        <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>timezone setting<br><font size='$font_s' face='$font_face' color='$font_color_main'>Set to how many hours you are from GMT.<br>Example: The North American Pacific Coast is \"-8\", whereas the city of Rome is just \"1\", or for London, just \"0\"</font></font></td>
            <td bgcolor='$table1'><input type='text' name='u_timezone' value='0'></td></tr>"; 

    echo "<tr><td bgcolor='$table1' colspan=2><input type=submit name=submit value='Submit'></td></tr>
        </table>$skin_tablefooter</form>";

    require_once('./includes/footer.php');
    if(empty($_COOKIE["user_env"])) $user = "guest";
    else $user = $_COOKIE["user_env"];

    $month = date("m",time());
    $year = date("Y",time());
    if ($HTTP_SERVER_VARS['REMOTE_HOST'] == "") $visitor_info = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    else $visitor_info = $HTTP_SERVER_VARS['REMOTE_HOST'];

    $base = "http://" . $HTTP_SERVER_VARS['SERVER_NAME'] . $HTTP_SERVER_VARS['PHP_SELF'];
    $x1= "host {$HTTP_SERVER_VARS['REMOTE_ADDR']} |grep Name";
    $x2= $HTTP_SERVER_VARS['REMOTE_ADDR'];
    $fp = fopen(DB_DIR."/iplog", "a");
    $date= "$month $year";
    fputs($fp, "$visitor_info -{$HTTP_SERVER_VARS['HTTP_USER_AGENT']} - $user - <br>Date/Time: $date $x1:$base:--------------------------------Next Person<p><br>\r\n");fclose($fp);
}
?>