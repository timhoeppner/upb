<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

/*
forum list - main.tdb :: forums (forum, cat, view, des, topics, posts, mod, id)
topic lists - posts.tdb :: [FORUM_ID]_topics (icon, subject, topic_starter, sticky, replies, locked, last_post, user_name, user_id, p_ids, id)
posts - posts.tdb :: [FORUM_ID] (icon, user_name, date, message, user_id, t_id, id)
*/

require_once('./includes/class/func.class.php');
$where = "Search";
require_once('./includes/header.php');
$posts_tdb = new functions(DB_DIR.'/', "posts.tdb");

$sText = '';
if(isset($_GET['q'])) $sText = $q;

//form
echo "<form action='search.php' method=GET><center>";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'><tr><td colspan='2' bgcolor='white'>

<center><table width=400 cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Search</font></b></td></tr>
<tr><td bgcolor='$table1' width=150><font size='$font_m' face='$font_face' color='$font_color_main'>Search Text:</font></td><td bgcolor='$table1'><input type=text name=q size=30 value='".$sText."'></td></tr>
<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Require:</font></td><td bgcolor='$table1'><select name='req'>
<option value='OR'>Any of the words
<option value='AND' selected>All of the words
</select></td></tr>
<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>Additional options:</font></td><td bgcolor='$table1'><input type='checkbox' name='intopic' DISABLED>Search in posts</td></tr>
<tr><td bgcolor='$table1' colspan='2'><input type=submit value='Search'></td></tr>
</tr></td></table></center></table>$skin_tablefooter</form>";
//end form

if($tdb->is_logged_in()) $_COOKIE["power_env"] = 0;
if(isset($_GET['q'])) {
	$forums = array();
	$fRecs = $tdb->listRec("forums", 1);
	for($i=0, $fmax=count($fRecs);$i<count($fRecs);$i++) {
		if($fRecs[$i]["view"] <= $_COOKIE["power_env"]) $forums[] = $fRecs[$i];
	}

	$sText = $_GET['q'];
	$sText = str_replace(",", "", $sText);
	$sText = str_replace(".", "", $sText);
	$sText = str_replace(";", "", $sText);
	$sText = str_replace("?", "", $sText);
	$sText = str_replace("\"", "", $sText);
	$sText = str_replace("\'", "", $sText);
	$sText = str_replace("+", "", $sText);
	$sText = str_replace("-", "", $sText);
	$words = explode(" ", $sText);

	if($_GET['req'] != 'OR') {
		$sWords = array();
		foreach($words as $word) {
			$sWords[] = "subject?'{$word}'";
		}
		$sWords = implode("&&", $sWords);
	}
}
$results = array();
if(!isset($_GET['intopic'])) {
	//start searching through the topic list
	foreach($forums as $fRec) {
		$posts_tdb->setFp("topics", $fRec['id']."_topics");
		$qtmp = array();
		if($_GET['req'] == 'OR') {
			foreach($words as $word) {
				$qtmp2 = $posts_tdb->query("topics", "subject?'{$word}'");
				$qtmp = array_merge($qtmp, $qtmp2);
			}
		} else {
			$qtmp = $posts_tdb->query("topics", $sWords);
		}
		if(empty($qtmp) || $qtmp[0] == '') continue;
		for($i=0, $max=count($qtmp);$i<$max;$i++) {
			$qtmp[$i]['fId'] = $fRec['id'];
		}
		$qtmp['forum'] = array($fRec['forum'], $fRec['id']);
		$results[] = $qtmp;
	}
} else {
	//thorough search
	foreach($forums as $fRec) {
		$posts_tdb->setFp("topics", $fRec['id']."_topics");
		$posts_tdb->setFp("posts", $fRec['id']);

		if($_GET['req'] == "OR") {
			$qtmp = array();
			foreach($words as $word) {
				$qtmp2 = $posts_tdb->query("posts", "message?'".$word."'");
				$qtmp = array_merge($qtmp, $qtmp2);
			}
		} else {
			$qtmp = $posts_tdb->query("posts", $sWords);
		}
		if(empty($qtmp) || $qtmp[0] == '') continue;
		for($i=0, $max=count($qtmp);$i<$max;$i++) {
			$qtmp[$i]['fId'] = $fRec['id'];
		}
		$topics = array();
		//echo '<pre>'; print_r($qtmp); echo '</pre><br><br>';
		foreach($qtmp as $pRec) {
			if(!is_array($topics[$pRec['tId']])) {
				if(FALSE === ($topic = $posts_tdb->get("topics", $pRec['tId']))) continue;
				$topics[$pRec['tId']] = array('topic' => $topic[0], $pRec['id']);
				unset($topic);
			} else $topics[$pRec['tId']][] = $pRec;
		}
		$topics['forum'] = array($fRec['forum'], $fRec['id']);
		$results[] = $topics;
		unset($topics);
	}
	print_r($results);
}

//results here
if(isset($step)) {
	echo "<br><br><center>";
	echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
	echo "<table cellspacing=1 cellpadding='2' bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'>";
	//while(list($fId, $result) = each($results)) {
	foreach($results as $result) {
		if(empty($result)) continue;
		echo "<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Results in <a href=\"viewforum.php?id={$result['forum'][1]}\" target=_blank>{$result['forum'][0]}</a>:</font></b></td></tr>";
		$fId = $result['forum'][1];
		if($step = '2.1') {
			unset($result['forum']);
			$pa = 0;
			foreach($result as $topic) {
				if($pa == 2) {
					$pa = 0;
					echo "</tr><tr>";
				}
				echo "<td bgcolor='$table1' width='50%'><font size='$font_m' face='$font_face' color='$font_color_main'><a href='viewtopic.php?id={$fId}&t_id={$topic['id']}' target=_blank>{$topic['subject']}</a></font></td>";
				$pa++;
			}
			while($pa < 2) {
				echo "<td bgcolor='$table1' width='50%'><p><font> </font></p></td>";
				$pa++;
			}
			echo '</tr>';
		} elseif($step = '2.2') {
			$x = +1;
			$pa=0;
			$bold_word = array();
			foreach($words as $word) {
				$bold_word[] = '<b>' . $word . '</b>';
			}
			foreach($result as $topic) {
				//for($i=0,$pa=0,$max=count($result)-1;$i<$max;$i++) {
				if($x == 0) {
					$table_color = $table1;
					$table_font = $font1;
					$x++;
				} else {
					$table_color = $table2;
					$table_font = $font2;
					$x--;
				}
				$max = count($topic)-1;
				echo "<td colpsan='2' bgcolor='$table1'><p><font size='$font_m' face='$font_face' color='$font_color_main'><a href='viewtopic.php?id={$fId}&t_id={$topic['topic']['id']}' target=_blank>{$topic['topic']['subject']}</a>found {$max} number of posts:</font></p>";
				/*
				for($i=0;$i<$max;$i++) {
				$pos = array();
				strpos($topic[$i],
				} */
				echo '</td></tr>';
			}
		}
	}
	echo "</table></tr></td></center></table>$skin_tablefooter";
	flush();
}
require_once('./includes/footer.php');
?>