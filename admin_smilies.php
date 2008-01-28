<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");

$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_smilies.php'>Manage Smilies</a>";
require_once('./includes/header.php');
$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
  $bdb->setFP('smilies','smilies');
if(!(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"]))) {
	echo "you are not even logged in";
	redirect("login.php?ref=admin_smilies.php", 2);
}

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

//REMOVE ALL TRACES OF $_GET['word']
if(!($tdb->is_logged_in() && $_COOKIE["power_env"] == 3)) exitPage("you are not authorized to be here.");
if($_GET["action"] == "addnew") {
	
  if($_POST["replace"] != "") {
		echo "adding new smilie...";
		//ADD SMILIE GOES HERE........UPLOAD FILE FORM NEEDED
		redirect("admin_smilies.php", 1);
	} else {
    echo "<form action='admin_cat.php?action=addnew' method=POST>";
		echoTableHeading("Add a new smilie", $_CONFIG);
				echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_1' style='width:20%'><strong>Smilie file</strong></td>
				<td class='area_2'><input type='file' name='smilie_file'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Text Replaced</strong></td>
				<td class='area_2'><select size='1' name='u_view'>
					".createUserPowerMisc(0, 1)."</select></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Display Type</strong></td>
				<td class='area_2'><select size='1' name='disp_type'>
        <option value='main' selected>Main</option><option value='more'>More</option></select>
			</td></tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit value='Add'> <input type=submit name='command' value='Add and Add another Category'> <input type=submit name='command' value='Add and Add forums to this category'></td>
			</tr>
		$skin_tablefooter
	</form>";
    //ADD SMILIE FORM
    ?> <form action="admin_smilies.php?action=addnew" method=POST>
                New smilie: <input type="text" name="newsmilie" size="20">
                <input type="text" name="replace" size="30">
                <input type="submit" value="Add">
                </form><?php
	}
} 
elseif($_GET["action"] == "edit") 
{
  
  $tmp = $tmp2 = $tmp3 = array();
  echo "<pre>";
  var_dump($_POST);
  echo "<pre>";
  
  //process the data for each id to get an array of values for each id
  foreach ($_POST as $key=>$value)
  {
    $tmp_key = explode("_",$key);
    if ($tmp_key[1] != "delete")
      $tmp[$tmp_key[0]][$tmp_key[1]] = $value;
    
    if ($tmp_key[1] == "delete")
    {
      unset($tmp[$tmp_key[0]]);
      $data = $bdb->query('smilies', "id='{$tmp_key[0]}'", 1, 1, array('replace'));
      $file = $data[0]['replace'];
      $newfile = strmstr(strstr_after($file, "'"),"'",true);
      unlink('./'.$newfile);
      $bdb->delete('smilies',$tmp_key[0]);
    }
  }
  
  foreach ($tmp as $key=>$value)
  {
    $result = $bdb->basicQuery("smilies","id",$key);
    unset($result[0]['replace']);
    //$tmp2[$result[0]['id']] = array('bbcode'=>$result[0]['bbcode'],'type'=>$result[0]['type']);
    $tmp2 = array('bbcode'=>$result[0]['bbcode'],'type'=>$result[0]['type']);
    $diff = array_diff_assoc($value,$tmp2);
    var_dump($diff);
    if (count($diff) == 0)
      continue;
    else
    {
      $bdb->edit('smilies',$key,$diff);
      echo "\$bdb->edit('smilies',$key,$diff)<br>";
    }
  }
  echo "Smilie database edited....";
  require_once('./includes/footer.php');
  //redirect("admin_smilies.php", 3);
}
else {
	$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
  $bdb->setFP('smilies','smilies');
  $smilies = $bdb->query('smilies',"id>'0'");
  //var_dump($smilies);
  
		
  echo "
				<div id='tabstyle_2'>
				<ul>
				<li><a href='admin_smilies.php?action=addnew' title='Add a new smilie?'><span>Add a new smilie?</span></a></li>
				</ul>
				</div>
				<div style='clear:both;'></div>";
		echoTableHeading("Smilie Control", $_CONFIG);
echo "<tr><th colspan='4'>Smilie Management</th>";
	if(count($smilies) == 0 or $smilies === false) {
		echo "<tr><td bgcolor='$table1' colspan='4'><font size='$font_m' face='$font_face' color='$font_color_main'>No smilies found.</font></td></tr>";
	} else {
		echo "<tr><td class='area_2' style='padding:8px;' colspan='4'>
    <ul><li>Select <strong>Main</strong> to display the smilie in the box below the message box<li>Select <strong>More</strong> to show the smilie on the <strong>More Smilies</strong> page</ul></td></tr>";
    echo "<form name='smilieupdate' method='POST' action='admin_smilies.php?action=edit'>";
    echo "
			<tr>
				<th style='width:40%;'>Smilie</th>
				<th style='width:35%;text-align:center;'>Text Replaced</th>
				<th style='width:15%;text-align:center;'>Display Type</th>
				<th style='width:10%;text-align:center;'>Delete?</th>
			</tr>";
    
    $types = array('main','more');
    foreach ($smilies as $smiley)
    {
			$id = $smiley['id'];
      echo "<tr><td class='area_2' style='padding:8px;text-align:center;'>".$smiley['replace']."</td>\n";
      echo "<td class='area_1' style='padding:8px;text-align:center;'><input type='text' size='40' name='{$id}_bbcode' value='".$smiley['bbcode']."'></font></td>\n";        
      echo "<td class='area_2' style='padding:8px;text-align:center;'>";
      //echo $smiley['type'];
      echo "<input type='radio' name='{$id}_type' value='main'";
      if ($smiley['type'] == "main")
        echo " checked";
      echo ">Main";
      echo "<input type='radio' name='{$id}_type' value='more'";
      if ($smiley['type'] == "more")
        echo " checked";
      echo ">More";
      /*echo "<select name='{$id}_type' size='1'>";
      foreach ($types as $type)
      {
        echo "<option value='$type'";
        if ($type == $smiley['type'])
        {
          echo " selected ";
        }
        echo ">".ucwords($type)."</option>";
      }
      echo "</select>";*/
      echo "</td>\n";
      echo "<td class='area_1' style='padding:8px;text-align:center;'><input type='checkbox' name='{$id}_delete'>";
      echo "</tr>\n";
		}
		echo "<tr><td class='area_1' colspan='4' style='padding:8px;text-align:center;'><input type='submit' value='Submit Changes'><input type='reset' value='Reset Form'></td></tr>";
	}
	echo "</table>$skin_tablefooter";
}
require_once("./includes/footer.php");
?>
