<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once('./includes/class/func.class.php');
require_once('./includes/inc/post.inc.php');
require_once("./includes/class/upload.class.php");
$fRec = $tdb->get("forums", $_GET["id"]);

$posts_tdb = new functions(DB_DIR."/", "posts.tdb");
$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);

$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"];
if($_GET["t_id"] == "") {
    $where .= " New Topic";
} else {
    $tRec = $posts_tdb->get("topics", $_GET["t_id"]);
    $where .= " <a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Post Reply";
}

if (!isset($a)) $a = 0;
require_once('./includes/header.php');

if(!($tdb->is_logged_in())) {
    $_COOKIE["user_env"] = "guest";
    $_COOKIE["power_env"] = 0;
    $_COOKIE["id_env"] = 0;
}
if($_COOKIE["power_env"] < $fRec[0]["post"] && $_GET["t"] == 1 || $_COOKIE["power_env"] < $fRec[0]["reply"] && $_GET["t"] == 0) exitPage("You do not have enough Power to perform this action");

if(!($_GET["id"] != "" && is_numeric($_GET["id"]))) exitPage("Invalid Forum ID/Information");
if(!($_GET["t_id"] != "" && is_numeric($_GET["t_id"]) || $_GET["t"] != 0)) exitPage("Invalid Topic ID/Information");
if ($_POST["a"] == "1") {
    if(isset($_POST['subject'])) $_POST['subject'] = htmlentities(stripslashes($_POST["subject"]));
    $_POST['message'] = htmlentities(stripslashes($_POST["message"]));
    if($_POST["icon"] == "") exitPage("Please select a message icon");
    if($_GET["t"] == 1 && trim($_POST["subject"]) == "") exitPage("You must enter a subject!");
    if($_POST["message"] == "") exitPage("You must type in a message!");
    if($_GET["t"] != 1 && isset($_GET["t_id"]) && (bool) $tRec[0]["locked"]) exitPage("The topic is closed to further posting");

    //FILE UPLOAD BEGIN
    $uploadText = '';
    $uploadId = 0;
    if(trim($_FILES["file"]["name"]) != "") {
        $upload = new upload(DB_DIR, $_CONFIG["fileupload_size"]);
        $uploadId = $upload->storeFile($_FILES["file"]);

        if($uploadId === false) $uploadId = 0;

    }
    //END

    if($_GET["t"] == 1) {
        if(!isset($_POST["sticky"])) $_POST["sticky"] = "0";
        if(!isset($_POST["locked"])) $_POST["locked"] = "0";
        $_POST["subject"] = trim($_POST["subject"], $_CONFIG['stick_note']);
        if(trim($_POST["subject"]) == "")  exitPage("You must enter a subject!");
        
        $_GET["t_id"] = $posts_tdb->add("topics", array(
            "icon" => $_POST["icon"], 
            "subject" => $_POST["subject"], 
            "topic_starter" => $_COOKIE["user_env"], 
            "sticky" => $_POST["sticky"], 
            "replies" => "0", 
            "views" => "0", 
            "locked" => $_POST["locked"], 
            "last_post" => mkdate(), 
            "user_name" => $_COOKIE["user_env"], 
            "user_id" => $_COOKIE["id_env"]
        ));
        
        echo "Making new topic... ";

        $tdb->edit("forums", $_GET["id"], array("topics" => ((int)$fRec[0]["topics"] + 1), "posts" => ((int)$fRec[0]["posts"] + 1)));
        $redirect = "viewforum.php?id=".$_GET["id"];
        $pre = "";
    } else {
        echo "Adding Reply...";
        $tdb->edit("forums", $_GET["id"], array("posts" => ((int)$fRec[0]["posts"] + 1)));
        $rec = $posts_tdb->get("topics", $_GET["t_id"]);
        if(isset($_POST["unstick"])) $rec[0]["sticky"] = "0";

        if($rec[0]["monitor"] != "") {
            $local_dir = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
            $e_sbj = "New Reply in \"".$rec[0]["subject"]."\"";
            $e_msg = "You, or someone else using this e-mail address has requested to watch this topic: ".$rec[0]["subject"]." at ".$local_dir."/index.php\n\n".$_COOKIE["user_env"]." wrote:\n".$_POST["message"]."\n\n- - - - -\nSince this user has replied, you have been taken off the monitor list.  There may have been other users who have replied since then.  To read the rest of this topic, visit ".$local_dir."/viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."\nOr you can reply immediately if you forum cookies are valid by visiting ".$local_dir."/newpost.php?id=".$_GET["id"]."&t=0&t_id=".$_GET["t_id"]."&page=".$_GET["page"];
            $e_hed = "From: ".$_REGISTER["admin_email"]."\r\nReply-To: no-reply@".$_SERVER["server_name"]."\r\n";

            if(strpos($rec[0]["monitor"], ",") !== FALSE) $monitor = explode($rec[0]["monitor"], ",");
            else $monitor[0] = $rec[0]["monitor"];
            for($i=0;$i<count($monitor);$i++) {
                @mail($monitor[$i], $e_sbj, $e_msg, $e_hed);
            }
        }
        $posts_tdb->edit("topics", $_GET["t_id"], array("replies" => ((int)$rec[0]["replies"] + 1), "last_post" => mkdate(), "user_name" => $_COOKIE["user_env"], "sticky" => $rec[0]["sticky"], "user_id" => $_COOKIE["id_env"], "monitor" => ""));
        if($_GET["page"] == "")  $_GET["page"] = 1;
        $redirect = "viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"];
        $pre = $rec[0]["p_ids"].",";
    }
    clearstatcache();
    $posts_tdb->sortAndBuild("topics", "last_post", "DESC");
    clearstatcache();
    
    $p_id = $posts_tdb->add("posts", array(
        "icon" => $_POST["icon"], 
        "user_name" => $_COOKIE["user_env"], 
        "date" => mkdate(), 
        "message" => $uploadText.$_POST["message"], 
        "user_id" => $_COOKIE["id_env"], 
        "t_id" => $_GET["t_id"], 
        "upload_id" => $uploadId
    ));
    
    $posts_tdb->edit("topics", $_GET["t_id"], array("p_ids" => $pre.$p_id));
    //$tdb->setFp('rss', 'rssfeed');
    //if($fRec[0]['view'] == 0) $tdb->add('rss', array('subject' => ((isset($_POST['subject'])) ? $_POST['subject'] : 'RE: ' . $rec[0]['subject']), 'user_name' => $_COOKIE['user_env'], 'date' => mkdate(), 'message' => $_POST['message'], 'f_id' => $_GET['id'], 't_id' => $_GET['t_id']));

    if($_COOKIE["power_env"] != "0") {
        $user = $tdb->get("users", $_COOKIE["id_env"]);
        $tdb->edit("users", $_COOKIE["id_env"], array("posts" => ((int)$user[0]["posts"] + 1)));
    }
    redirect($redirect, 1);
} else {
    $message = "";
    if(!isset($_GET["page"])) $_GET["page"] = 1;
    if($_GET["t"] == 1) {
        $tpc = "<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Subject:</font></td><td bgcolor='$table1'><input type=text name=subject size=40></td></tr>";
        if($_COOKIE["power_env"] == 3) $sticky = "<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Sticky:</font></td><td bgcolor='$table1'><input type=checkbox name=sticky size=40 value=\"1\"></td></tr>";
        $hed = "New Topic";
        $iframe = "";
    } else {
        if($_GET['quote'] == 1) {
            $hed = "Reply Quote";
            $reply = $posts_tdb->get("posts", $_GET['p_id']);
            $message = "[quote]".$reply[0]["message"]."[/quote]";
        } else $hed = "Reply";
        $tpc = "";
        if($_COOKIE["power_env"] == 3) $sticky = "<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Un-Sticky:</font></td><td bgcolor='$table1'><input type=checkbox name=unstick size=40 value=\"1\"></td></tr>";
        $iframe = "<br><br><B><font size='$font_m' face='$font_face' color='$font_color_main'>Topic overview:<br></font></B>
                <IFRAME SRC='viewtopic_simple.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."' WIDTH=".$_CONFIG["table_width_main"]." HEIGHT='300'></IFRAME>";
    }
    $icons = message_icons();
    echo "<SCRIPT LANGUAGE='JavaScript'>
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

<form action='newpost.php?id=".$_GET["id"]."&t=".$_GET["t"]."&quote=".$_GET["quote"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."' method=POST name='newentry' onSubmit='submitonce(this)' enctype='multipart/form-data'>
  <input type='hidden' name='a' value='1'>";

    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='center'>
		<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>$hed</font></b></td></tr>
		<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>User Name:</font></td><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$_COOKIE["user_env"]."</td></tr>
        $tpc
        $sticky
		<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Message Icon:</font></td><td bgcolor='$table1'><input type=radio name=icon value='icon1.gif' CHECKED><img src='./icon/icon1.gif'> $icons</td></tr>
		<tr><td bgcolor='$table1' valign='top'><font size='$font_m' face='$font_face' color='$font_color_main'>Message:</font>
<br><br><br>
<center>
<table border=1><tr><td valign=top>";
    toolMapImage();
    echo "</tr></td></table></center>

		</td><td bgcolor='$table1'><textarea name=message cols=60 rows=18>".$message."</textarea>
        <br><br>
        <table border=1>
        <tr>
        <td valign=top>
    <table border='0' width='100%'><tr><td><font size='$font_m' face='$font_face' color='$font_color_main'>Smilies:</font></td><td align='right'><font size='$font_m' face='$font_face' color='$font_color_main'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></font></td></tr>
<tr><td colspan='2' bgcolor='$table1'>
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
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:SetSmiley('LOL')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/lol.gif BORDER=0 ALT=LOL></A>
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


</td></tr></table></tr></td>";
    if(!(($_CONFIG["fileupload_size"] == "0" || $_CONFIG["fileupload_size"] == "") && $_CONFIG["fileupload_location"] == "")) {
        echo "<tr><td bgcolor='$table1' colspan=2><font size='$font_m' face='$font_face' color='$font_color_main'>
		Attach File:<br>
<input type=file name='file' value='file_name' size=20><br><small><b>Valid file types: txt, gif, jpg, jpeg, zip.<br> Maximum file size is ".$_CONFIG["fileupload_size"]." Kb. If your file does not meet the requirements, the file will be rejected with no warning.</b></small></font></td></tr>";
    }
    echo "<tr><td bgcolor='$table1' colspan=2>
                <input type=submit value='Submit' onclick='return check_submit()'></td></tr></form></font>".$skin_tablefooter.$iframe;
}
require_once('./includes/footer.php');
?>