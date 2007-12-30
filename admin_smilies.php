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

//REMOVE ALL TRACES OF $_GET['word']
if(!($tdb->is_logged_in() && $_COOKIE["power_env"] == 3)) exitPage("you are not authorized to be here.");
if($_GET["action"] == "add") {
	
  if($_POST["newword"] != "") {
		echo "adding new smilie...";
		//ADD SMILIE GOES HERE........UPLOAD FILE FORM NEEDED
		redirect("admin_smilies.php", 1);
	} else {
		//ADD SMILIE FROM
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
    if (count($diff) == 0)
      continue;
    else
      $bdb->edit('smilies',$key,$diff);
  }
  
  echo "Smilie database edited....";
  require_once('./includes/footer.php');
  redirect("admin_smilies.php", 3);
}
else {
	$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
  $bdb->setFP('smilies','smilies');
  $smilies = $bdb->query('smilies',"id>'0'");
  //var_dump($smilies);
  echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
  echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

            <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='4' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
	echo "<tr><td colspan='4' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

<tr><td colspan='4' bgcolor='$header'><table width=100% cellspacing=0 cellpadding=0><tr><td><font size='$font_l' face='$font_face' color='$font_color_header'><b>Manage Smilies</b></font></td><td align=right><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_smilies.php?action=add'><img src='images/add.jpg' border='0'></a></font></td></tr></table></td></tr>";
	if(count($smilies) == 0 or $smilies === false) {
		echo "<tr><td bgcolor='$table1' colspan='4'><font size='$font_m' face='$font_face' color='$font_color_main'>No smilies found.</font></td></tr>";
	} else {
		echo "<tr><td bgcolor='$table1' colspan='4'><font size='$font_m' face='$font_face' color='$font_color_main'>BBcode is the text that the smilie will replace.<br>There are three display types:
    <ul><li>Main displays the smilie in the box below the message box<li>More means the smilie will appear on the more smilies page<li>None means the smilie will not appear anywhere but the database entry and file will remain</ul>To completely remove a smilie select Yes in the Delete column. This will not only remove the information from the database but also remove the image file.</font></td></tr>";
    echo "<form name='smilieupdate' method='POST' action='admin_smilies.php?action=edit'>";
    echo "<tr><td bgcolor='$header'><font size='$font_l' face='$font_face' color='$font_color_header'>Smilie</td><td bgcolor='$header'><font size='$font_l' face='$font_face' color='$font_color_header'>BBcode</td><td bgcolor='$header'><font size='$font_l' face='$font_face' color='$font_color_header'>Display</td><td bgcolor='$header'><font size='$font_l' face='$font_face' color='$font_color_header'>Delete?</td>";
    $types = array('main','more','none');
    foreach ($smilies as $smiley)
    {
			$id = $smiley['id'];
      echo "<tr><td bgcolor='$table1' width='40%'><font size='$font_m' face='$font_face' color='$font_color_main'>".$smiley['replace']."</td>\n";
      echo "<td bgcolor='$table1' width='40%'><input type='text' size='50' name='{$id}_bbcode' value='".$smiley['bbcode']."'></font></td>\n";        
      echo "<td bgcolor='$table1' width=10%>";
      echo "<select name='{$id}_type' size='1'>";
      foreach ($types as $type)
      {
        echo "<option value='$type'";
        if ($type == $smiley['type'])
        {
          echo " selected ";
        }
        echo ">".ucwords($type)."</option>";
      }
      echo "</select>";
      echo "</td>\n";
      echo "<td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><input type='checkbox' name='{$id}_delete'>";
      echo "</tr>\n";
		}
		echo "<tr><td bgcolor='$table1' colspan='4' align='center'><input type='submit' value='Submit Changes'><input type='reset' value='Reset Form'></td></tr>";
	}
	echo "</table>$skin_tablefooter";
}
require_once("./includes/footer.php");
?>
