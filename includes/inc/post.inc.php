<?php
	// Ultimate PHP Board
	// Author: PHP Outburst
	// Website: http://www.myupb.com
	// Version: 2.0
	//Special Posting Functions
	function message_icons() {
		$dir = opendir("./icon");
		$r = "";
		while ($file = readdir($dir)) {
			$str = strpos($file, "con", 0);
			if ($str == "1") {
				$fileext = explode(".", $file);
				$fileext = end($fileext);
				if ($fileext == "gif" or $fileext == "jpg") {
					if ($file != "icon1.gif") {
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
		for($pp = 0; $pp < count($words); $pp++) {
			$msg = eregi_replace(" ".$words[$pp], " ".$censor, $msg);
			$msg = eregi_replace($words[$pp]." ", $censor." ", $msg);
		}
		//end bad words filter
		return $msg;
	}
	function UPBcoding($text) {
		$msg = $text;
		//start emoticons
		$msg = str_replace(":)", "<img src='smilies/smile.gif' alt='' title='' />", $msg);
		$msg = str_replace(":(", "<img src='smilies/frown.gif' alt='' title='' />", $msg);
		$msg = str_replace("&quot;", "\"", $msg);
		$msg = str_replace(";)", "<img src='smilies/wink.gif' alt='' title='' />", $msg);
		$msg = str_replace("\"", "&quot;", $msg);
		$msg = str_replace(":P", "<img src='smilies/tongue.gif' alt='' title='' />", $msg);
		$msg = str_replace(":o", "<img src='smilies/eek.gif' alt='' title='' />", $msg);
		$msg = str_replace(":D", "<img src='smilies/biggrin.gif' alt='' title='' />", $msg);
		$msg = str_replace("(C)", "<img src='smilies/cool.gif' alt='' title='' />", $msg);
		$msg = str_replace("(M)", "<img src='smilies/mad.gif' alt='' title='' />", $msg);
		$msg = str_replace("(confused)", "<img src='smilies/confused.gif' alt='' title='' />", $msg);
		$msg = str_replace("(crazy)", "<img src='smilies/crazy.gif' alt='' title='' />", $msg);
		$msg = str_replace("(hm)", "<img src='smilies/hm.gif' alt='' title='' />", $msg);
		$msg = str_replace("(hmmlaugh)", "<img src='smilies/hmmlaugh.gif' alt='' title='' />", $msg);
		$msg = str_replace("(offtopic)", "<img src='smilies/offtopic.gif' alt='' title='' />", $msg);
		$msg = str_replace("(blink)", "<img src='smilies/blink.gif' alt='' title='' />", $msg);
		$msg = str_replace("(rofl)", "<img src='smilies/rofl.gif' alt='' title='' />", $msg);
		$msg = str_replace("(R)", "<img src='smilies/redface.gif' alt='' title='' />", $msg);
		$msg = str_replace("(E)", "<img src='smilies/rolleyes.gif' alt='' title='' />", $msg);
		$msg = str_replace("(wallbash)", "<img src='smilies/wallbash.gif' alt='' title='' />", $msg);
		$msg = str_replace("(noteeth)", "<img src='smilies/noteeth.gif' alt='' title='' />", $msg);
		$msg = str_replace("LOL", "<img src='smilies/lol.gif' alt='' title='' />", $msg);
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
		echo "<img src='skins/default/images/font_buttons.gif' width='100' height='125' usemap='#tool_image_map' alt='' title='' />
			<map name='tool_image_map'>
			<area shape='rect' coords='4,53,24,73' href=\"javascript:SetSmiley('[img] [/img]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='4,79,48,99' href=\"javascript:SetSmiley('[move] [/move]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='29,54,73,74' href=\"javascript:SetSmiley('[quote] [/quote]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='4,29,25,49' href=\"javascript:SetSmiley('[small] [/small]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='75,5,97,25' href=\"javascript:SetSmiley('[center] [/center]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='83,107,94,117' href=\"javascript:SetSmiley('[white] [/white]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='67,107,78,117' href=\"javascript:SetSmiley('[yellow] [/yellow]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='52,107,61,116' href=\"javascript:SetSmiley('[green] [/green]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='36,107,46,117' href=\"javascript:SetSmiley('[purple] [/purple]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='6,106,16,117' href=\"javascript:SetSmiley('[red] [/red]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='21,107,30,116' href=\"javascript:SetSmiley('[blue] [/blue]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='28,5,48,23' href=\"javascript:SetSmiley('[i] [/i]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='3,4,25,24' href=\"javascript:SetSmiley('[b] [/b]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='52,4,73,24' href=\"javascript:SetSmiley('[u] [/u]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='28,28,72,49' href=\"javascript:SetSmiley('[url] [/url]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='75,28,97,49' href=\"javascript:SetSmiley('[email] [/email]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='75,54,97,74' href=\"javascript:SetSmiley('[bullet] [/bullet]')\" ONFOCUS=\"filter:blur()\">
			<area shape='rect' coords='52,79,96,99' href=\"javascript:SetSmiley('[offtopic] [/offtopic]')\" ONFOCUS=\"filter:blur()\">
			</map>";
	}
?>