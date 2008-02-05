<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_cat.php'>Manage Categories</a>";
	require_once('./includes/header.php');
	if ($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
		if ($_GET["action"] == "edit") {
			//edit categories
			if (isset($_GET["id"])) {
				if (isset($_POST["u_cat"])) {
          $tdb->edit("cats", $_GET["id"], array("name" => $_POST["u_cat"], "view" => $_POST["u_view"], "sort" => $_POST['neworder']));
					echo "
						<div class='alert_confirm'>
						<div class='alert_confirm_text'>
						<strong>Redirecting:</div><div style='padding:4px;'>
						Category successfully edited.
						</div>
						</div>";
					redirect($_SERVER['PHP_SELF'], 2);
				} else {
					$cRec = $tdb->get("cats", $_GET["id"]);
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
					echo "<form action='admin_cat.php?action=edit&id=".$_GET["id"]."' method='POST' name='form'>";
		echoTableHeading("Editing a category", $_CONFIG);
          echo "<input type=\"hidden\" name=\"neworder\" value=\"\">
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_1' style='width:35%'><strong>Change category's name to</strong></td>
				<td class='area_2'><input type='text' name='u_cat' value='".$cRec[0]["name"]."' size='40'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can see this category?</strong></td>
				<td class='area_2'><select size='1' name='u_view'>";
					echo createUserPowerMisc($cRec[0]["view"], 1);
					echo "</select></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>";
				
        $fRecs = $tdb->query("forums", "cat='".$_GET["id"]."'");
        if ($fRecs !== false)
                      {
                  echo "  <td class='area_1'><strong>Sort the Forums in this category</strong></td>
				<td class='area_2'>";
 
                    $sort = $cRec[0]["sort"];
                    $order = explode(",",$sort);

                    echo "<table><tr><td>";
                    echo "<select id='fsort' multiple name='fsort' size='".count($fRecs)."'>";
 
                    for ($i = 0;$i < count($order);$i++)
                    {
                      foreach ($fRecs as $fRec)
                       {
                         if ($fRec['id'] == $order[$i])
                          echo "<option value='".$fRec['id']."'>".$fRec['forum']."</option>";
                       }
                    }
                     echo "</select></td>";
                    echo "<td><img src='./images/up.gif' ";
     echo "onClick=\"moveOptionsUp('fsort');fsort.focus();\">&nbsp;&nbsp;&nbsp;";
     echo "<p><img src='./images/down.gif' ";
    echo "onClick=\"moveOptionsDown('fsort');fsort.focus();\"></td></tr></table>";
        
        echo "</td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='button' onClick=\"submitorderform('forum','full')\" value='Edit'></td>
			</tr>";
			}
			else
			{
      echo "<td class='area_1' colspan='2'>There are no forums in this category";
      echo "</td></tr><tr><td colspan='2'><input type='button' onClick=\"submitorderform('forum','empty')\" value='Edit'></td></tr>";
      }
			echo "$skin_tablefooter</form>";
				}
			} else {
				echo "No id selected.";
			}
		} elseif($_GET["action"] == "delete") {
			//delete categories
			if (isset($_GET["id"])) {
				if ($_POST["verify"] == "Ok") {
					$sort = explode(",", $admin_catagory_sorting);
					if (($i = array_search($_GET["id"], $sort)) !== FALSE) unset($sort[$i]);
					$config_tdb->editVars("config", array("admin_catagory_sorting" => implode(",", $sort)));
					$tdb->delete("cats", $_GET["id"]);
					echo "
						<div class='alert_confirm'>
						<div class='alert_confirm_text'>
						<strong>Redirecting:</div><div style='padding:4px;'>
						Successfully deleted category.
						</div>
						</div>
						";
					redirect($_SERVER['PHP_SELF'], 2);
				} elseif($_POST["verify"] == "Cancel") {
					redirect($_SERVER['PHP_SELF'], 0);
				} else {
					ok_cancel("admin_cat.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete a category?");
				}
			} else {
				echo "No id selected.";
			}
		} elseif($_GET["action"] == "addnew") {
			//add new category
			if (isset($_GET['a'])) {
				$cat_id = $tdb->add("cats", array("name" => $_POST["u_cat"], "view" => $_POST["u_view"]));
				echo "
					<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Successfully added new category:</strong></div><div style='padding:4px;'>
					".$_POST["u_cat"]."
					</div>
					</div>";
				if ($_POST['command'] == 'Add and Add another Category') redirect($_SERVER['PHP_SELF'].'?action=addnew', 2);
				elseif ($_POST['command'] == 'Add and Add forums to this category') redirect('admin_forum.php?action=addnew&cat_id='.$cat_id, 2);
				else redirect($_SERVER['PHP_SELF'], 2);
			} else {
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
				echo "<form action='admin_cat.php?action=addnew&a=1' method=POST>";
		echoTableHeading("Creating a new category", $_CONFIG);
				echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_1' style='width:20%'><strong>Name of new category</strong></td>
				<td class='area_2'><input type=text name=u_cat size='40'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Who can see the category?</strong></td>
				<td class='area_2'><select size='1' name='u_view'>
					".createUserPowerMisc(0, 1)."</select></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit value='Add'> <input type=submit name='command' value='Add and Add another Category'> <input type=submit name='command' value='Add and Add forums to this category'></td>
			</tr>
		$skin_tablefooter
	</form>";
			}
		} else {
			$cats = $tdb->listRec("cats", 1);
			if (empty($cats)) redirect('admin_cat.php?action=addnew', 0);
			$c = count($cats);
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
			echo "
				<div id='tabstyle_2'>
				<ul>
				<li><a href='admin_cat.php?action=addnew' title='Add a new category?'><span>Add a new category?</span></a></li>
				</ul>
				</div>
				<div style='clear:both;'></div>";
		echoTableHeading("Category Control", $_CONFIG);
			echo "
			<tr>
				<th style='width:60%;'>Category Name</th>
				<th style='width:20%;text-align:center;'>View</th>
				<th style='width:10%;text-align:center;'>Edit?</th>
				<th style='width:10%;text-align:center;'>Delete?</th>
			</tr>";
			if ($cats[0]["name"] == "") {
				echo "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='4'>No records found</td>
			</tr>";
			} else {
				foreach($cats as $cat) {
					//show each category
					$view = createUserPowerMisc($cat["view"], 2);
					echo "
			<tr>
				<td class='area_2' style='padding:8px;'><strong>".$cat["name"]."</strong></td>
				<td class='area_1' style='padding:8px;text-align:center;'>$view</td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_cat.php?action=edit&id=".$cat["id"]."'>Edit</a></td>
				<td class='area_1' style='padding:8px;text-align:center;'><a href='admin_cat.php?action=delete&id=".$cat["id"]."'>Delete</a></td>
			</tr>";
				}
			}
			echo "$skin_tablefooter";
		}
	} else {
		echo "
			<div class='alert'><div class='alert_text'>
			<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>
			<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>";
	}
	require_once("./includes/footer.php");
?>
