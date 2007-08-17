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
if(isset($_GET['q'])) $sText = $_GET['q'];

if(!$tdb->is_logged_in()) $_COOKIE["power_env"] = 0;

//build our forum list for selecting which forums to search from
$form_cats = $tdb->listRec("cats", 1);
$form_select = "";
foreach($form_cats as $form_c) {
    if(FALSE !== ($form_forums = $tdb->query("forums", "cat='".$form_c["id"]."'"))) {
        foreach($form_forums as $form_f) {
            if($form_f["view"] <= $_COOKIE["power_env"]) $form_select .= "<option value='".$form_f["id"]."'>".$form_c["name"]." -&#62; ".$form_f["forum"]."\n";
        }
    }
}

//form
echo "<form action='search.php' method=GET><center>";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 bgcolor='$border' WIDTH='".$_CONFIG["table_width_main"]."'>
<tr><td colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Search</font></b></td></tr>
<tr><td bgcolor='$table1' width=40% align=right><font size='$font_m' face='$font_face' color='$font_color_main'>Search Text:</font></td><td bgcolor='$table1'><input type=text name=q size=30 value='".$sText."'></td></tr>
<tr><td bgcolor='$table1' align=right><font size='$font_m' face='$font_face' color='$font_color_main'>Made by User:</font></td><td bgcolor='$table1'><input type=text name=user size=30></td></tr>
<tr><td bgcolor='$table1' align=right><font size='$font_m' face='$font_face' color='$font_color_main'>Require:</font></td><td bgcolor='$table1'><select name='req'>
<option value='OR'>Any of the words
<option value='AND' selected>All of the words
</select></td></tr>
<tr><td bgcolor='$table1' align=right><font size='$font_m' face='$font_face' color='$font_color_main'>Which forums to search:</font></td><td bgcolor='$table1'><select name='forums_req'>
<option value='all' selected>All Forums
$form_select
</select></td></tr>
<tr><td bgcolor='$table1' align=right><font size='$font_m' face='$font_face' color='$font_color_main'>Additional options:</font></td><td bgcolor='$table1'><input type='checkbox' name='intopic'>Search in posts</td></tr>
<tr><td bgcolor='$table1' colspan='2' align=center><input type=submit value='Search'></td></tr>
</table>$skin_tablefooter</form>";
//end form


if(isset($_GET['q']) && trim($_GET['q']) != "" || trim($_GET["q"]) == "" && trim($_GET["user"]) != "") {
	$forums = array();
    $fRecs = $tdb->listRec("forums", 1);
    if($_GET["forums_req"] == "all") {
        for($i=0, $fmax=count($fRecs);$i<count($fRecs);$i++) {
            if($fRecs[$i]["view"] <= $_COOKIE["power_env"]) $forums[] = $fRecs[$i];
        }
    } else {
        for($i=0, $fmax=count($fRecs);$i<count($fRecs);$i++) {
            if($fRecs[$i]["view"] <= $_COOKIE["power_env"] && $fRecs[$i]["id"] == $_GET["forums_req"]) $forums[] = $fRecs[$i];
        }
    }

    if(isset($_GET["intopic"])) $intopic = TRUE;
    else $intopic = FALSE;

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

    $userParam = $_GET["user"];

    $sTopics = array();
    foreach($words as $word) {
        if($_GET["req"] == "OR" && $userParam != "") $sTopics[] = "subject?'{$word}'&&user_name='{$userParam}'";
        else $sTopics[] = "subject?'{$word}'";
    }
    
    if($intopic) {
        $sPosts = array();
        foreach($words as $word) {
            if($_GET["req"] == "OR" && $userParam != "") $sPosts[] = "message?'{$word}'&&user_name='{$userParam}'";
            else $sPosts[] = "message?'{$word}'";
        }
    }

    if($_GET['req'] != 'OR') {
		$sTopics = implode("&&", $sTopics);
		if($userParam != "") $sTopics .= "&&user_name='{$userParam}'";
        if($intopic) {
            $sPosts = implode("&&", $sPosts);
            if($userParam != "") $sPosts .= "&&user_name='{$userParam}'";
        }
	} else {
        $sTopics = implode("||", $sTopics);
        if($intopic) $sPosts = implode("||", $sPosts);
    }
    
    if(trim($sText) == "" && $userParam != "") {
        $sTopics = "user_name='{$userParam}'";
        if($intopic) $sPosts = "user_name='{$userParam}'";
    }
    
    $MAX_TOPIC_RESULTS = 10;
    $MAX_POSTS_RESULTS = 10;

    //query time...
    $result = array();
    foreach($forums as $fRec) {
        //run on each forum
        $posts_tdb->setFp("topics", $fRec["id"]."_topics");
        if(FALSE !== ($r = $posts_tdb->query("topics", $sTopics, 1, $MAX_TOPIC_RESULTS))) {
            $MAX_TOPIC_RESULTS -= count($r);
            $resultTopics[$fRec["id"]]["forumName"] = $fRec["forum"];
            $resultTopics[$fRec["id"]]["catID"] = $fRec["cat"];
            //first 10 results...
            foreach($r as $sRec) {
                $resultTopics[$fRec["id"]]["records"][] = array("topicID" => $sRec["id"], "topicName" => $sRec["subject"]);
            }
        }
        unset($r);
        
        if($intopic) {
            $posts_tdb->setFp("posts", $fRec["id"]);
            if(FALSE !== ($r = $posts_tdb->query("posts", $sPosts, 1, $MAX_POSTS_RESULTS))) {
                $MAX_POSTS_RESULTS -= count($r);
                $resultPosts[$fRec["id"]]["forumName"] = $fRec["forum"];
                $resultPosts[$fRec["id"]]["catID"] = $fRec["cat"];
                //first 10 results...
                foreach($r as $sRec) {
                    //need to get the topic name...
                    $topic_query = $posts_tdb->get("topics", $sRec["t_id"]);
                    $sRec["topicName"] = $topic_query[0]["subject"];
                    $resultPosts[$fRec["id"]]["records"][] = $sRec;
                }
            }
        }
    }
}

//Lets query this
/*
$resultTopics {
    forumID {
        forumName
        catID
        records {
            index {
                topicID
                topicName [the search text should be bolded, maybe not...]
            }
        }
    }
}

$resultPosts {
    forumID {
        forumName
        catID
        records {
            index {
                COMPLETE RESULT
            }
        }
    }
}
*/

//results here
if(!empty($resultTopics)) {
	echo "<br><br><center>";
	echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], "First 10 Results..."), $_CONFIG);
	echo "<table cellspacing=1 cellpadding='2' bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'>";
	//while(list($fId, $result) = each($results)) {
	foreach($resultTopics as $fID => $result) {
		if(empty($result)) continue;
        $cRec = $tdb->get('cats', $result["catID"]);
        echo "<tr><td colspan='1' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Results in ".$cRec[0]['name']." ".$_CONFIG['table_sep']." <a href=\"viewforum.php?id={$fID}\" target=_blank>{$result['forumName']}</a>:</font></b></td></tr>\n";

        foreach($result["records"] as $topic) {
            echo "<tr><td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><a href='viewtopic.php?id={$fID}&t_id={$topic['topicID']}' target=_blank>{$topic['topicName']}</a></font></td></tr>\n";
        }

	}
	echo "</table>$skin_tablefooter";
	flush();
}
if(!empty($resultPosts)) {
    echo "<p>Showing the first 10 in topic results...</p>";
    $table_color = $table1;
    $table_font = $font1;
    foreach($resultPosts as $fID => $result) {
        foreach($result["records"] as $post) {
            $msg = format_text(filterLanguage(UPBcoding($post["message"]), $_CONFIG["censor"]));
            $msg = removeRedirect($msg);
            echo "<br><br><center>\n";
            echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], "Result from: <a href='viewforum.php?id=".$fID."'>".$result["forumName"]."</a> ".$_CONFIG["where_sep"]." <a href='viewtopic.php?id=".$fID."&t_id=".$post["t_id"]."'>".$post["topicName"]."</a>"), $_CONFIG);
            echo "<table cellspacing=1 cellpadding='2' bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'>\n";
            echo "<tr><td bgcolor='$table_color' valign=top width=15%><font size='$font_m' face='$font_face' color='$table_font'><b>".$post["user_name"]."</b></td>\n";
            echo "<td bgcolor='$table_color' valign=top><font size='$font_m' face='$font_face' color='$table_font'>
$msg
</font></td></tr>\n";
            echo "</table>$skin_tablefooter";
        }
    }
}
if(empty($resultTopics) && empty($resultPosts) && isset($_GET["q"]) && strlen(trim($_GET["q"])) > 0) {
    echo '<center>No results found</center>';
}
require_once('./includes/footer.php');

function removeRedirect($string) {
    $pos = strpos($string, "<meta");
    if($pos !== FALSE) {
        $pos2 = strpos($string, ">", ($pos+1));
        $string = substr($string, 0, $pos).substr($string, ($pos2+1));
    }
    return $string;
}
?>
