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
	if (!($tdb->is_logged_in())) {
		$posts_tdb->set_user_info("guest", "password", "0", "0");
		$_COOKIE["power_env"] = 0;
	}
	else $posts_tdb->set_user_info($_COOKIE["user_env"], $_COOKIE["uniquekey_env"], $_COOKIE["power_env"], $_COOKIE["id_env"]);
	$where = $fRec[0]["forum"];
	if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) exitPage("Invalid ID", true);
	if ($_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have enough Power to view this forum", true);
	$vars["cTopics"] = $posts_tdb->getNumberOfRecords("topics");
	if (!isset($_GET["page"])) $_GET["page"] = 1;
	$vars["page"] = $_GET["page"];
	$start = ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"]) + 1;
	$tRecs1 = $posts_tdb->query("topics", "sticky='1'", 1);
	if (!empty($tRecs1)) $c_total_stickies = count($tRecs1);
	else $c_total_stickies = 0;
	if ($c_total_stickies >= $start + $_CONFIG['topics_per_page']) {
		//if greater than how many will be displayed
		//delete extra records off the end
		for($i = ($start - 1); $i <= $c_total_stickies; $i++) {
			unset($tRecs1[$i]);
		}
	}
	//delete records off the beginning
	if ($_GET['page'] != 1) {
		for($i = 0; $i < ($start - 1); $i++) {
			unset($tRecs1[$i]);
		}
		//Clark's error fix
		if ($tRecs1 !== false)
			$tRecs1 = array_merge(array(), $tRecs1);
		//reindex
	}
	if (!empty($tRecs1)) $c_cur_stickies = count($tRecs1);
	else $c_cur_stickies = 0;
	if ($c_cur_stickies != $_CONFIG['topics_per_page']) {
		if ($_GET['page'] == 1) {
			$tRecs2 = $posts_tdb->query('topics', "sticky='0'", $start, $_CONFIG['topics_per_page'] - $c_cur_stickies);
		} elseif($_GET['page'] > 1) {
			$tRecs2 = $posts_tdb->query('topics', "sticky='0'", $start - $c_total_stickies, $_CONFIG['topics_per_page'] - $c_cur_stickies);
		}
	}
	if ($tRecs2 !== FALSE) {
		if (!empty($tRecs1)) $tRecs = array_merge($tRecs1, $tRecs2);
		else $tRecs = $tRecs2;
	} elseif(!empty($tRecs1)) $tRecs = $tRecs1;
	else $tRecs = array();
	if ($vars["cTopics"] <= $_CONFIG["topics_per_page"]) $num_pages = 1;
	elseif (($vars["cTopics"] % $_CONFIG["topics_per_page"]) == 0) $num_pages = ($vars["cTopics"] / $_CONFIG["topics_per_page"]);
	else $num_pages = ($vars["cTopics"] / $_CONFIG["topics_per_page"]) + 1;
	$num_pages = (int) $num_pages;
	$p = createPageNumbers($_GET["page"], $num_pages, 'id='.$_GET['id']);
	require_once('./includes/header.php');
	if (isset($_GET['show'])) echo 'forum last check: '.gmdate("M d, Y g:i:s a", user_date($_COOKIE['fId'.$_GET['id']]));
	$posts_tdb->d_topic($p);
	echoTableHeading($fRec[0]["forum"], $_CONFIG);
	echo "
		<tr>
			<th style='width: 75%;'>Topic</th>
			<th style='width:25%;text-align:center;'>Last Post</th>
		</tr>";
	if (empty($tRecs)) {
		echo "
		<tr>
			<td colspan='6' class='area_2' style='text-align:center;font-weight:bold;padding:20px;'>no posts</td>
		</tr>";
	} else {
		foreach($tRecs as $tRec) {
			$posts_tdb->set_topic(array($tRec));
			if ($tRec["icon"] == "") {
				echo "";
			} else {
				if ($tdb->is_logged_in()) {
					if ($_COOKIE['lastvisit'] < $tRec['last_post']) {
						//if($_COOKIE['fId'.$_GET['id'].'tId'.$tRec['id']] == 1 || ($tRec['last_post'] > $_COOKIE['fId'.$_GET['id']] && $_COOKIE['fId'.$_GET['id'].'tId'.$tRec['id']] != 0)) {
						//$v_icon = "<font color=red size='$font_s'>new</font>";
						$tRec['icon'] = 'new.gif';
					}
					else $v_icon = "";
				}
				else $v_icon = "";
				if ($tRec["sticky"] == "1") {
					if ($_CONFIG["sticky_after"] == "1") $tRec["subject"] = "<a href='viewtopic.php?id=".$_GET["id"]."&amp;t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>&nbsp;".$_CONFIG["sticky_note"];
					else $tRec["subject"] = $_CONFIG["sticky_note"]."&nbsp;<a href='viewtopic.php?id=".$_GET["id"]."&amp;t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>";
				}
				else $tRec["subject"] = "<a href='viewtopic.php?id=".$_GET["id"]."&amp;t_id=".$tRec["id"]."'>".$tRec["subject"]."</a>";
				settype($tRec["replies"], "integer");
				$total_posts = $tRec["replies"] + 1;
				$num_pages = ceil($total_posts / $_CONFIG["posts_per_page"]);
				if ($num_pages == 1) {
					$r_ext = "";
				} else {
					$r_ext = "<br /><div class='pagination_small'> Pages: ( ";
					for($m = 1; $m <= $num_pages; $m++) {
						$r_ext .= "<a href='viewtopic.php?id=".$_GET["id"]."&amp;t_id=".$tRec["id"]."&page=$m'>$m</a> ";
					}
					$r_ext .= ")</div>";
				}
				if ($tRec["topic_starter"] == "guest") $tRec["topic_starter"] = "<i>guest</i>";
				echo "
		<tr>
			<td class='area_2' style=\"cursor:pointer;\" onclick=\"window.location.href='viewtopic.php?id=".$_GET["id"]."&amp;t_id=".$tRec["id"]."';\" onmouseover=\"this.className='area_2_over'\" onmouseout=\"this.className='area_2'\">
				<span class='link_1'>".$tRec["subject"].$r_ext."</span>
				<div class='description'>Started By:&nbsp;<span style='color:#".$statuscolor."'>".$tRec["topic_starter"]."</span></div>
				<div class='box_posts'><strong>Views:</strong>&nbsp;".$tRec["views"]."</div>
				<div class='box_posts'><strong>Replies:</strong>&nbsp;".$tRec["replies"]."</div></td>
			<td class='area_1' style='text-align:center;'>
				<div class='post_image'><img src='icon/".$tRec["icon"]."'></div>
				<span class='latest_topic'><span class='date'>".gmdate("M d, Y g:i:s a", user_date($tRec["last_post"]))."</span>
				<br />
				<strong>By:</strong> ";
				if ($tRec["user_id"] != "0") echo "<span class='link_2'><a href='profile.php?action=get&id=".$tRec["user_id"]."'>".$tRec["user_name"]."</a></span></td>
		</tr>";
				else echo "a ".$tRec["user_name"]."</span></td>
		</tr>";
			}
		}
	}
	echo $skin_tablefooter;
	require_once('./includes/footer.php');
?>
