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
        $msg = eregi_replace(" ".$words[$pp]." ", " ".$censor." ", $msg);
        //$msg = eregi_replace($words[$pp]." ", $censor." ", $msg);
    }
    //end bad words filter
    return $msg;
}

function UPBcoding($text) {
    $msg = $text;
    //start emoticons
    $msg = str_replace(" :)", " <img src='smilies/smile.gif'> ", $msg);
    $msg = str_replace(" :(", " <img src='smilies/frown.gif'> ", $msg);
    $msg = str_replace("&quot;", "\"", $msg);
    $msg = str_replace(" ;)", " <img src='smilies/wink.gif'> ", $msg);
    $msg = str_replace("\"", "&quot;", $msg);
    $msg = str_replace(" :P", " <img src='smilies/tongue.gif'> ", $msg);
    $msg = str_replace(" :o", " <img src='smilies/eek.gif'> ", $msg);
    $msg = str_replace(" :D", " <img src='smilies/biggrin.gif'> ", $msg);
    $msg = str_replace(" (C)", " <img src='smilies/cool.gif'> ", $msg);
    $msg = str_replace(" (M)", " <img src='smilies/mad.gif'> ", $msg);
    $msg = str_replace(" (confused)", " <img src='smilies/confused.gif'> ", $msg);
    $msg = str_replace(" (crazy)", " <img src='smilies/crazy.gif'> ", $msg);
    $msg = str_replace(" (hm)", " <img src='smilies/hm.gif'> ", $msg);
    $msg = str_replace(" (hmmlaugh)", " <img src='smilies/hmmlaugh.gif'> ", $msg);
    $msg = str_replace(" (offtopic)", " <img src='smilies/offtopic.gif'> ", $msg);
    $msg = str_replace(" (blink)", " <img src='smilies/blink.gif'> ", $msg);
    $msg = str_replace(" (rofl)", " <img src='smilies/rofl.gif'> ", $msg);
    $msg = str_replace(" (R)", " <img src='smilies/redface.gif'> ", $msg);
    $msg = str_replace(" (E)", " <img src='smilies/rolleyes.gif'> ", $msg);
    $msg = str_replace(" (wallbash)", " <img src='smilies/wallbash.gif'> ", $msg);
    $msg = str_replace(" (noteeth)", " <img src='smilies/noteeth.gif'> ", $msg);
    $msg = str_replace(" LOL", " <img src='smilies/lol.gif'> ", $msg);
    //end emoticons

    //bullet points
    $msg = str_replace("[ul]", "<ul>", $msg);
    $msg = str_replace("[/ul]", "</ul>", $msg);
    $msg = str_replace("[ol]", "<ol>", $msg);
    $msg = str_replace("[/ol]", "</ol>", $msg);
    $msg = str_replace("[*]", "<li>", $msg);
    //$msg = str_replace("[/*]", "</li>", $msg);

    //start script for delete.php
    $msg = str_replace("(emailadmin)", "<a href='email.php?id=$adminid' target='_blank'><img src='images/email.gif' border='0'></a>", $msg);
    //end script for delete.php
    //start upb code
    $msg = preg_replace("/\[center\](.*?)\[\/center\]/si", "<div align='center'>\\1</div>", $msg);
    $msg = preg_replace("/\[left\](.*?)\[\/left\]/si", "<div align='left'>\\1</div>", $msg);
    $msg = preg_replace("/\[right\](.*?)\[\/right\]/si", "<div align='right'>\\1</div>", $msg);
    $msg = preg_replace("/\[justify\](.*?)\[\/justify\]/si", "<div align='justify'>\\1</div>", $msg);
    $msg = preg_replace("/\[move\](.*?)\[\/move\]/si", "<marquee>\\1</marquee>", $msg);
    $msg = preg_replace("/\[color=(.*?)\](.*?)\[\/color\]/si", "<span style='color:\\1;'>\\2</span>", $msg);
    $msg = preg_replace("/\[font=(.*?)\](.*?)\[\/font\]/si", "<span style='font-family:\\1;'>\\2</span>", $msg);
    $msg = preg_replace("/\[size=(.*?)\](.*?)\[\/size\]/si", "<span style='font-size:\\1px;'>\\2</span>", $msg);
    $msg = preg_replace("/\[b\](.*?)\[\/b\]/si", "<span style='font-weight:bold;'>\\1</span>", $msg);
    $msg = preg_replace("/\[u\](.*?)\[\/u\]/si", "<span style='text-decoration:underline;'>\\1</span>", $msg);
    $msg = preg_replace("/\[i\](.*?)\[\/i\]/si", "<span style='font-style: italic;'>\\1</span>", $msg);
    $msg = preg_replace("/\[url\](http:\/\/)?(.*?)\[\/url\]/si", "<a href=\"\\1\\2\" target='_blank'>\\2</a>", $msg);
    $msg = preg_replace("/\[url=(http:\/\/)?(.*?)\](.*?)\[\/url\]/si", "<a href=\"\\1\\2\" target='_blank'>\\3</a>", $msg);
    $msg = preg_replace("/\[email\](.*?)\[\/email\]/si", "<a href=\"mailto:\\1\">\\1</a>", $msg);
    $msg = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/si", "<a href=\"mailto:\\1\">\\2</a>", $msg);
    $msg = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"\\1\" border=\"0\">", $msg);
    $msg = preg_replace("/\[offtopic\](.*?)\[\/offtopic\]/si", "<font color='blue' size='$font_s' face='$font_face'>Offtopic: \\1</font>", $msg);
    $msg = preg_replace("/\[small\](.*?)\[\/small\]/si", "<small>\\1</small>", $msg);
    $msg = preg_replace("/\[quote\](.*?)\[\/quote\]/si", "<blockquote><font size='1' face='tahoma'>quote:</font><hr>\\1<br><hr></blockquote>", $msg);
    $msg = preg_replace("/\[code\](.*?)\[\/code\]/si", "<font color='red'>Code:<hr><pre>\\1<hr></pre></font>", $msg);
    
    while (true)
    {
      $tmp_msg = $msg;
      $search = array('#<span(\s[^>]*)><span(\s[^>]*)>#i','#</span></span>#i');
      $replace = array('<span\\1\\2>','</span>');
      $msg = preg_replace($search, $replace, $msg);
      $msg = preg_replace("/style='(.*?)\' style='(.*?)\'/si","style='\\1\\2'",$msg); 
      if ($msg == $tmp_msg)
        break;
    }
    return $msg;
    //end upb code
}

function bbcodebuttons() {
    $bb_buttons = "<p>";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.colors,'colors')\" name='colors'>";
    $bb_buttons .= "<option value='' selected>Choose color</option>";
    $bb_buttons .= "<option value='white'>White</option>";
    $bb_buttons .= "<option value='yellow'>Yellow</option>";
    $bb_buttons .= "<option value='green'>Green</option>";
    $bb_buttons .= "<option value='purple'>Purple</option>";
    $bb_buttons .= "<option value='red'>Red</option>";
    $bb_buttons .= "<option value='blue'>Blue</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.typeface,'typeface')\" name='typeface'>";
    $bb_buttons .= "<option value='' selected>Choose font</option>";
    $bb_buttons .= "<option value='arial'>Arial</option>";
    $bb_buttons .= "<option value='Times New Roman'>Times New Roman</option>";
    $bb_buttons .= "<option value='Helvetica'>Helvetica</option>";
    $bb_buttons .= "<option value='Garamond'>Garamond</option>";
    $bb_buttons .= "<option value='Courier'>Courier</option>";
    $bb_buttons .= "<option value='Verdana'>Verdana</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.size,'size')\" name='size'>";
    $bb_buttons .= "<option value='' selected>Choose font size</option>";
    $bb_buttons .= "<option value='6'>6px</option>";
    $bb_buttons .= "<option value='12'>12px</option>";
    $bb_buttons .= "<option value='18'>18px</option>";
    $bb_buttons .= "<option value='24'>24px</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<p>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[b]','[/b]','message')\"><img src='./images/bbcode/bold.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[i]','[/i]','message')\"><img src='./images/bbcode/italic.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[u]','[/u]','message')\"><img src='./images/bbcode/underline.gif' border='0'></a>";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[left]','[/left]','message')\"><img src='./images/bbcode/left.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[center]','[/center]','message')\"><img src='./images/bbcode/center.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[right]','[/right]','message')\"><img src='./images/bbcode/right.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[justify]','[/justify]','message')\"><img src='./images/bbcode/justify.gif' border='0'></a>";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_link('img','message')\"><img src='./images/bbcode/img.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('url','message')\"><img src='./images/bbcode/url.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('email','message')\"><img src='./images/bbcode/email.gif' border='0'></a> ";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_list('ul','message')\"><img src='./images/bbcode/ul.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_list('ol','message')\"><img src='./images/bbcode/ol.gif' border='0'></a> ";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[move]','[/move]','message')\"><img src='./images/bbcode/move.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[quote]','[/quote]','message')\"><img src='./images/bbcode/quote.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[offtopic]','[/offtopic]','message')\"><img src='./images/bbcode/offtopic.gif' border='0'></a> ";
  return $bb_buttons."<br>";
}

function getSmilies()
{
$smilies = "<A HREF=\"javascript:setsmilies(' :) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/smile.gif BORDER=0 ALT=:)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' :( ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/frown.gif BORDER=0 ALT=:(></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' ;) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wink.gif BORDER=0 ALT=;)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' :P ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/tongue.gif BORDER=0 ALT=:P></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' :o ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/eek.gif BORDER=0 ALT=:o></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' :D ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/biggrin.gif BORDER=0 ALT=:D></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (C) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/cool.gif BORDER=0 ALT=(C)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (M) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/mad.gif BORDER=0 ALT=(M)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (R) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/redface.gif BORDER=0 ALT=(R)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (E) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rolleyes.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' LOL ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/lol.gif BORDER=0 ALT=LOL></A>
&nbsp;&nbsp;&nbsp;&nbsp;<br>

<A HREF=\"javascript:setsmilies(' (offtopic) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/offtopic.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (rofl) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/rofl.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (confused) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/confused.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (crazy) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/crazy.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (hm) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hm.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (hmmlaugh) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/hmmlaugh.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (blink) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/blink.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (wallbash) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/wallbash.gif BORDER=0 ALT=(E)></A>
&nbsp;&nbsp;&nbsp;&nbsp;
<A HREF=\"javascript:setsmilies(' (noteeth) ','message')\" ONFOCUS=\"filter:blur()\">
        <IMG SRC=smilies/noteeth.gif BORDER=0 ALT=(E)></A>";
return $smilies;
}
?>
