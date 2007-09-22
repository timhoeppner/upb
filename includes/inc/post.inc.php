<?php
// Ultimate PHP Board
// Author: PHP Outburst
// Website: http://www.myupb.com
// Version: 2.0

//Special Posting Functions

function message_icons() {
    $dir = opendir("./icon");
    $r = "";
    while($file = readdir($dir)) {
        $str = strpos($file, "con", 0);
        if ($str == "1") {
            $fileext = explode(".", $file);
            $fileext = end($fileext);
            if ($fileext == "gif" or $fileext =="jpg") {
                if($file != "icon1.gif"){
                $r .= "<input type=radio name=icon value='$file'><img src='./icon/$file'>";
                }
            }
        }
    }
    return $r;
}

function format_text($text) {
    $text = str_replace("\n", "<br>", $text);
    $text = str_replace("  ", "&nbsp; ", $text);
    return $text;
}

function filterLanguage($text, $censor) {
    $msg = $text;
    //start bad words filter
    $words = file(DB_DIR."/badwords.dat");
    deleteWhiteIndex($words);
    for($pp=0;$pp<count($words);$pp++) {
        $msg = eregi_replace(" ".$words[$pp], " ".$censor, $msg);
        $msg = eregi_replace($words[$pp]." ", $censor." ", $msg);
    }
    //end bad words filter
    return $msg;
}

function UPBcoding($text) {
    $msg = $text;
    //start emoticons
    $msg = str_replace(":)", "<img src='smilies/smile.gif'>", $msg);
    $msg = str_replace(":(", "<img src='smilies/frown.gif'>", $msg);
    $msg = str_replace("&quot;", "\"", $msg);
    $msg = str_replace(";)", "<img src='smilies/wink.gif'>", $msg);
    $msg = str_replace("\"", "&quot;", $msg);
    $msg = str_replace(":P", "<img src='smilies/tongue.gif'>", $msg);
    $msg = str_replace(":o", "<img src='smilies/eek.gif'>", $msg);
    $msg = str_replace(":D", "<img src='smilies/biggrin.gif'>", $msg);
    $msg = str_replace("(C)", "<img src='smilies/cool.gif'>", $msg);
    $msg = str_replace("(M)", "<img src='smilies/mad.gif'>", $msg);
    $msg = str_replace("(confused)", "<img src='smilies/confused.gif'>", $msg);
    $msg = str_replace("(crazy)", "<img src='smilies/crazy.gif'>", $msg);
    $msg = str_replace("(hm)", "<img src='smilies/hm.gif'>", $msg);
    $msg = str_replace("(hmmlaugh)", "<img src='smilies/hmmlaugh.gif'>", $msg);
    $msg = str_replace("(offtopic)", "<img src='smilies/offtopic.gif'>", $msg);
    $msg = str_replace("(blink)", "<img src='smilies/blink.gif'>", $msg);
    $msg = str_replace("(rofl)", "<img src='smilies/rofl.gif'>", $msg);
    $msg = str_replace("(R)", "<img src='smilies/redface.gif'>", $msg);
    $msg = str_replace("(E)", "<img src='smilies/rolleyes.gif'>", $msg);
    $msg = str_replace("(wallbash)", "<img src='smilies/wallbash.gif'>", $msg);
    $msg = str_replace("(noteeth)", "<img src='smilies/noteeth.gif'>", $msg);
    $msg = str_replace("LOL", "<img src='smilies/lol.gif'>", $msg);
    //end emoticons

    //bullet points
    $msg = str_replace("[list]", "<ul>", $msg);
    $msg = str_replace("[/list]", "</ul>", $msg);
    $msg = str_replace("[bullet]", "<li>", $msg);
    $msg = str_replace("[/bullet]", "</li>", $msg);

    //start script for delete.php
    $msg = str_replace("(emailadmin)", "<a href='email.php?id=$adminid' target='_blank'><img src='images/email.gif' border='0'></a>", $msg);
    //end script for delete.php
    //start upb code
    $msg = preg_replace("/\[center\](.*?)\[\/center\]/si", "<center>\\1</center>", $msg);
    $msg = preg_replace("/\[move\](.*?)\[\/move\]/si", "<marquee>\\1</marquee>", $msg);
    $msg = preg_replace("/\[white\](.*?)\[\/white\]/si", "<font color='white'>\\1</font>", $msg);
    $msg = preg_replace("/\[yellow\](.*?)\[\/yellow\]/si", "<font color='yellow'>\\1</font>", $msg);
    $msg = preg_replace("/\[green\](.*?)\[\/green\]/si", "<font color='lime'>\\1</font>", $msg);
    $msg = preg_replace("/\[purple\](.*?)\[\/purple\]/si", "<font color='fuchsia'>\\1</font>", $msg);
    $msg = preg_replace("/\[blue\](.*?)\[\/blue\]/si", "<font color='blue'>\\1</font>", $msg);
    $msg = preg_replace("/\[red\](.*?)\[\/red\]/si", "<font color='red'>\\1</font>", $msg);
    $msg = preg_replace("/\[b\](.*?)\[\/b\]/si", "<b>\\1</b>", $msg);
    $msg = preg_replace("/\[u\](.*?)\[\/u\]/si", "<u>\\1</u>", $msg);
    $msg = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>\\1</i>", $msg);
    $msg = preg_replace("/\[url\](http:\/\/)?(.*?)\[\/url\]/si", "<a href=\"\\1\\2\" target='_blank'>\\2</a>", $msg);
    $msg = preg_replace("/\[url=(http:\/\/)?(.*?)\](.*?)\[\/url\]/si", "<a href=\"\\1\\2\" target='_blank'>\\3</a>", $msg);
    $msg = preg_replace("/\[email\](.*?)\[\/email\]/si", "<a href=\"mailto:\\1\">\\1</a>", $msg);
    $msg = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/si", "<a href=\"mailto:\\1\">\\2</a>", $msg);
    $msg = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"\\1\" border=\"0\">", $msg);
    $msg = preg_replace("/\[offtopic\](.*?)\[\/offtopic\]/si", "<font color='blue' size='$font_s' face='$font_face'>Offtopic: \\1</font>", $msg);
    $msg = preg_replace("/\[small\](.*?)\[\/small\]/si", "<small>\\1</small>", $msg);
    $msg = preg_replace("/\[quote\](.*?)\[\/quote\]/si", "<blockquote><font size='1' face='tahoma'>quote:</font><hr>\\1<br><hr></blockquote>", $msg);
    $msg = preg_replace("/\[code\](.*?)\[\/code\]/si", "<font color='red'>Code:<hr><pre>\\1<hr></pre></font>", $msg);
    //end upb code
    return $msg;
}

function toolMapImage() {
    echo "<img src='images/font_buttons.gif' width='100' height='125' border='0' usemap='#tool_image_map'>
	<map name='tool_image_map'>
	<area shape='rect' coords='4,53,24,73' href=\"javascript:createBBtag('[img]','[/img]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='4,79,48,99' href=\"javascript:createBBtag('[move]','[/move]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='29,54,73,74' href=\"javascript:createBBtag('[quote]','[/quote]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='4,29,25,49' href=\"javascript:createBBtag('[small]','[/small]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='75,5,97,25' href=\"javascript:createBBtag('[center]','[/center]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='83,107,94,117' href=\"javascript:createBBtag('[white]','[/white]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='67,107,78,117' href=\"javascript:createBBtag('[yellow]','[/yellow]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='52,107,61,116' href=\"javascript:createBBtag('[green]','[/green]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='36,107,46,117' href=\"javascript:createBBtag('[purple]','[/purple]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='6,106,16,117' href=\"javascript:createBBtag('[red]','[/red]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='21,107,30,116' href=\"javascript:createBBtag('[blue]','[/blue]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='28,5,48,23' href=\"javascript:createBBtag('[i]','[/i]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='3,4,25,24' href=\"javascript:createBBtag('[b]','[/b]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='52,4,73,24' href=\"javascript:createBBtag('[u]','[/u]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='28,28,72,49' href=\"javascript:createBBtag('[url]','[/url]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='75,28,97,49' href=\"javascript:createBBtag('[email]','[/email]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='75,54,97,74' href=\"javascript:createBBtag('[bullet]','[/bullet]','message')\" ONFOCUS=\"filter:blur()\">
	<area shape='rect' coords='52,79,96,99' href=\"javascript:createBBtag('[offtopic]','[/offtopic]','message')\" ONFOCUS=\"filter:blur()\">
        </map>";
}

function getSmilies()
{
$smilies = "<A HREF=\"javascript:setsmilies(':)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/smile.gif BORDER=0 ALT=:)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(':(','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/frown.gif BORDER=0 ALT=:(></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(';)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wink.gif BORDER=0 ALT=;)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(':P','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/tongue.gif BORDER=0 ALT=:P></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(':o','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/eek.gif BORDER=0 ALT=:o></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(':D','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/biggrin.gif BORDER=0 ALT=:D></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(C)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/cool.gif BORDER=0 ALT=(C)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(M)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/mad.gif BORDER=0 ALT=(M)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(R)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/redface.gif BORDER=0 ALT=(R)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(E)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rolleyes.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('LOL','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/lol.gif BORDER=0 ALT=LOL></A>
&nbsp;&nbsp;&nbsp;&nbsp;<br>

<A HREF=\"javascript:setsmilies('(offtopic)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/offtopic.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(rofl)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rofl.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(confused)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/confused.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(crazy)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/crazy.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(hm)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hm.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(hmmlaugh)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hmmlaugh.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(blink)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/blink.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(wallbash)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wallbash.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies('(noteeth)','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/noteeth.gif BORDER=0 ALT=(E)></A>";
return $smilies;
}
?>
