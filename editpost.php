<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3

require_once("./includes/class/func.class.php");
require_once("./includes/class/posts.class.php");

$posts_tdb = new posts(DB_DIR, "posts.tdb");

$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);
//$fRec = $tdb->get("forums", $_GET["id"]);
//$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
$pRec = $posts_tdb->get("posts", $_GET["p_id"]);

$where = "Edit Post";
//$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"]." <a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Edit Post";
require_once("./includes/header.php");

if(!(isset($_GET["id"]) && isset($_GET["t_id"]) && isset($_GET["p_id"]))) exitPage("Not enough information to perform this function.");
if(!($tdb->is_logged_in())) exitPage("You are not logged in, therefore unable to perform this action.");

if($pRec[0]["user_id"] != $_COOKIE["id_env"] && $_COOKIE["power_env"] < 2) exitPage("You are not authorized to edit this post.");

if(isset($_POST["message"])) {
    $posts_tdb->edit("posts", $_GET["p_id"], array("message" => htmlentities(stripslashes($_POST["message"])), "edited_by_id" => $_COOKIE["id_env"], "edited_by" => $_COOKIE["user_env"], "edited_date" => mkdate()));
    echo "Successfully edited post, redirecting...";
    require_once("./includes/footer.php");
    redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], 2);
    exit;
} else {
    echo "
    <SCRIPT>
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
    
    <form action='editpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$_GET["p_id"]."' METHOD=POST name='newentry'>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width=".$_CONFIG["table_width_main"]." cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='center'>";
    //<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Edit Post</font></b></td></tr>
    echo "<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Message:</font></b></td></tr>
<tr><td bgcolor='$table1' valign='top'>
    
    <br><br><br><br><center>";		
    //<table border=1><tr><td valign=top>
toolMapImage();
    //</tr></td></table>
    echo "</center>
            
    </td><td bgcolor='$table1'><textarea id=\"message\" name=\"message\" cols=\"60\" rows=\"18\">".$pRec[0]["message"]."</textarea>
    
    <br><br>
        <table cellspacing=1 cellpadding=3 border=0 bgcolor='$border' align='left'>
    <tr>
    <td valign='top' bgcolor='$table1' >
    <table border='0' width='100%'><tr><td><font size='$font_m' face='$font_face' color='$font_color_main'>Smilies:</font></td><td align='right'><font size='$font_m' face='$font_face' color='$font_color_main'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></font></td></tr>
<tr><td colspan='2' bgcolor='$table1'>
<font size='$font_m' face='$font_face' color='$font_color_main'>
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
        <IMG SRC=smilies/noteeth.gif BORDER=0 ALT=(E)></A>

</td></tr></table></td></tr></table>
            
            <tr><td bgcolor='$table1' colspan=2><input type=submit value='Edit'></td></tr>
            </table>$skin_tablefooter
            </form>";
        }
require_once("./includes/footer.php");
?>
