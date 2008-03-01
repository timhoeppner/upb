<?php
// Ultimate PHP Board
// Author: PHP Outburst
// Website: http://www.myupb.com
// Version: 2.0

//Special Posting Functions
function message_icons()
{
  $tdb = new tdb(DB_DIR.'/', 'bbcode.tdb');
  $tdb->setFP("icons","icons");
  $icons = $tdb->query("icons","id>'0'");
  //var_dump($smilies);
  $output = "";
  $checked = "";
  foreach ($icons as $key => $icon)
  {
    if ($key == 0)
      $checked = 'checked';
    else
      $checked = "";
    $output .= "<input type='radio' name='icon' value=".$icon['filename']." $checked><img src='./icon/".$icon['filename']."' border='0'>&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($key%10 == 9)
      $output .= "<br>";
  }
  return $output;
}

function format_text($text) {
    $text = str_replace("\n", "<br>", $text);
    $text = str_replace("  ", "&nbsp; ", $text);
    $text = str_replace("&amp;#", "&#", $text);
    return $text;
}

function encode_text($text)
{
  $string = str_replace(array('<','>'),array('&lt;','&gt;'),$text);
  return $string;
}

function filterLanguage($text, $_CONFIG) {
    $msg = $text;
    //start bad words filter
    $words = explode(",", $_CONFIG['banned_words']);
    deleteWhiteIndex($words);
    for($pp=0;$pp<count($words);$pp++) {
        $words[$pp] = html_entity_decode($words[$pp]);
        $msg = preg_replace('/\b'.$words[$pp].'\b/i', $_CONFIG["censor"], $msg);
        //$msg = eregi_replace($words[$pp]." ", $censor." ", $msg);
    }
    //end bad words filter
    return $msg;
}

function UPBcoding($text) {

    $tdb = new tdb(DB_DIR.'/', 'bbcode.tdb');

    $tdb->setFP("smilies","smilies");
    $msg = $text;

    $smilies = array();
    //start emoticons
    $smilies = $tdb->query("smilies","id>'0'");

    foreach ($smilies as $smiley)
    {
      $msg = str_replace($smiley['bbcode'],$smiley['replace'],$msg);
    }

    //escape characters to prevent data injection
    //$msg = str_replace("&quot;", "\"", $msg);
    //$msg = str_replace("\"", "&quot;", $msg);

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
    $msg = preg_replace("/\[quote\](.*?)\[\/quote\]/si", "<blockquote><font size='1' face='tahoma'>Quote:</font><hr>\\1<br><hr></blockquote>", $msg);
    $msg = preg_replace("/\[quote=(.*?)\](.*?)\[\/quote\]/si", "<blockquote><font size='1' face='tahoma'>Quote: \\1</font><hr>\\2<br><hr></blockquote>", $msg);
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

function bbcodebuttons($txtarea='message',$type='post') {
    $bb_buttons = "<p>";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.colors,'colors','$txtarea')\" name='colors'>";
    $bb_buttons .= "<option value='' selected>Choose color</option>";
    $bb_buttons .= "<option value='#FFFFFF'>White</option>";
    $bb_buttons .= "<option value='#FFFF00'>Yellow</option>";
    $bb_buttons .= "<option value='#008000'>Green</option>";
    $bb_buttons .= "<option value='#800080'>Purple</option>";
    $bb_buttons .= "<option value='#FF0000'>Red</option>";
    $bb_buttons .= "<option value='#0000FF'>Blue</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.typeface,'typeface','$txtarea')\" name='typeface'>";
    $bb_buttons .= "<option value='' selected>Choose font</option>";
    $bb_buttons .= "<option value='arial'>Arial</option>";
    $bb_buttons .= "<option value='Times New Roman'>Times New Roman</option>";
    $bb_buttons .= "<option value='Helvetica'>Helvetica</option>";
    $bb_buttons .= "<option value='Garamond'>Garamond</option>";
    $bb_buttons .= "<option value='Courier'>Courier</option>";
    $bb_buttons .= "<option value='Verdana'>Verdana</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select onChange=\"bb_dropdown(this.form.size,'size','$txtarea')\" name='size'>";
    $bb_buttons .= "<option value='' selected>Choose font size</option>";
    $bb_buttons .= "<option value='6'>6px</option>";
    $bb_buttons .= "<option value='12'>12px</option>";
    $bb_buttons .= "<option value='18'>18px</option>";
    $bb_buttons .= "<option value='24'>24px</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<p>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[b]','[/b]','$txtarea')\"><img src='./images/bbcode/bold.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[i]','[/i]','$txtarea')\"><img src='./images/bbcode/italic.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[u]','[/u]','$txtarea')\"><img src='./images/bbcode/underline.gif' border='0'></a>";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[left]','[/left]','$txtarea')\"><img src='./images/bbcode/left.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[center]','[/center]','$txtarea')\"><img src='./images/bbcode/center.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[right]','[/right]','$txtarea')\"><img src='./images/bbcode/right.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[justify]','[/justify]','$txtarea')\"><img src='./images/bbcode/justify.gif' border='0'></a>";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_link('img','$txtarea')\"><img src='./images/bbcode/img.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('url','$txtarea')\"><img src='./images/bbcode/url.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('email','$txtarea')\"><img src='./images/bbcode/email.gif' border='0'></a> ";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_list('ul','$txtarea')\"><img src='./images/bbcode/ul.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_list('ol','$txtarea')\"><img src='./images/bbcode/ol.gif' border='0'></a> ";
    $bb_buttons .= "<img src='./images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[move]','[/move]','$txtarea')\"><img src='./images/bbcode/move.gif' border='0'></a> ";
    if ($type != 'sig')
    {
      $bb_buttons .= "<a href=\"javascript:createBBtag('[quote]','[/quote]','$txtarea')\"><img src='./images/bbcode/quote.gif' border='0'></a> ";
      $bb_buttons .= "<a href=\"javascript:createBBtag('[offtopic]','[/offtopic]','$txtarea')\"><img src='./images/bbcode/offtopic.gif' border='0'></a> ";
    }
  return $bb_buttons."<br>";
}

function getSmilies($field = 'message')
{
  $tdb = new tdb(DB_DIR.'/', 'bbcode.tdb');
  $tdb->setFP("smilies","smilies");
  $smilies = $tdb->query("smilies","id>'0'&&type='main'");
  //var_dump($smilies);
  foreach ($smilies as $key => $smiley)
  {
    $output .= "<A HREF=\"javascript:setsmilies(' ".$smiley['bbcode']." ','$field')\" ONFOCUS=\"filter:blur()\">".$smiley['replace']."</A>&nbsp;&nbsp;&nbsp;&nbsp;";
    if ($key%10 == 9)
      $output .= "<br>";
  }
  return $output;
}

function status($user)
{
global $_STATUS;
if ($user[0]["level"] == "1") {
				$status = "Member";
        $statuscolor = $_STATUS["userColor"];
				if ($user[0]["posts"] >= $_STATUS["member_post1"]) $status = $_STATUS["member_status1"];
				if ($user[0]["posts"] >= $_STATUS["member_post2"]) $status = $_STATUS["member_status2"];
				if ($user[0]["posts"] >= $_STATUS["member_post3"]) $status = $_STATUS["member_status3"];
				if ($user[0]["posts"] >= $_STATUS["member_post4"]) $status = $_STATUS["member_status4"];
				if ($user[0]["posts"] >= $_STATUS["member_post5"]) $status = $_STATUS["member_status5"];
			} elseif($user[0]["level"] == "2") {
				$statuscolor = $_STATUS["modColor"];
				if ($user[0]["posts"] >= $_STATUS["mod_post1"]) $status = $_STATUS["mod_status1"];
				if ($user[0]["posts"] >= $_STATUS["mod_post2"]) $status = $_STATUS["mod_status2"];
				if ($user[0]["posts"] >= $_STATUS["mod_post3"]) $status = $_STATUS["mod_status3"];
				if ($user[0]["posts"] >= $_STATUS["mod_post4"]) $status = $_STATUS["mod_status4"];
				if ($user[0]["posts"] >= $_STATUS["mod_post5"]) $status = $_STATUS["mod_status5"];
			} elseif($user[0]["level"] >= "3") {
				$statuscolor = $_STATUS["adminColor"];
				if ($user[0]["posts"] >= $_STATUS["admin_post1"]) $status = $_STATUS["admin_status1"];
				if ($user[0]["posts"] >= $_STATUS["admin_post2"]) $status = $_STATUS["admin_status2"];
				if ($user[0]["posts"] >= $_STATUS["admin_post3"]) $status = $_STATUS["admin_status3"];
				if ($user[0]["posts"] >= $_STATUS["admin_post4"]) $status = $_STATUS["admin_status4"];
				if ($user[0]["posts"] >= $_STATUS["admin_post5"]) $status = $_STATUS["admin_status5"];
			} else {
				$status = "Member";
				$statuscolor = $_STATUS["membercolor"];
			}
			$statconf = array('status' => $status,'statuscolor'=>$statuscolor);
      return $statconf;
}
?>
