<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	header ("refresh: 600");
	if (!file_exists("./db/main.tdb") && file_exists("./db/config2.php")) die("updater has not been run yet. click <a href='update1.x-2.0.php'>here</a> to update.");
	if (file_exists("config.php")) {
		require_once("config.php");
		if (!defined('DB_DIR')) die("installer has not been run yet. click <a href='install.php'>here</a> to install.");
	}
	require_once("./includes/upb.initialize.php");

	if(!isset($_GET['action'])) $_GET['action'] = '';      //PHP sends Notices if you don't do this first
	if($_GET['action'] == 'markallread') {
	    $now = mkdate();
	    if($tdb->is_logged_in()) {
	        $tdb->edit('users', $_COOKIE['id_env'], array('lastvisit' => $now));   //Update lastvisit field for next time
	    }
        while(list($key_1, $val_1) = each($_SESSION['newTopics'])) {    //Loop through each forum
            if($key_1 == 'lastVisitForums') continue;
            while(list($key_2, $val_2) = each($_SESSION['newTopics'][$key_1])) {    //Loop through each topic
                if($val_2 == 2) continue;       // Do not erase bookmarked topics
                unset($_SESSION['newTopics'][$key_1][$key_2]);
            }
        }
        while(list($key, $val) = each($_SESSION['newTopics']['lastVisitForums'])) {
            $_SESSION['newTopics']['lastVisitForums'][$key] = $now;
        }
        if(!isset($_GET['ref'])) $_GET['ref'] = 'index.php';
        redirect($_GET['ref'], 0);
	}

	if ($_COOKIE["power_env"] == "" || empty($_COOKIE["power_env"]) || trim($_COOKIE["power_env"]) == "") $_COOKIE["power_env"] = "0";
	require_once("./includes/header.php");
	$posts = new tdb(DB_DIR, "posts.tdb");
	$cRecs = $tdb->listRec("cats", 1);
	//$cRecs = $tdb->query("cats", "view<'".($_COOKIE["power_env"] + 1)."'");
	if ($cRecs[0]["id"] == "") {
		echo "
			<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>No categories have been added yet.<br />";
		if ($_COOKIE["power_env"] < 3) {
			echo " Please contact an Administrator";
			if ($_COOKIE["power_env"] > 0) {
                echo " via <a href='newpm.php?id=1'>PM Message</a>";
                if (EMAIL_MODE)
                echo "or <a href='email.php?id=1'>web email</a>";
		    }
        } else {
			echo " To add a Category, <a href='admin_forums.php?action=add_cat'>click here</a>.";
		}
		echo '</div></div>';
	} else {
	    // Sort categories in the order that they appear
		$cSorting = explode(",", $_CONFIG["admin_catagory_sorting"]);
		$k = 0;
		$i = 0;
		$sorted = array();
		while ($i < count($cRecs)) {
		    if($k >= count($cSorting)) break;
			if ($cSorting[$k] == $cRecs[$i]["id"]) {
				if ($_COOKIE["power_env"] >= $cRecs[$i]["view"]) $sorted[] = $cRecs[$i];
				//unset($cRecs[$i]);
				$k++;
				$i = 0;
			} else {
				$i++;
			}
		}
		$cRecs = $sorted;
		unset($sorted, $i, $catdef, $cSorting);
		reset($cRecs);

    	if ($cRecs[0]["id"] == "") {
    		echo "
    			<div class='alert'><div class='alert_text'>
    			<strong>Caution!</strong></div><div style='padding:4px;'>This is a private forum, which may require registration.<br />";
    		if ($_COOKIE["power_env"] < 3) {
    			echo " Please contact an Administrator";
    			if ($_COOKIE["power_env"] > 0) echo " via <a href='newpm.php?id=1'>PM Message</a> or <a href='email.php?id=1'>web email</a>";
    		} else {
    			echo " To add a Category, <a href='admin_forums.php?action=add_cat'>click here</a>.";
    		}
    		echo '</div></div>';
    	} else {
    		$t_t = 0;
    		$t_p = 0;
    		foreach($cRecs as $cRec) {
    			if ($_COOKIE["power_env"] >= $cRec["view"]) {
    		echoTableHeading($cRec["name"], $_CONFIG);
    				echo "
    			<tr>
    				<th style='width: 75%;'>Forum</th>
    				<th style='width:25%;text-align:center;'>Latest Topic</th>
    			</tr>";
    				$cId = $cRec["id"];
    				//$fRecs = $tdb->query("forums", "cat='$cId'&&view<'".($_COOKIE["power_env"] + 1)."'");
    				if ($cRec["sort"] == "") {
    					echo "
    			<tr>
    				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='2'>No forums have been added to this Category yet.<br />";
    					if ($_COOKIE["power_env"] != "3") {
    						echo " Please contact an Administrator";
                            if ($_COOKIE["power_env"] > 0) {
                                echo " via <a href='newpm.php?id=1'>PM Message</a>";
                                if (EMAIL_MODE) echo " or <a href='email.php?id=1'>web email</a>";
        					}
    					} else {
    						echo " To add a forum, <a href='admin_forums.php?action=add_forum&cat_id=".$cRec["id"]."'>click here</a>.";
    					}
    					echo "</td>
    			</tr>";
    				} else {
    					unset($sort);
    					$sort = explode(",", $cRec["sort"]);
    					while (!empty($sort)) {
    						$fRec = $tdb->get("forums", $sort[0]);
    						$fRec = $fRec[0];
    						if ((int)$fRec["view"] <= (int)($_COOKIE["power_env"])) {
    							//if($fRec["cat"] == $cRec["id"]) {
    							$posts->setFp("topics", $fRec["id"]."_topics");
    							$tRec = $posts->listRec("topics", 1, 1);
    							if ($tRec[0]["id"] == "") {
    								$when = "No Posts";
    								$v_icon = "off.png";
    							} else {
    								$when = "<span class='date'>".gmdate("M d, Y g:i:s a", user_date($tRec[0]["last_post"]))."</span><br /><strong>In:</strong>&nbsp;<strong><a href='viewtopic.php?id=".$fRec["id"]."&amp;t_id=".$tRec[0]["id"]."'>".$tRec[0]["subject"]."</a></strong><br /><strong>By:</strong> ";
    								if ($tRec[0]["user_id"] != "0") $when .= "<span class='link_2'><a href='profile.php?action=get&amp;id=".$tRec[0]["user_id"]."'>".$tRec[0]["user_name"]."</a></span>";
    								else $when .= "a ".$tRec[0]["user_name"]."";
			    					if($_SESSION['newTopics']['f'.$fRec['id']]['t'.$tRec[0]['id']] == 2) {
                					    $v_icon = 'star.gif';
                					} elseif($_SESSION['newTopics']['f'.$fRec['id']]['t'.$tRec[0]['id']] == 1 || ($tRec['last_post'] > $_SESSION['newTopics']['lastVisitForums'][$fRec['id']] && $_SESSION['newTopics']['f'.$fRec['id']]['t'.$tRec[0]['id']] != 0)) {
                						$v_icon = 'new.gif';
                					} else $v_icon = "off.png";
    							}
    							$t_t += $fRec["topics"];
    							$t_p += $fRec["posts"];
    							if ($fRec["topics"] == "0") $v_icon = "off.png";
    							echo "
    			<tr>
    				<td class='area_2' style=\"cursor:pointer;\" onclick=\"window.location.href='viewforum.php?id=".$fRec["id"]."';\" onmouseover=\"this.className='area_2_over'\" onmouseout=\"this.className='area_2'\">
    								<span class='link_1'><a href='viewforum.php?id=".$fRec["id"]."'>".$fRec["forum"]."</a></span>
    								<div class='description'>".$fRec["des"]."</div>
    								<div class='box_posts'><strong>Posts:</strong>&nbsp;".$fRec["posts"]."</div>
    								<div class='box_topics'><strong>Topics:</strong>&nbsp;".$fRec["topics"]."</div></td>
    				<td class='area_1' style='text-align:center;'><div class='post_image'><img src='".$_CONFIG["skin_dir"]."/icons/$v_icon' alt='' title='' /></div><span class='latest_topic'>$when</span></td>
    			</tr>";
    							unset($when);
    							/*} else {
    							echo "<tr><td colspan='6' bgcolor='$table1'><center>Forum's Category ID doesn't match</center></td></tr>";
    							}*/
    						}
    						array_shift($sort);
    						unset($when);
    					}
    				}
    				echoTableFooter($_CONFIG['skin_dir']);
    			}
    			unset($cRec);
    		}
    	}
	}
	//start Statistics Table
	$whos = whos_online($whos_online_log, $_STATUS);
	$whos_t = $whos["users"]+$whos["guests"];
	$users_string = "";
	if ($whos["users"] > 0) $users_string = $whos["who"];
	$mem_total = $tdb->getNumberOfRecords("users");
	$mem_last = $tdb->listRec("users", $mem_total, 1);
	$mt = explode(' ', microtime());
	$script_end_time = $mt[0] + $mt[1];
	echo "
		<div id='tabstyle_2'>
			<ul>";
    echo "
				<li><a href='index.php?action=markallread' title='Mark as read'><span>Mark all forums as read?</span></a></li>";
	echo "
			</ul>
		</div>
		<div style='clear:both;'></div>";
		echoTableHeading("Community Information", $_CONFIG);
	echo "
			<tr>
				<th>Users online in the last 15 minutes: $whos_t</th>
			</tr>
			<tr>
				<td class='area_2'>";
	//Whos Online System Offline
	echo "
					<span class='whos_online'>".$whos["users"]." member(s) and ".$whos["guests"]." guest(s).</span>
					<hr />
					<strong>".$users_string."</strong></td>
			</tr>
			<tr>
				<th>Board Statistics</th>
			</tr>
			<tr>
				<td class='area_1'>
					<div class='legend_2'>No New Posts</div>
					<div class='legend_1'><img src='".$_CONFIG["skin_dir"]."/icons/off.png' alt='' title='' /></div>
					<div class='legend_2'>New Posts</div>
					<div class='legend_1'><img src='".$_CONFIG["skin_dir"]."/icons/on.png' alt='' title='' /></div>
					<span class='stats'>
					<strong>Total Topics:</strong> $t_t<br />
					<strong>Total Posts:</strong> $t_p<br />
					<strong>Total Members:</strong> $mem_total<br />
					<strong>Newest Member:</strong> <span class='link_2'><a href='profile.php?action=get&amp;id=".$mem_last[0]["id"]."'>".$mem_last[0]["user_name"]."</a></span><br />
					<strong>Forum Page Views:</strong> $hits_today<br />
					<strong>Page Rendering Time:</strong> ".round($script_end_time - $script_start_time, 5)." seconds</span></td>
			</tr>";
      echoTableFooter($_CONFIG['skin_dir']);
	//End Statistic Table
	require_once("./includes/footer.php");

	if(!isset($_SESSION['iplogged']) || ($_SESSION['iplogged']+300) < time()) {
	    $_SESSION['iplogged'] = time();
        $user = ((empty($_COOKIE["user_env"])) ? "guest" : $_COOKIE["user_env"]);;
    	$visitor_info = ((!isset($_SERVER['REMOTE_HOST']) || $_SERVER['REMOTE_HOST'] == "") ? $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST']);
    	$base = "http://" . $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
        $date = date("r", time());

        $fp = fopen(DB_DIR."/ip.log", "a");
        fputs($fp, $visitor_info."\t".$user."\t".$base."\t".time()."\t".$_SERVER['HTTP_USER_AGENT']."\n");
        //fputs($fp, "<strong>$visitor_info</strong> -<i>".$_SERVER['HTTP_USER_AGENT']."</i>- <strong>$user</strong>- <br />Accessed \"$base\" on: $date.--------------------------------Next Person<p><br />\r\n");
        fclose($fp);

        if(filesize(DB_DIR."/ip.log") > (1024 * 1024)) {
            $fp = fopen(DB_DIR."/ip.log", 'r');
            fseek($fp, (filesize(DB_DIR."/ip.log") - (1024 * 1024)));
            $log = fread($fp, (1024 * 1024));
            fclose($fp);
            $fp = fopen(DB_DIR."/ip.log", 'w');
            fwrite($fp, $log);
            fclose($fp);
        }
    }
?>
