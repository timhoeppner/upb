<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_forum.php'>Manage Forums</a>";
	require_once('./includes/header.php');
	$post_tdb = new functions(DB_DIR, "posts.tdb");
	if (!$tdb->is_logged_in() || $_COOKIE["power_env"] < 3) exitPage("
		<div class='alert'><div class='alert_text'>
		<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>
		<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>");
	if (isset($_GET["action"])) {
		if ($_GET["action"] == "edit") {
			if (isset($_GET["id"])) {
				$fRec = $tdb->get("forums", $_GET["id"]);
				if (isset($_POST["u_forum"])) {
					if ($_POST["cat"] != $fRec[0]["cat"]) {
						$cRec = $tdb->get("cats", $fRec[0]["cat"]);
						$cRec[0]["sort"] = explode(",", $cRec[0]["sort"]);
						$key = array_search($fRec[0]["id"], $cRec[0]["sort"]);
						unset($cRec[0]["sort"][$key]);
						$tdb->edit("cats", $cRec[0]["id"], array("sort" => implode(",", $cRec[0]["sort"])));
						unset($key, $cRec);
						$cRec = $tdb->get("cats", $_POST["cat"]);
						if ($cRec[0]["sort"] != "") $cRec[0]["sort"] .= ",".$fRec[0]["id"];
						else $cRec[0]["sort"] = $fRec[0]["id"];
						$tdb->edit("cats", $_POST["cat"], array("sort" => $cRec[0]["sort"]));
					}
					$tdb->edit("forums", $_GET["id"], array("forum" => $_POST["u_forum"], "cat" => $_POST["cat"], "des" => $_POST["des"], "view" => $_POST["u_view"], "post" => $_POST["u_post"], "reply" => $_POST["u_reply"]));
					echo "
						<div class='alert_confirm'>
						<div class='alert_confirm_text'>
						<strong>Redirecting:</div><div style='padding:4px;'>
						Forum successfully edited.
						</div>
						</div>";
					redirect($_SERVER['PHP_SELF'], 2);
				} else {
					$cRecs = $tdb->listRec("cats", 1);
					$select = "<Select name=cat>\n";
					foreach($cRecs as $cRec) {
						if ($cRec["id"] == $fRec[0]["cat"]) $select .= "<option value='".$cRec["id"]."' selected>".$cRec["name"]."</option>";
						else $select .= "<option value='".$cRec["id"]."'>".$cRec["name"]."</option>";
					}
					$select .= "</select>";
					$whoView = "<select size='1' name='u_view'>".createUserPowerMisc($fRec[0]["view"], 1)."</select>";
					$whoPost = "<select size='1' name='u_post'>".createUserPowerMisc($fRec[0]["post"], 1)."</select>";
					$whoReply = "<select size='1' name='u_reply'>".createUserPowerMisc($fRec[0]["reply"], 1)."</select>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
					echo "
			<tr>
				<th>Admin Panel Navigation</th>
			</tr>";
					echo "
			<tr>
				<td class='area_2' style='padding:20px;' valign='top'>";
					require_once("admin_navigation.php");
					echo "</td>
			</tr>
		$skin_tablefooter";
					echo "<form action='".$_SERVER['PHP_SELF']."?action=edit&id=".$_GET["id"]."' method=POST>";
		echoTableHeading("Editing a forum", $_CONFIG);
					echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>";
					echo "
			<tr>
				<td class='area_1' style='width:20%'><strong>Name of forum</strong></td>
				<td class='area_2'><input type=text name=u_forum size='40' maxlength=50 value='".$fRec[0]["forum"]."'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Parent category</strong></td>
				<td class='area_2'>$select</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can see this forum?</strong></td>
				<td class='area_2'>$whoView</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can post in this forum?</strong></td>
				<td class='area_2'>$whoPost</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can reply in this forum?</strong></td>
				<td class='area_2'>$whoReply</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Forum description</strong></td>
				<td class='area_2'><textarea cols=30 rows=5 maxlength=50 name=des>".$fRec[0]["des"]."</textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit value='Edit'></td>
			</tr>
		";
    echoTableFooter($_CONFIG['skin_dir']);
    echo "
	</form>";
				}
			} else {
				echo "No id selected.";
			}
		} elseif($_GET["action"] == "delete") {
			//delete a forum
			if (isset($_GET["id"])) {
				if ($_POST["verify"] == "Ok") {
					$fRec = $tdb->get("forums", $_GET["id"]);
					$cRec = $tdb->get("cats", $fRec[0]["cat"]);
					$sort = explode(",", $cRec[0]["sort"]);
					for($i = 0; $i < count($sort); $i++) {
						if ($sort[$i] == $_GET["id"]) {
							unset($sort[$i]);
							break;
						}
					}
					$sort = implode(",", $sort);
					$tdb->edit("cats", $cRec[0]["id"], array("sort" => $sort));
					$tdb->delete("forums", $_GET["id"]);
					$post_tdb->removeTable($_GET["id"]);
					$post_tdb->removeTable($_GET["id"]."_topics");
					$post_tdb->cleanup();
					echo "
						<div class='alert_confirm'>
						<div class='alert_confirm_text'>
						<strong>Redirecting:</div><div style='padding:4px;'>
						Successfully deleted forum.
						</div>
						</div>";
						redirect($_SERVER['PHP_SELF'], 2);
				} elseif($verify == "Cancel") {
					redirect($_SERVER['PHP_SELF'], 0);
				} else {
					ok_cancel("admin_forum.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete this forum?");
				}
			} else {
				echo "No id selected.";
			}
		} elseif($_GET["action"] == "addnew") {
			//add new forum
			if (isset($_POST["u_forum"])) {
				$record = array(
				"forum" => $_POST["u_forum"],
					"cat" => $_POST["cat"],
					"view" => $_POST["u_view"],
					"post" => $_POST["u_post"],
					"reply" => $_POST["u_reply"],
					"des" => $_POST["des"],
					"topics" => 0,
					"posts" => 0 );
				$_GET["id"] = $tdb->add("forums", $record);
				$cRec = $tdb->get("cats", $_POST["cat"]);
				if ($cRec[0]["sort"] == "") $sort = $_GET["id"];
				else $sort = $cRec[0]["sort"].",".$_GET["id"];
				$tdb->edit("cats", $_POST["cat"], array("sort" => $sort));
				$post_tdb->createTable($_GET["id"], array(
				array("icon", "string", 10),
					array("user_name", "string", 20),
					array("date", "number", 14),
					array("message", "memo"),
					array("user_id", "number", 7),
					array("t_id", "number", 7),
					array('edited_by', 'string', 20),
					array('edited_by_id', 'number', 7),
					array('edited_date', 'number', 14),
					array("id", "id"),
					array("upload_id", "number", 10)
				));
				//chown(DB_DIR."/".$_GET["id"].".memo", "nobody");
				//chown(DB_DIR."/".$_GET["id"].".ref", "nobody");
				//chown(DB_DIR."/".$_GET["id"], "nobody");
				$post_tdb->createTable($_GET["id"]."_topics", array(
				array("icon", "string", 10),
					array("subject", "memo"),
					array("topic_starter", "string", 20),
					array("sticky", "number", 1),
					array("replies", "number", 9),
					array("locked", "number", 1),
					array("views", "number", 7),
					array("last_post", "number", 14),
					array("user_name", "string", 20),
					array("user_id", "number", 7),
					array("monitor", "memo"),
					array("p_ids", "memo"),
					array("id", "id")
				), 30);
				//chown(DB_DIR."/".$_GET["id"]."_topics.memo", "nobody");
				//chown(DB_DIR."/".$_GET["id"]."_topics.ref", "nobody");
				//chown(DB_DIR."/".$_GET["id"]."_topics", "nobody");
				echo "
					<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Redirecting:</div><div style='padding:4px;'>
					Successfully added new Forum ".$_POST["u_forum"]."
					</div>
					</div>
					";
				if ($_POST['command'] == 'Add and Add another forum to the selected Category') redirect($_SERVER['PHP_SELF'].'?action=addnew&cat_id='.$_POST['cat'], 2);
				elseif($_POST['command'] == 'Add and Add another forum') redirect($_SERVER['PHP_SELF'].'?action=addnew', 2);
				else redirect($_SERVER['PHP_SELF'], 2);
			} else {
				$cRecs = $tdb->listRec("cats", 1);
				$select = "<Select name=cat>\n";
				foreach($cRecs as $cat) {
					if (isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['id']) $select .= "<option value='".$cat["id"]."' selected>".$cat["name"]."</option>";
					else $select .= "<option value='".$cat["id"]."'>".$cat["name"]."</option>";
				}
				$select .= "</select>";
				$whoView = "<select size='1' name='u_view'>".createUserPowerMisc(0, 1)."</select>";
				$whoPost = "<select size='1' name='u_post'>".createUserPowerMisc(1, 1)."</select>";
				$whoReply = "<select size='1' name='u_reply'>".createUserPowerMisc(1, 1)."</select>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
				echo "
			<tr>
				<th>Admin Panel Navigation</th>
			</tr>";
				echo "
			<tr>
				<td class='area_2' style='padding:20px;' valign='top'>";
				require_once("admin_navigation.php");
				echo "</td>
			</tr>";
      echoTableFooter($_CONFIG['skin_dir']);
				echo "<form action='admin_forum.php?action=addnew' method=POST>";
		echoTableHeading("Creating a new forum", $_CONFIG);
				echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>";
				echo "
			<tr>
				<td class='area_1' style='width:20%'><strong>Name of new forum</strong></td>
				<td class='area_2'><input type=text name=u_forum maxlength=50 size='40'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Parent category</strong></td>
				<td class='area_2'>$select</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can see this forum?</strong></td>
				<td class='area_2'>$whoView</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can post in this forum?</strong></td>
				<td class='area_2'>$whoPost</td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can reply in this forum?</strong></td>
				<td class='area_2'>$whoReply</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Forum description</strong></td>
				<td class='area_2'><textarea cols=30 rows=5 maxlength=70 name=des></textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit value='Add'> <input type=submit name='command' value='Add and Add another forum' size='10'> <input type=submit name='command' value='Add and Add another forum to the selected Category' size='15'></td>
			</tr>
		";
    echoTableFooter($_CONFIG['skin_dir']);
    echo "</form>";
			}
		}
	} else {
		$fRecs = $tdb->listRec("forums", 1);
		if (empty($fRecs)) redirect('admin_forum.php?action=addnew', 0);
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "
			<tr>
				<th>Admin Panel Navigation</th>
			</tr>";
		echo "
			<tr>
				<td class='area_2' style='padding:20px;' valign='top'>";
		require_once("admin_navigation.php");
		echo "</td>
			</tr>";
			echoTableFooter($_CONFIG['skin_dir']);
		echo "
			<div id='tabstyle_2'>
			<ul>
			<li><a href='admin_forum.php?action=addnew' title='Add a new forum?'><span>Add a new forum?</span></a></li>
			</ul>
			</div>
			<div style='clear:both;'></div>";
		echoTableHeading("Forum Control", $_CONFIG);
		echo "
			<tr>
				<th style='width:24%;'>Forum Name</th>
				<th style='width:10%;text-align:center;'>Cat ID#</th>
				<th style='width:5%;text-align:center;'>View</th>
				<th style='width:5%;text-align:center;'>Post</th>
				<th style='width:5%;text-align:center;'>Reply</th>
				<th style='width:20%;text-align:center;'>Space in topics table</th>
				<th style='width:20%;text-align:center;'>Space in posts table</th>
				<th style='width:4%;text-align:center;'>Edit?</th>
				<th style='width:7%;text-align:center;'>Delete?</th>
			</tr>";
		foreach($fRecs as $fRec) {
			$post_tdb->setFp("topics", $fRec["id"]."_topics");
			$post_tdb->setFp("posts", $fRec["id"]);
			$t_txt = "<strong>".(round(($post_tdb->getNumberOfRecords("topics")/5000), 1)-100) * (-1)."%</strong>";
			$p_txt = "<strong>".(round(($post_tdb->getNumberOfRecords("posts")/5000), 1)-100) * (-1)."%</strong>";
			$whoView = createUserPowerMisc($fRec["view"], 3);
			$whoPost = createUserPowerMisc($fRec["post"], 3);
			$whoReply = createUserPowerMisc($fRec["reply"], 3);
			//show each category
			echo "
			<tr>
				<td class='area_2' style='padding:8px;'><strong>".$fRec["forum"]."</td>
				<td class='area_1' style='padding:8px;text-align:center;'>".$fRec["cat"]."</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoView</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoPost</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoReply</td>
				<td class='area_1' style='padding:8px;text-align:center;'>$t_txt</td>
				<td class='area_1' style='padding:8px;text-align:center;'>$p_txt</td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forum.php?action=edit&id=".$fRec["id"]."'>Edit</a></td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forum.php?action=delete&id=".$fRec["id"]."'>Delete</a></td>
			</tr>";
		}
		echoTableFooter($_CONFIG['skin_dir']);
	}
	require_once("./includes/footer.php");
?>
