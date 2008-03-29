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
  $output .= "<table><tr>";
  foreach ($icons as $key => $icon)
  {
    if ($key == 0)
      $checked = 'checked';
    else
      $checked = "";
    $output .= "<td><input type='radio' name='icon' value=".$icon['filename']." $checked><img src='".SKIN_DIR."/icons/post_icons/".$icon['filename']."' border='0'></td>";
    if ($key%12 == 11)
      $output .= "</tr><tr>";
  }
  $output .= "</tr></table>";
  return $output;
}

function format_text($text) {
    $text = str_replace("\n", "<br>", $text);
    $text = str_replace("  ", "&nbsp; ", $text);
    $text = str_replace("&amp;#", "&#", $text);
    return $text;
}

/* cOULD POTENTIALLY BREAK SETTING COOKIES AND SESSIONS */?><?php //<? added to allow for syntax highlighting in editors
function encode_text($text)
{
  $string = str_replace(array('<','>'),array('&lt;','&gt;'),$text);
  return $string;
}

function filterLanguage($text) {
    $_CONFIG = &$GLOBALS['_CONFIG'];
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
    if ($_CONFIG['email_mode'])
      $msg = str_replace("(emailadmin)", "<a href='email.php?id=1' target='_blank'><img src='images/email.gif' border='0'></a>", $msg); //$adminid undefined, changed back to "1"
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
    $msg = preg_replace("/\[offtopic\](.*?)\[\/offtopic\]/si", "<font color='blue'>Offtopic: \\1</font>", $msg);
    $msg = preg_replace("/\[small\](.*?)\[\/small\]/si", "<small>\\1</small>", $msg);

    while (preg_match("/\[quote(.*?)\](.*?)\[\/quote\]/si", $msg))
    {
      $msg = preg_replace_callback("/\[quote(.*?)\](.*?)\[\/quote\]/si",'parse_quote',$msg);
    }
    $msg = preg_replace("/\[code\](.*?)\[\/code\]/si", "<font color='red'>Code:<hr><pre>\\1<hr></pre></font>", $msg);

    //loop to combine multiple span tags into a single span tag
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

//finds the correct type of quote and converts to correct quote output
function parse_quote($matches)
{
  $explode = explode(";",$matches[1]);
  $author = substr($explode[0],1,strlen($explode[0]));
  if (count($explode) == 1)
    $msg = "<blockquote><font size='1' face='tahoma'>Quote: $author</font><hr>".$matches[2]."<br><hr></blockquote>";
  else
  {
    $msg = "<blockquote><font size='1' face='tahoma'>Quote: <a href='viewtopic.php?id=".$_GET['id']."&t_id=".$_GET['t_id'];
    if (array_key_exists('page',$_GET))
      $msg .= "&page=".$_GET['page'];
    $msg .= "#".$explode[1]."'>$author at ".gmdate("M d, Y g:i:s a", user_date($explode[2]))."</a></font><hr>".$matches[2]."<br><hr></blockquote>";
  }
  return $msg;
}

function bbcodebuttons($txtarea='message',$type='post') {
    if (!isset($_COOKIE['javascript']))
      return "Please enable Javascript to use text formatting and smilies<p>";

    $bb_buttons = "<p>";
    $bb_buttons .= "<select class='bbselect' onChange=\"bb_dropdown(this.form.colors,'colors','$txtarea')\" name='colors'>";
    $bb_buttons .= "<option value='' selected>Choose color</option>";
    $bb_buttons .= "<option style='color: #ffffff;' value='#FFFFFF'>White</option>";
    $bb_buttons .= "<option style='color: #ffff00;' value='#FFFF00'>Yellow</option>";
    $bb_buttons .= "<option style='color: #008000;' value='#008000'>Green</option>";
    $bb_buttons .= "<option style='color: #800080;' value='#800080'>Purple</option>";
    $bb_buttons .= "<option style='color: #ff0000;' value='#FF0000'>Red</option>";
    $bb_buttons .= "<option style='color: #0000ff;' value='#0000FF'>Blue</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select class='bbselect' onChange=\"bb_dropdown(this.form.typeface,'typeface','$txtarea')\" name='typeface'>";
    $bb_buttons .= "<option value='' selected>Choose font</option>";
    $bb_buttons .= "<option style='font-family : Arial;' value='arial'>Arial</option>";
    $bb_buttons .= "<option style='font-family : Times New Roman;' value='Times New Roman'>Times New Roman</option>";
    $bb_buttons .= "<option style='font-family : Helvetica;' value='Helvetica'>Helvetica</option>";
    $bb_buttons .= "<option style='font-family : Garamond;' value='Garamond'>Garamond</option>";
    $bb_buttons .= "<option style='font-family : Courier;' value='Courier'>Courier</option>";
    $bb_buttons .= "<option style='font-family : Verdana;' value='Verdana'>Verdana</option>";
    $bb_buttons .= "<option style='font-family : Comic Sans MS;' value='Comic Sans MS'>Comic Sans MS</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<select class='bbselect' onChange=\"bb_dropdown(this.form.size,'size','$txtarea')\" name='size'>";
    $bb_buttons .= "<option value='' selected>Choose font size</option>";
    $bb_buttons .= "<option style='font-size : 8px;' value='8'>8px</option>";
    $bb_buttons .= "<option style='font-size : 12px;' value='12'>12px</option>";
    $bb_buttons .= "<option style='font-size : 18px;' value='18'>18px</option>";
    $bb_buttons .= "<option style='font-size : 24px;' value='24'>24px</option>";
    $bb_buttons .= "</select> ";
    $bb_buttons .= "<p>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[b]','[/b]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/bold.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[i]','[/i]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/italic.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[u]','[/u]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/underline.gif' border='0'></a>";
    $bb_buttons .= "<img src='".SKIN_DIR."/images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[left]','[/left]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/left.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[center]','[/center]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/center.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[right]','[/right]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/right.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[justify]','[/justify]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/justify.gif' border='0'></a>";
    $bb_buttons .= "<img src='".SKIN_DIR."/images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_link('img','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/img.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('url','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/url.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_link('email','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/email.gif' border='0'></a>";
    $bb_buttons .= "<img src='".SKIN_DIR."/images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:add_list('ul','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/ul.gif' border='0'></a> ";
    $bb_buttons .= "<a href=\"javascript:add_list('ol','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/ol.gif' border='0'></a>";
    $bb_buttons .= "<img src='".SKIN_DIR."/images/bbcode/separator.gif' border='0'>";
    $bb_buttons .= "<a href=\"javascript:createBBtag('[move]','[/move]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/move.gif' border='0'></a> ";
    if ($type != 'sig')
    {
      $bb_buttons .= "<a href=\"javascript:createBBtag('[quote]','[/quote]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/quote.gif' border='0'></a> ";
      $bb_buttons .= "<a href=\"javascript:createBBtag('[offtopic]','[/offtopic]','$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/offtopic.gif' border='0'></a> ";
    }
    $bb_buttons .= "<a href=\"javascript:removeBBcode('$txtarea')\"><img src='".SKIN_DIR."/images/bbcode/removeformat.gif' border='0'></a>";
  return $bb_buttons."<br>";
}

function getSmilies($field = 'message')
{
  if (!isset($_COOKIE['javascript']))
    return "";
  $tdb = new tdb(DB_DIR.'/', 'bbcode.tdb');
  $tdb->setFP("smilies","smilies");
  $smilies = $tdb->query("smilies","id>'0'&&type='main'");
  //var_dump($smilies);
  $output = "<table class='smilie_tb'><tr>";
  foreach ($smilies as $key => $smiley)
  {
    $output .= "<td class='smilie'><A HREF=\"javascript:setsmilies(' ".$smiley['bbcode']." ','$field')\" ONFOCUS=\"filter:blur()\">".$smiley['replace']."</A></td>";
    if ($key%10 == 9)
      $output .= "</tr><tr>";
  }
  $output .= "</tr><tr><td colspan='10' class='more_smilie'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></td></tr></table>";
  return $output;
}

function username_status($username)
{
  $tdb = new tdb(DB_DIR.'/', 'main.tdb');
  $tdb->setFP("users","members");
  $user = $tdb->basicQuery('users','user_name',$username);
  $status_config = status($user);
	$statuscolor = $status_config['statuscolor'];
  return $statuscolor;
}

function status($user)
{
$_STATUS = $GLOBALS['_STATUS'];
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
			} elseif($user[0]["level"] >= 3) {
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
