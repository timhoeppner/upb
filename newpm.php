<?php
// Private Messaging System
// Add on to Ultimate PHP Board V2.0
// Original PM Version (before _MANUAL_ upgrades): 2.0
// Addon Created by J. Moore aka Rebles
// Using textdb Version: 4.4.2

require_once("./includes/class/func.class.php");
require_once("./includes/inc/post.inc.php");
$where = "<a href='pmsystem.php'>Private Msg</a> ".$_CONFIG["where_sep"]." Send a PM";
if($tdb->is_logged_in() === false) exitPage("You are not even Logged in.");

$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));

if($_GET["action"] == "ClearOutBox") {
    require_once("./includes/header.php");
    $recs = $PrivMsg->query("CuBox", "box='outbox'&&from='".$_COOKIE["id_env"]."'", 1);
    $recs = array_reverse($recs);
    $c_outbox_recs = count($recs); //extra one for the pm just added to the outbox.
    if($c_outbox_recs > $_CONFIG["pm_max_outbox_msg"] && $recs[0]["id"] != "") {
        for($i=($_CONFIG["pm_max_outbox_msg"]);$i<($c_outbox_recs);$i++) {
            $PrivMsg->delete("CuBox", $recs[$i]["id"], false);
        }
        $PrivMsg->reBuild("CuBox");
    }

    echo "PM Sucessfully Sent!";
    require_once("./includes/footer.php");
    if($_GET["ref"] != "" && $_GET["section"] != "" && $_GET["r"] != "") redirect($_POST["ref"]."?section=".$_GET["section"]."&id=".$_GET["r"], "2");
    else redirect("pmsystem.php", "2");
    exit;
} elseif($_POST["s"] == 1) {
    $_POST['subject'] = htmlentities(stripslashes($_POST['subject']));
    $_POST['message'] = htmlentities(stripslashes($_POST['message']));
    $error_msg = "";
    if(!isset($_POST["icon"])) {
        $error_msg .= "Must be submitted through the form<br>";
    }
    if(chop($_POST["message"]) == "") {
        $error_msg .= "You must provide a message<br>";
    }
    if($_POST["to"] == "" || $_POST["to"] == "0") {
        $error_msg .= "Select a Username<br>";
    } elseif($_POST["to"] == $_COOKIE["id_env"]) {
        $error_msg .= "You cannot send yourself a Private Message";
    } else {
        $ids = getUsersPMBlockedList($_COOKIE["user_env"]);
        if(true === array_search($_COOKIE["id_env"], $ids)) {
            $error_msg .= "The User you are sending does not wish to recieve messages from you. (You are blocked)<br>";
        }
    }
    if($error_msg == "") {
        $to_info = $tdb->get("users", $_POST["to"]);
        $PrivMsg->setFp("ToBox", ceil($_POST["to"]/120));
        if($_POST["icon"] == "") $_POST["icon"] = "icon1.gif";
        if(trim($_POST["subject"]) == "") $_POST["subject"] = "No Subject";
        if(isset($_POST["del"]) && isset($_POST["r"])) $PrivMsg->delete("CuBox", $_POST["r"]);

        $PrivMsg->add("ToBox", array("box" => "inbox",  "from" => $_COOKIE["id_env"], "to" => $_POST["to"], "icon" => $_POST["icon"], "subject" => $_POST["subject"], "date" => mkdate(), "message" => chop($_POST["message"])));
        $PrivMsg->add("CuBox", array("box" => "outbox", "from" => $_COOKIE["id_env"], "to" => $_POST["to"], "icon" => $_POST["icon"], "subject" => $_POST["subject"], "date" => mkdate(), "message" => chop($_POST["message"])));

        $f = fopen(DB_DIR."/new_pm.dat", 'r+');
        fseek($f, (((int)$_POST["to"] * 2) - 2));
        $new_pm = trim(fread($f, 2));
        (int)$new_pm++;
        if(strlen($new_pm) == 3) $new_pm = 99;
        elseif(strlen($new_pm) == 1) $new_pm = " ".$new_pm;
        fseek($f, (((int)$_POST["to"] * 2) - 2));
        fwrite($f, $new_pm);
        fclose($f);

        redirect("newpm.php?action=ClearOutBox&ref=".$_POST["ref"]."&section=".$_POST["section"]."&r=".$_POST["r"], '2');
        exit;
    } else {
        if($_POST["r"] != "") $_GET["r_id"] = $_POST["r"];
        $sbj = $subject;
        $msg = $message;
    }
}
require_once('./includes/header.php');
if($error_msg != "") echo $error_msg;
if(isset($_GET["r_id"]) && is_numeric($_GET["r_id"])) {
    $reply = $PrivMsg->get("CuBox", $_GET["r_id"]);
    $u_reply = $tdb->get("users", $reply[0]["from"]);
    $send_to = $u_reply[0]['user_name']."<input type='hidden' name='to' value='".$reply[0]["from"]."'>";
    if(!isset($sbj)) {
        while(substr($reply[subject], 0, 4) == "RE: ") {
            $reply[0]["subject"] = substr($reply[0]["subject"], 5);
        }
        $sbj = "RE: ".$reply[0]["subject"];
    }
    $hed = "Reply";
    $iframe = "<br><br><B><font size='$font_m' face='$font_face' color='$font_color_main'>".$u_reply[0]["user_name"]." PM to you:<br></font></B>
        <IFRAME SRC='viewpm_simple.php?id=".$_GET["r_id"]."' WIDTH='".$_CONFIG["table_width_main"]."' HEIGHT='300'></IFRAME>";
} else {
    if(!isset($_GET['to'])) exitPage('You must click on "send pm" from a user\'s profile or post.');
    if(!is_numeric($_GET['to'])) exitPage('Invalid User.');
    $send_to = $tdb->get('users', $_GET['to']);
    $send_to = $send_to[0]['user_name'].'<input type="hidden" name="to" value="'.$_GET['to'].'">';
    $hed = "New Topic";
    $iframe = "";
}

$icons = message_icons();
echo "

<SCRIPT LANGUAGE='JavaScript'>
	<!--
	function SetSmiley(Which) {
  	if (document.newentry.message.createTextRange) {
  		document.newentry.message.focus();
  		document.selection.createRange().duplicate().text = Which;
   	} else {
  		document.newentry.message.value += Which;
   	}
  }
	
	
	//-->
	</SCRIPT>
	
<script language='JavaScript'>
function submitonce(theform){
if (document.all||document.getElementById){
for (i=0;i<theform.length;i++){
var tempobj=theform.elements[i]
if(tempobj.type.toLowerCase()=='submit'||tempobj.type.toLowerCase()=='reset')
tempobj.disabled=true
}
}
}
</script>

<SCRIPT LANGUAGE=\"JavaScript\"><!--
function openChild(file,window) {
    childWindow=open(file,window,'resizable=no,width=400,height=200');
    if (childWindow.opener == null) childWindow.opener = self;
    }
//--></SCRIPT>



    <form action='".$_SERVER['PHP_SELF'].(isset($_GET['to']) ? "?to=".$_GET['to'] : '')."' method='POST' name='newentry' onSubmit='submitonce(this)' enctype='multipart/form-data'><input type='hidden' name='s' value='1'><input type='hidden' name='r' value='".$_GET["r_id"]."'>";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='center'>
    <tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>$hed</font></b></td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>From:</font></td><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$_COOKIE["user_env"]."</td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Send to:</font></td><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$send_to."</td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Subject:</font></td><td bgcolor='$table1'><input type='text' name='subject' size='40' value='".$sbj."'></td></tr>
    <tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Message Icon:</font></td><td bgcolor='$table1'><input type='radio' name='icon' value='icon1.gif' CHECKED><img src='./icon/icon1.gif'> $icons</td></tr>
    <tr><td bgcolor='$table1' valign='top'><font size='$font_m' face='$font_face' color='$font_color_main'>Message:</font><p>
    
<center>		
<table border=1><tr><td valign=top>";
toolMapImage();
echo "</tr></td></table></center>

    </td><td bgcolor='$table1'><textarea name='message' cols='60' rows='18'>".$msg."</textarea>
    
        <br><br>
        <table border=1>
        <tr>
        <td valign=top>
        <font size='$font_m' face='$font_face' color='$font_color_main'>
        Smilies:<br>
<A HREF=\"javascript:SetSmiley(':)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/smile.gif BORDER=0 ALT=:)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley(':(')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/frown.gif BORDER=0 ALT=:(></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley(';)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wink.gif BORDER=0 ALT=;)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley(':P')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/tongue.gif BORDER=0 ALT=:P></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley(':o')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/eek.gif BORDER=0 ALT=:o></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley(':D')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/biggrin.gif BORDER=0 ALT=:D></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(C)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/cool.gif BORDER=0 ALT=(C)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(M)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/mad.gif BORDER=0 ALT=(M)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(R)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/redface.gif BORDER=0 ALT=(R)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(E)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rolleyes.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;<br>

<A HREF=\"javascript:SetSmiley('(offtopic)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/offtopic.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(rofl)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rofl.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(confused)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/confused.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(crazy)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/crazy.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(hm)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hm.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(hmmlaugh)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hmmlaugh.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(blink)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/blink.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(wallbash)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wallbash.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('(noteeth)')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/noteeth.gif BORDER=0 ALT=(E)></A></tr></td></table>
&nbsp;&nbsp;&nbsp;&nbsp;

<br><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a>

</td></tr><tr><td colspan='2' bgcolor='$table1'>
    <input type=submit value='Submit' onclick='check_submit()'></td>
    </tr></table>$skin_tablefooter</form></font><br><br>$iframe</p>";
require_once("./includes/footer.php");
?>
