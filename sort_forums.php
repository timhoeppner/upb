<?php
require_once("./includes/class/func.class.php");
require_once("./includes/class/posts.class.php");
require_once("./includes/class/config.class.php");
$output = "";
if($_POST['what'] == 'cat') 
{
  $sort = $_CONFIG['admin_catagory_sorting'];
} 
elseif($_POST['what'] == 'forum') 
{
  $fRec = $tdb->get('forums', $_POST['id']);
  $cRec = $tdb->get('cats', $fRec[0]['cat']);
	$sort = $cRec[0]['sort'];
}


$sort = explode(',', $sort);

if(FALSE !== ($index = array_search($_POST['id'], $sort))) 
{
  if($_POST['where'] == 'up' && $index > 0) 
  {
    $tmp = $sort[$index-1];
    $sort[$index-1] = $sort[$index];
    $sort[$index] = $tmp;
	} 
  elseif($_POST['where'] == 'down' && $index < (count($sort)-1)) 
  {
    $tmp = $sort[$index+1];
    $sort[$index+1] = $sort[$index];
    $sort[$index] = $tmp;
	}
	$sort = implode(',', $sort);
	if($_POST['what'] == 'cat') 
  {
    $config_tdb->editVars('config', array('admin_catagory_sorting' => $sort));
  } 
  elseif($_POST['what'] == 'forum') 
  {
  $tdb->edit('cats', $cRec[0]['id'], array('sort' => $sort));
  }
}

$tdb->cleanUp();
$tdb->setFp('forums', 'forums');
$tdb->setFp('cats', 'categories');

$cRecs = $tdb->listRec("cats", 1);
$config_tdb->clearcache();
$vars = $config_tdb->getVars('config', true);
    		// Sort categories in the order that they appear
    		$cSorting = explode(",", $vars[7]['value']);
        $k = 0;
    		$i = 0;
    		$sorted = array();
    		while ($i < count($cRecs)) {
    			if ($cSorting[$k] == $cRecs[$i]["id"]) {
    				$sorted[] = $cRecs[$i];
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

  $output .= "<div class='main_cat_wrapper'>
		<div class='cat_area_1'>Forum Control</div>
		<table class='main_table' cellspacing='1'>
		<tbody>";

		    $output .= "
			<tr>
			    <th style='width:7%;'>&nbsp;</th>
				<th style='width:68%;'>Name</th>
				<th style='width:5%;text-align:center;'>View</th>
				<th style='width:5%;text-align:center;'>Post</th>
				<th style='width:5%;text-align:center;'>Reply</th>
				<th style='width:10%;text-align:center;'>Edit?</th>
				<th style='width:10%;text-align:center;'>Delete?</th>
			</tr>";
		     if ($cRecs[0]["name"] == "") {
				$output .= "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='6'>No categories found</td>
			</tr>";
			} else {
			    for($i=0,$c1=count($cRecs);$i<$c1;$i++) {
					//show each category
					$view = createUserPowerMisc($cRecs[$i]["view"], 2);
					$output .= "
			<tr>
			    <td class='area_1' style='padding:8px;'>".(($i>0) ? "<a href=\"javascript:forumSort('cat','up','".$cRecs[$i]['id']."');\"><img src='./images/up.gif'></a>&nbsp;" : "&nbsp;&nbsp;&nbsp;&nbsp;").(($i<($c1-1)) ? "<a href=\"javascript:forumSort('cat','down','".$cRecs[$i]['id']."');\"><img src='./images/down.gif'></a>" : "")."</td>
				<td class='area_1' style='padding:8px;'><strong>".$cRecs[$i]["name"]."</strong></td>
				<td class='area_1' style='padding:8px;text-align:center;' colspan=3>$view</td>
				<td class='area_1' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=edit_cat&id=".$cRecs[$i]["id"]."'>Edit</a></td>
				<td class='area_1' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=delete_cat&id=".$cRecs[$i]["id"]."'>Delete</a></td>
			</tr>";

					if($cRecs[$i]['sort'] == '') {
					   $output .= "
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='7'>No forums exist in this category yet.</td>
			</tr>";
					} else {
					    $ids = explode(',', $cRecs[$i]['sort']);
					    for($j=0,$c2=count($ids);$j<$c2;$j++) {
					       $fRec = $tdb->get('forums', $ids[$j]);
                			//$post_tdb->setFp("topics", $fRec[0]["id"]."_topics");
                			//$post_tdb->setFp("posts", $fRec[0]["id"]);
                			$whoView = createUserPowerMisc($fRec[0]["view"], 3);
                			$whoPost = createUserPowerMisc($fRec[0]["post"], 3);
                			$whoReply = createUserPowerMisc($fRec[0]["reply"], 3);
                			//show each forum
                			$output .= "
			<tr>
			    <td class='area_2' style='padding:8px;text-align:center;'>".(($j>0) ? "<a href=\"javascript:forumSort('forum','up','".$fRec[0]['id']."');\"><img src='./images/up.gif'></a>" : "&nbsp;&nbsp;&nbsp;").(($j<($c2-1)) ? "<a href=\"javascript:forumSort('forum','down','".$fRec[0]['id']."');\"><img src='./images/down.gif'></a>" : "")."</td>
				<td class='area_2' style='padding:8px;'><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$fRec[0]["forum"]."</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoView</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoPost</td>
				<td class='area_2' style='padding:8px;text-align:center;'>$whoReply</td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=edit_forum&id=".$fRec[0]["id"]."'>Edit</a></td>
				<td class='area_2' style='padding:8px;text-align:center;'><a href='admin_forums.php?action=delete_forum&id=".$fRec[0]["id"]."'>Delete</a></td>
			</tr>";
					    }
					}
				}
			}
    		$output .= "
		</tbody>
		</table>
		<div class='footer'><img src='".$_CONFIG['skin_dir']."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";
	echo $output;
?>
