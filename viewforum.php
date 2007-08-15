<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once('./includes/class/func.class.php');
$fRec = $tdb->get("forums", $_GET["id"]);

require_once('./includes/class/posts.class.php');
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->set_forum($fRec);
if(!($tdb->is_logged_in())) {
    $posts_tdb->set_user_info("guest", "password", "0", "0");
    $_COOKIE["power_env"] = 0;
} else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
$where = $fRec[0]["forum"];

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) exitPage("Invalid ID", true);

if($_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have enough Power to view this forum", true);

$vars["cTopics"] = $posts_tdb->getNumberOfRecords("topics");
if(!isset($_GET["page"])) $_GET["page"] = 1;
$vars["page"] = $_GET["page"];

$start = ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"]) + 1;

$tRecs1 = $posts_tdb->query("topics", "sticky='1'", 1);
if(!empty($tRecs1)) $c_total_stickies = count($tRecs1);
else $c_total_stickies = 0;

if($c_total_stickies >= $start + $_CONFIG['topics_per_page']) { //if greater than how many will be displayed
//delete extra records off the end
for($i=($start - 1);$i<=$c_total_stickies;$i++) {
    unset($tRecs1[$i]);
}
}
//delete records off the beginning
if($_GET['page'] != 1) {
    for($i=0;$i<($start - 1);$i++) {
        unset($tRecs1[$i]);
    }

    $tRecs1 = array_merge(array(), $tRecs1); //reindex
}

if(!empty($tRecs1)) $c_cur_stickies = count($tRecs1);
else $c_cur_stickies = 0;

if($c_cur_stickies != $_CONFIG['topics_per_page']) {
    if($_GET['page'] == 1) {
        $tRecs2 = $posts_tdb->query('topics', "sticky='0'", $start, $_CONFIG['topics_per_page'] - $c_cur_stickies);
    } elseif( $_GET['page'] > 1) {
        $tRecs2 = $posts_tdb->query('topics', "sticky='0'", $start - $c_total_stickies, $_CONFIG['topics_per_page'] - $c_cur_stickies);
    }
}
if($tRecs2 !== FALSE) {
    if(!empty($tRecs1)) $tRecs = array_merge($tRecs1, $tRecs2);
    else $tRecs = $tRecs2;
} elseif(!empty($tRecs1)) $tRecs = $tRecs1;
else $tRecs = array();

//Track unread topics
/* if($tdb->is_logged_in() && !isset($_COOKIE['fId'.$_GET['id']])) {
if(isset($_GET['show'])) echo 'executing....';
$posts_tdb->setFp('trackforums', 'trackforums');
$track_this_forum = $posts_tdb->query('trackforums', "fId='".$_GET['id']."&&uId='".$_COOKIE['id_env']."'");
if(empty($track_this_forum) || FALSE === $track_this_forum) {
//echo 'new forum';
$posts_tdb->add('trackforums', array('fId' => $_GET['id'], 'uId' => $_COOKIE['id_env'], 'lastvisit' => mkdate()));
} else {
//echo 'resetting forums';
$posts_tdb->edit('trackforums', array('lastvisit' => mkdate()));
setcookie('fId'.$_GET['id'], $track_this_forum[0]['lastvisit']);
$_COOKIE['fId'.$_GET['id']] = $track_this_forum[0]['lastvisit'];
}
$posts_tdb->setFp('tracktopics', 'tracktopics');
$track_topics = $posts_tdb->query('tracktopics', "fId='".$_GET['id']."'&&uId='".$_COOKIE['id_env']."'", 1, -1);
$continue_checking_for_newTopics = false;
for($i=0,$max=count($track_topics);$i<$max;$i++) {
if($track_topics[$i]['old'] == '1') {
$posts_tdb->delete('tracktopics', $track_topics[$i]['id']);
setcookie('fId'.$_GET['id'].'tId'.$track_topics[$i]['id'], '');            unset($track_topics[$i]);
} else {
setcookie('fId'.$_GET['id'].'tId'.$track_topics[$i]['id'], '1');
$_COOKIE['fId'.$_GET['id'].'tId'.$track_topics[$i]['id']] = 1;
}
}

$continue_checking_for_newTopics = false;
for($i=0, $max=count($tRecs);$i<$max;$i++) {
if($tRecs[$i]['last_post'] > $_COOKIE['fId'.$_GET['id']]) {
//echo 'checking..';
$posts_tdb->add('tracktopics', array('fId' => $_GET['id'], 'tId' => $tRecs[$i]['id'], 'uId' => $_COOKIE['id_env']));
setcookie('fId'.$_GET['id'].'tId'.$tRecs[$i]['id'], '1');
$_COOKIE['fId'.$_GET['id'].'tId'.$track_topics[$i]['id']] = 1;
if($i == ($max - 1)) $continue_checking_for_newTopics = true;
} else break;
}

if($continue_checking_for_newTopics) {
$more_topics = $posts_tdb->query('topics', "last_post>'".$_COOKIE['lastvisit']."'", 1, -1);
foreach($more_topics as $more_topic) {
$posts_tdb->add('tracktopics', array('fId' => $_GET['id'], 'tId' => $more_topic['id'], 'uId' => $_COOKIE['id_env']));
setcookie('fId'.$_GET['id'].'tId'.$more_topic['id'], '1');
$_COOKIE['fId'.$_GET['id'].'tId'.$more_topic['id']] = 1;
}
}
} */

/*
no. of stickies = count($tRec1)
if no. of stickies = topics_per_page ... skip 2nd query
if no. of stickies < topics_per_page
if page = 0 ... regular query (howmany = topics_per_page - no. of stickies)
if tot no. of topics > 0
if page > 0 ... query(start = $start - total_stickies, howmany = topics_per_page - no. of stickies)

*/
if ($vars["cTopics"] <= $_CONFIG["topics_per_page"]) $num_pages = 1;
elseif (($vars["cTopics"] % $_CONFIG["topics_per_page"]) == 0) $num_pages = ($vars["cTopics"] / $_CONFIG["topics_per_page"]);
else $num_pages = ($vars["cTopics"] / $_CONFIG["topics_per_page"]) + 1;
$num_pages = (int) $num_pages;

$p = createPageNumbers($_GET["page"], $num_pages, 'id='.$_GET['id']);

require_once('./includes/header.php');
if(isset($_GET['show'])) echo 'forum last check: '.gmdate("M d, Y g:i:s a", user_date($_COOKIE['fId'.$_GET['id']]));
//echo "<br>lastvisit: ".$_COOKIE['lastvisit']." <br>thisvisit: ".$_COOKIE['thisvisit']." <br>and finally lastVisitForums:".$_SESSION['newTopics']['lastVisitForums'][$_GET['id']];

$posts_tdb->d_topic($p);
echoTableHeading($fRec[0]["forum"], $_CONFIG);

echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing='1' cellpadding='3' bgcolor='$border' align='center'>
  <tr>";
//<td width='' bgcolor='$header'>&nbsp;</td>
echo "<td width='5%' bgcolor='$header'>&nbsp;</td>
    <td width='36%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Topic</font></td>
    <td width='10%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Topic Starter</font></td>
    <td width='7%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Replies</font></td>
    <td width='7%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Views</font></td>
    <td width='30%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Last Post</font></td>
  </tr>
";

if (empty($tRecs)) {
    echo "<tr><td colspan = '7' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><center>no posts</center></font></td></tr>";
} else {
    foreach($tRecs as $tRec) {
        $posts_tdb->set_topic(array($tRec));
        if ($tRec["icon"] == "") {
            echo "<tr><td colspan = '6' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><center>no posts</center></font></td></tr>";
        } else {
            if($tdb->is_logged_in()) {
                if($_COOKIE['lastvisit'] < $tRec['last_post']) {
                    //if($_COOKIE['fId'.$_GET['id'].'tId'.$tRec['id']] == 1 || ($tRec['last_post'] > $_COOKIE['fId'.$_GET['id']] && $_COOKIE['fId'.$_GET['id'].'tId'.$tRec['id']] != 0)) {
                    //$v_icon = "<font color=red size='$font_s'>new</font>";
                    $tRec['icon'] = 'new.gif';
                } else $v_icon = "";
            } else $v_icon = "";
            if($tRec["sticky"] == "1") {
                if($_CONFIG["sticky_after"] == "1") $tRec["subject"] = "<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>".$_CONFIG["sticky_note"];
                else $tRec["subject"] = $_CONFIG["sticky_note"]."<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>";
            } else $tRec["subject"] = "<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>";
            settype($tRec["replies"], "integer");
            $total_posts = $tRec["replies"] + 1;
            $num_pages = ceil($total_posts / $_CONFIG["posts_per_page"]);
            if($num_pages == 1){
                $r_ext = "";
            } else {
                $r_ext = "<br><font size='$font_s'> Pages ( ";
                for($m=1;$m<=$num_pages;$m++) {
                    $r_ext .= "<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$tRec["id"]."&page=$m'>$m</a> ";
                }
                $r_ext .= ")</font>";
            }
            if($tRec["topic_starter"] == "guest") $tRec["topic_starter"] = "<i>guest</i>";
            echo "<tr>";
            //<td bgcolor='$table1'>$v_icon</td>
            echo "<td bgcolor='$table1' align=center><img src='icon/".$tRec["icon"]."'></td>
		<td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$tRec["subject"].$r_ext."</font></td>
		<td bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'>".$tRec["topic_starter"]."</font></td>
		<td bgcolor='$table1' align=center><font size='$font_m' face='$font_face' color='$font_color_main'>".$tRec["replies"]."</font></td>
                <td bgcolor='$table1' align=center><font size='$font_m' face='$font_face' color='$font_color_main'>".$tRec["views"]."</font></td>
		<td bgcolor='$table1'><font size='$font_s' face='$font_face' color='$font_color_main'>".gmdate("M d, Y g:i:s a", user_date($tRec["last_post"]))." by ";
            if($tRec["user_id"] != "0") echo "<a href='profile.php?action=get&id=".$tRec["user_id"]."'>".$tRec["user_name"]."</a></font></td></tr>\n";
            else echo "a <i>".$tRec["user_name"]."</i></font></td></tr>\n";
        }
    }

}
echo $skin_tablefooter;

$posts_tdb->d_topic($p);

require_once('./includes/footer.php');
?>