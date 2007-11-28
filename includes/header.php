<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

if(!headers_sent()) {
    switch (basename($_SERVER['PHP_SELF'])) {
        case 'register.php':
        case 'profile.php':
        case 'newpost.php':
        case 'newpm.php':
        header ("Cache-control: private");
        break;
        default:
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        break;
    }
}
if(!defined('DB_DIR')) die('The constant, DB_DIR has not been defined.  Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
if(!is_array($_CONFIG)) die('UPB Arrays have not been initialized.  Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
if($_CONFIG['skin_dir'] == '') die('SKIN_DIR not set ("'.SKIN_DIR.'").  This may be an indication that your config data was not set.');

$banned_addresses = file(DB_DIR.'/banneduser.dat');
foreach($banned_addresses as $address)
if(trim($address) == $HTTP_SERVER_VARS['REMOTE_ADDR']) {
    if(!headers_sent()) {
        setcookie("banned", "User is banned", time()+9999*99999*999999);
        header("location: http://www.catholicninjas.org/superfuntime/");
    }
    exit;
}
if(isset($_COOKIE["user_env"])) {
    $banned_addresses = file( DB_DIR.'/banneduser.dat' );
    foreach( $banned_addresses as $address )
    if(trim($address) == $_COOKIE["user_env"]) {
        if(!headers_sent()) {
            setcookie("banned", "User is banned", time()+9999*99999*999999);
            header("location: http://www.whitetrash.nl/pmf");
        }
        exit;
    }
}

if(isset($_COOKIE["banned"])) {
    if(!headers_sent()) header("location: http://www.whitetrash.nl/pmf");
    exit;
}

$mt = explode(' ', microtime());
$script_start_time = $mt[0] + $mt[1];

if($tdb->is_logged_in() && INSTALLATION_MODE === FALSE) {
    $refresh = false;
    if (!isset($_COOKIE["lastvisit"])) {
        $now = mkdate();
        $ses_info = lastvisit();
        if($ses_info == '') $ses_info = $now;
        
        if(!headers_sent()) {
            $uniquekey = generateUniqueKey();
            $tdb->edit('users', $_COOKIE['id_env'], array('uniquekey' => $uniquekey));
            //setcookie("thisvisit", $v_date);
            setcookie("lastvisit", $ses_info);
            setcookie("timezone", $_COOKIE["timezone"], (time() + (60 * 60 * 24 * 7)));
            if(isset($_COOKIE["remember"])) {
                setcookie("remember", 1, (time() + (60*60*24*7)));
                setcookie("user_env", $_COOKIE["user_env"], (time() + (60*60*24*7)));
                setcookie("uniquekey_env", $uniquekey, (time() + (60*60*24*7)));
                setcookie("power_env", $_COOKIE["power_env"], (time() + (60*60*24*7)));
                setcookie("id_env", $_COOKIE["id_env"], (time() + (60*60*24*7)));
            }
        }
        $refresh = true;
    }
    if($refresh && $_GET["a"] != 1 && $_POST["a"] != 1 && $_GET["s"] != 1 && $_POST["s"] != 1) redirect($_SERVER['PHP_SELF']."?".$QUERY_STRING, 0);
} else {
    if(!isset($_COOKIE["timezone"]) && !headers_sent()) setcookie("timezone", "0", (time() + (60*60*24*7)));
}
if(isset($_COOKIE['password_env'])) { setcookie('password_env', '', time() - 3600); redirect($_SERVER['PHP_SELF']."?".$QUERY_STRING, 0); }

$h_f = fopen(DB_DIR."/hits_today.dat", "r");
$hits = explode(":", fread($h_f, filesize(DB_DIR."/hits_today.dat")));
fclose($h_f);
$h_f = fopen(DB_DIR."/hits_record.dat", "r");
$hits_r = explode(":", fread($h_f, filesize(DB_DIR."/hits_record.dat")));
fclose($h_f);
$day = date("d");
if(date("d", $hits[0]) != $day) {
    //in place for debugging
    //echo "<font size=1>xxx</font>";
    $hits[0] = time();
    $hits[1] = 0;
}
$hits[1] += 1;
$hits_today = $hits[1];
if($hits_today > $hits_r[1]) {
    //New record
    $hits_r[0] = date("M j, Y");
    $hits_r[1] = $hits_today;
    $h_f = fopen(DB_DIR."/hits_record.dat", "w");
    fwrite($h_f, implode(":", $hits_r));
    fclose($h_f);
}
$hits_date = $hits_r[0];
$hits_record = $hits_r[1];
$h_f = fopen(DB_DIR."/hits_today.dat", "w");
flock($h_f, 2);
fwrite($h_f, implode(":", $hits));
flock($h_f, 3);
fclose($h_f);

if(!defined('SKIN_DIR')) die('The constant, SKIN_DIR has not been defined. Go to <a href="http://forum.myupb.com/" target="_blank">forum.myupb.com</a> for support.');
require_once $_CONFIG["skin_dir"]."/coding.php";

$login = "";
if (!$tdb->is_logged_in()) {
    $login = "You are not logged in. <a href='login.php?ref='>Login</a> or <a href='register.php'>Register</a>.";
    $loginlink = "login.php?ref=";
    $pm_display = "login.php?ref=pmsystem.php";
} else {
    $login = "Welcome, ".$_COOKIE["user_env"]."! <a href='logoff.php'>Log off?</a>";
    $loginlink = "logoff.php";
    $pm_display = "pmsystem.php";

    $f = fopen(DB_DIR."/new_pm.dat", 'r');
    fseek($f, (((int)$_COOKIE["id_env"] * 2) - 2));
    $new_pm = fread($f, 2);
    fclose($f);
    if((int)$new_pm != 0) $login .= "<Br><a href='pmsystem.php?section=inbox'><b>".$new_pm." new PMs in your inbox</b></a>";
    else $login .="<br>";

/*    $header_PrivMsg = new functions(DB_DIR, "privmsg.tdb");
    $header_PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));

    $header_pmRecs = $header_PrivMsg->query("CuBox", "box='inbox'&&to='".$_COOKIE["id_env"]."'&&date>'".$_COOKIE["lastvisit"]."'");
    $new_pm = count($header_pmRecs);

    if($header_pmRecs[0]["icon"] != "") $login .= "<Br><a href='pmsystem.php?section=inbox'><b>".count($header_pmRecs)." new PMs in your inbox</b></a>";
    else $login .= "<br>";
*/

    $login .= "<br><a href='setallread.php'>Mark all posts read</a>";
    if($_COOKIE["power_env"] == 3) $login .= "<br><a href='admin.php'>Admin Panel</a>";
}

//Start Header
echo "
<html>
<head>
<title>".(($where == '') ? $_CONFIG['title'] : (strip_tags(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where))))."</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link rel=\"stylesheet\" href=\"".$_CONFIG["skin_dir"]."/css/skin.css\" type=\"text/css\">
<script language='JavaScript'>
<!--
var counter=0;
function check_submit()
{
counter++;
if (counter>1)
{
alert('You cannot submit the form again! Please Wait.');
return false;
}
}
-->
</script>
<script>

/*
Form field Limiter script- By Dynamic Drive
For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
This credit MUST stay intact for use
*/

var ns6=document.getElementById&&!document.all

function restrictinput(maxlength,e,placeholder){
if (window.event&&event.srcElement.value.length>=maxlength)
return false
else if (e.target&&e.target==eval(placeholder)&&e.target.value.length>=maxlength){
var pressedkey=/[a-zA-Z0-9\.\,\/]/ //detect alphanumeric keys
if (pressedkey.test(String.fromCharCode(e.which)))
e.stopPropagation()
}
}

function countlimit(maxlength,e,placeholder){
var theform=eval(placeholder)
var lengthleft=maxlength-theform.value.length
var placeholderobj=document.all? document.all[placeholder] : document.getElementById(placeholder)
if (window.event||e.target&&e.target==eval(placeholder)){
if (lengthleft<0)
theform.value=theform.value.substring(0,maxlength)
placeholderobj.innerHTML=lengthleft
}
}


function displaylimit(theform,thelimit){
var limit_text='<b><span id=\"'+theform.toString()+'\">'+thelimit+'</span></b> characters remaining on your input limit'
if (document.all||ns6)
document.write(limit_text)
if (document.all){
eval(theform).onkeypress=function(){ return restrictinput(thelimit,event,theform)}
eval(theform).onkeyup=function(){ countlimit(thelimit,event,theform)}
}
else if (ns6){
document.body.addEventListener('keypress', function(event) { restrictinput(thelimit,event,theform) }, true);
document.body.addEventListener('keyup', function(event) { countlimit(thelimit,event,theform) }, true);
}
}

</script>
<script language=\"Javascript\" src=\"./includes/bbcode.js\"></script>
<script language=\"Javascript\" src=\"./includes/ajax.js\"></script>
<script language=\"Javascript\" src=\"./includes/scripts.js\"></script>
</head>

<body bgcolor='$bgcolor' text='$font_color_main' link='$link' alink='$alink' vlink='$vlink' leftmargin='0' topmargin='0' rightmargin='0'>

<div align='center'>
<table cellSpacing=1 cellPadding=0 width=100% border=1 bordercolor='#FFFFFF' bgcolor='#333333'>
    <tr valign='top'>
      <td>
        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td colspan='2' height='72'>
              <table width='100%' border='0' cellspacing='0' cellpadding='0' height='72'>
                <tr>
                  <td width='100%' background='".$_CONFIG["skin_dir"]."/images/head_top_left_bg.JPG' valign='middle'><img src='".$_CONFIG["skin_dir"]."/images/head_logo.JPG' border=0></td>
                  <td>
                    <div align='right'><a href='".$_CONFIG["homepage"]."'><img src='".$_CONFIG["skin_dir"]."/images/head_logo_right.JPG' border=0></a></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                  <td background='".$_CONFIG["skin_dir"]."/images/head_bottom_left_bg.JPG' valign='top' width='100%'>";
if($tdb->is_logged_in()) echo "<a href='profile.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_usercp.JPG' border='0'></a><a href='$pm_display'><img src='".$_CONFIG["skin_dir"]."/images/head_but_pms.JPG' border='0'><a href='showmembers.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_members.JPG' border='0'></a><a href='board_faq.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_faq.JPG'  border='0'></a><a href='search.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_search.JPG' border='0'></a><a href='$loginlink'><img src='".$_CONFIG["skin_dir"]."/images/head_but_loginout.JPG' border='0'></a>";
else echo "<a href='register.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_register.JPG' border='0'></a><a href='$pm_display'><img src='".$_CONFIG["skin_dir"]."/images/head_but_pms.JPG' border='0'><a href='login.php?ref=showmembers.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_members.JPG' border='0'></a><a href='board_faq.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_faq.JPG'  border='0'></a><a href='search.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_search.JPG' border='0'></a><a href='$loginlink'><img src='".$_CONFIG["skin_dir"]."/images/head_but_loginout.JPG' border='0'></a>";
                    //<a href='register.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_register.JPG' border='0'></a><a href='profile.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_usercp.JPG' border='0'></a><a href='$pm_display'><img src='".$_CONFIG["skin_dir"]."/images/head_but_pms.JPG' border='0'><a href='showmembers.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_members.JPG' border='0'></a><a href='board_faq.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_faq.JPG'  border='0'></a><a href='search.php'><img src='".$_CONFIG["skin_dir"]."/images/head_but_search.JPG' border='0'></a><a href='$loginlink'><img src='".$_CONFIG["skin_dir"]."/images/head_but_loginout.JPG' border='0'></a>
                    echo "</td>
                  <td background='".$_CONFIG["skin_dir"]."/images/head_bottom_right_bg.JPG' width='100%'></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

<table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=6 width='100%' bgcolor=$outsideborder border=0>
  <tbody>
  <tr>
    <td width='100%'>
      <center><table style='BORDER-COLLAPSE: collapse' bordercolor=#111111 cellspacing=0 cellpadding=0 width='90%' bgcolor=$insideborder border=0>
        <tbody>
        <tr>
          <td width='100%' valign='top' colspan=2><center><br>";
echoTableHeading("Welcome ".$_COOKIE["user_env"], $_CONFIG);
echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."' background='".$_CONFIG["skin_dir"]."/images/cat_top_bg.gif'><tr><td colspan='2' bgcolor='$alternatingcolor1'>
<table width='90%' border='0' cellspacing='0' cellpadding='4' align='center'>
  <tr>
    <td width='50%'><font size='$font_m' face='$font_face' color='$font_color_main'>
      <a href='index.php'>".$_CONFIG["title"]."</a>";
if(isset($where)) echo " ".$_CONFIG["where_sep"]." ".$where;
echo "<br>";

if(file_exists($soundfile)) {
    if (!isset($sound)) echo "Sound is off, Turn it <a href='sound.php' alt='May or may not work in netscape'>on</a>?";
    else echo "Sound is on, Turn it <a href='sound.php' alt='May or may not work in netscape'>off</a>?";
}
echo "</font></td>
    <td width='50%' align='right'> <font size='$font_m' face='$font_face' color='$font_color_main'>$login</font></td>
  </tr>
</table></tr></td></table>$skin_tablefooter</center><center>
<font face=$font_face size=$font_s>";
//End Header

//login information
if(!$tdb->is_logged_in() && isset($_COOKIE['user_env']) && isset($_COOKIE['uniquekey_env']) && isset($_COOKIE['id_env'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    echo "<p><b>Alert</b>: You or another person logged in on a different computer since the last time you've visited.  <a href=\"logoff.php?ref={$redirect}\">Don't show this message anymore</a> or <a href=\"login.php?ref={$redirect}\">Login</a>.</p>";
}
//begining INSTALLATION MODE
if(INSTALLATION_MODE === TRUE && (FALSE === eregi('admin', $_SERVER['PHP_SELF'])) && (FALSE === strpos($_SERVER['PHP_SELF'], 'install')) && (FALSE === strpos($_SERVER['PHP_SELF'], 'update')) && (FALSE === strpos($_SERVER['PHP_SELF'], 'upgrade'))) {
    echo 'The forum is in installation mode. Cannot continue.';
    if($tdb->is_logged_in() && $_COOKIE['power_env'] === 3) echo 'You may access the <a href="admin.php">Admin Panel</a> to switch INSTALLATION_MODE off.';
    require('./includes/footer.php');
    exit;
}
if($_GET['SHOW'] == 'COOKIES') {
    print '</center><pre>';
    foreach($GLOBALS["_COOKIE"] as $varname => $varvalue) {
        print $varname."\t= ".$varvalue."\n";
    }
    print '</pre><center>';
//echo "\$user_env = ".$_COOKIE["user_env"]."<br>";
//if(isset($_COOKIE['pass_env'])) echo "\$pass_env = ".$_COOKIE['pass_env']."<br>";
//if(isset($_COOKIE['password_env'])) echo "\$password_env = ".$_COOKIE["password_env"]."<br>strlen(\$password_env):".strlen($_COOKIE["password_env"])."<br>";
//echo "\$uniquekey_env = ".$_COOKIE["uniquekey_env"]."<br>\$power_env = ".$_COOKIE["power_env"]."<br>\$id_env = ".$_COOKIE["id_env"]."<br><br>";
//echo "\$remember = ".$_COOKIE['remember']."<br>";
//echo "\$lastvisit = ".gmdate("M d, Y g:i:s a", $_COOKIE["lastvisit"])." (".$_COOKIE["lastvisit"].")<br><br>";
}
?>
