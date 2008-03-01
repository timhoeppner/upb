<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once('./includes/class/func.class.php');
	$where = "Members List";
	require_once('./includes/header.php');
	if ($tdb->is_logged_in()) {
		if ($_GET["page"] == "") $_GET["page"] = 1;
		$users = $tdb->listRec("users", ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"] + 1), $_CONFIG["topics_per_page"]);
		$c = $tdb->getNumberOfRecords("users");
		if ($c <= $_CONFIG["topics_per_page"]) {
			$num_pages = 1;
		} elseif (($c % $_CONFIG["topics_per_page"]) == 0) {
			$num_pages = ($c / $_CONFIG["topics_per_page"]);
		} else {
			$num_pages = ($c / $_CONFIG["topics_per_page"]) + 1;
		}
		$num_pages = (int) $num_pages;
		if ($num_pages == 1) {
			$pageStr = "<span class='pagination_current'>$num_pages</span>";
		} else {
			//$pageStr = "<font face='$font_face' size='$font_s'><span class=pagenumstatic>";
			for($i = 1; $i <= $num_pages; $i++) {
				if ($_GET["page"] == $i) {
					$pageStr .= "<span class='pagination_current'>".$i."</span>";
				} else {
					$pageStr .= "<span class='pagination_link'><a href='showmembers.php?page=".$i."'>".$i."</a></span> ";
				}
			}
			//$pageStr .= "</font></span>";
			unset($num_pages);
		}
		echo "
		<table class='pagenum_container' cellspacing='1'>
			<tr>
				<td style='text-align:left;height:23px;'><span class='pagination_current'>Pages: </span>".$pageStr."</td>
			</tr>
		</table>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "
			<tr>
				<th style='width:3%' style='text-align:center;'>ID</th>
				<th style='width:15%'>Username</th>
				<th style='width:20%' style='text-align:center;'>Location</th>
				<th style='width:5%' style='text-align:center;'>Posts</th>
				<th style='width:15%' style='text-align:center;'>AIM</th>
				<th style='width:15%' style='text-align:center;'>MSN</th>
				<th style='width:15%' style='text-align:center;'>Yahoo!</th>
				<th style='width:12%' style='text-align:center;'>ICQ</th>
			</tr>";
		if ($users[0]["id"] == "") {
			echo "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='8'>No records found</td>
			</tr>";
		} else {
			foreach($users as $user) {
				if ($user["level"] == "2") $userColor = $_STATUS["moderatcolor"];
				elseif($user["level"] == "3") $userColor = $_STATUS["admcolor"];
				else $userColor = $_STATUS["membercolor"];
				/* location, # of posts, aim, msn, yahoo, icq */
				echo "
			<tr>
				<td class='area_1' style='padding:8px;'>".$user["id"]."</td>
				<td class='area_2'><span class='link_1'><a href='profile.php?action=get&id=".$user["id"]."'>".$user["user_name"]."</a></span></td>
				<td class='area_2' style='text-align:center;'>".$user["location"]."</td>
				<td class='area_1' style='text-align:center;'>".$user["posts"]."</td>
				<td class='area_2' style='text-align:center;'>";
				if ($user["aim"] != "") echo "<a href='aim:goim?screenname=".$user["aim"]."'><img src='images/aol.gif' border='0'>&nbsp;".$user["aim"]."</a>";
				echo "</td>
				<td class='area_1' style='text-align:center;'>";
				if ($user["msn"] != "") echo "<a href='http://members.msn.com/".$user["msn"]."' target='_blank'><img src='images/msn.gif' border='0'>&nbsp;".$user["msn"]."</a>";
				echo "</td>
				<td class='area_2' style='text-align:center;'>";
				if ($user["yahoo"] != "") echo "<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user["yahoo"]."&m=g&t=0'>&nbsp;".$user["yahoo"]."</a>";
				echo "</td>
				<td class='area_1' style='text-align:center;'>";
				if ($user["icq"] != "") echo "<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user["icq"]."&action=message'><img src='images/icq.gif' border='0'>&nbsp;".$user["icq"]."</a>";
				echo "</td>
			</tr>";
			}
		}
		echoTableFooter(SKIN_DIR);
		echo "
		<table class='pagenum_container' cellspacing='1'>
			<tr>
				<td style='text-align:left;height:23px;'><span class='pagination_current'>Pages: </span>".$pageStr."</td>
			</tr>
		</table>";
	} else {
		echo "<div class='alert'><div class='alert_text'>
<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>";
	}
	require_once('./includes/footer.php');
?>