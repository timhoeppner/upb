<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");

$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_icons.php'>Manage Post Icons</a>";
require_once('./includes/header.php');
$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
$bdb->setFP('icons','icons');
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
if($_GET["action"] == "addnew") 
{
  $error = "";

  if($_FILES["icon_file"]['name'] != "") 
  {
    if ($_FILES["icon_file"]["type"] != "image/gif")
      $error .= "Error: File must be a gif file ".$_FILES["icon_file"]["type"]."<br />";
    
    if ($_FILES['icon_file']['size'] > 3072 or ($_FILES["icon_file"]["error"] > 0 and $_FILES["icon_file"]["error"] < 3))
      $error .= "Error: File size must be under 3KB<br />";
    
    if ($_FILES["icon_file"]["error"] > 2)
      $error .= "File Upload Error: " . $_FILES["icon_file"]["error"] . "<br />";

    if ($error != "")
    {
      echo "<div class='alert'>
			<div class='alert_text'>
			<strong>File Upload Error</strong></div><div style='padding:4px;'>$error<P><a href='admin_icons.php?action=addnew'>Back to upload form</a></div>
			</div>";
    }
    else
    {
      $upload_dir = "./icon/";
      $upload_filename = $upload_dir.basename($_FILES['icon_file']['name']);
      if (@move_uploaded_file($_FILES['icon_file']['tmp_name'], $upload_filename)) 
      {
        $bdb->add('icons',array("filename"=>$_FILES['icon_file']['name']));
        
        echo "<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Post Icon Upload Successful</strong></div>
          <div style='padding:4px;'>The Post Icon has been uploaded and is available for use.
					</div>
					</div>";
          redirect("admin_icons.php", 2);
      } 
      else 
      {
        echo "<div class='alert'>
			<div class='alert_text'>
			<strong>File Upload Error</strong></div><div style='padding:4px;'>Post Icon was unable to be saved.<br>Please check the permissions for the 'icon' directory. It should be 777<p><a href='admin_icons.php?action=addnew'>Back to upload form</a></div>
			</div>";
      }
     
    }
  } 
  else 
  {
    echo "<form action='admin_icons.php?action=addnew' method='POST' enctype='multipart/form-data'>";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='3072' />";
    echoTableHeading("Add a new post icon", $_CONFIG);
				echo "<tr><th colspan='2'>Post Icon File Requirements</th>";
        echo "<tr><td class='area_2' style='padding:8px;' colspan='2'>Post Icons must be gif files and have a maximum filesize of 3KB</td></tr>";
        echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_1' style='width:20%'><strong>Post Icon file</strong></td>
				<td class='area_2'><input type='file' name='icon_file'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type=submit value='Add Post Icon'></td>
			</tr>
		$skin_tablefooter
	</form>";
	}
} 
else if ($_GET['action'] == 'delete')
{
  $delete_array = array();
  foreach ($_POST as $value)
  {
    $delete_array[] = $value;
  }
  
  $icon_dir = './icon/';
  
  foreach ($delete_array as $value)
  {
    $result = $bdb->basicQuery('icons','id',$value);
    
    $icon_file = $result[0]['filename'];
    if (@file_exists($icon_dir.$icon_file))
    {
      if (@unlink($icon_dir.$icon_file))
      {
        $bdb->delete('icons',$value);
        echo "<div class='alert_confirm'>
					<div class='alert_confirm_text'>
					<strong>Post Icon Deletion Successful</strong></div>
          <div style='padding:4px;'>The Post Icon(s) has been deleted.
					</div>
					</div>";
        redirect("admin_icons.php", 2);
      }
      else
      {
      echo "<div class='alert'>
			<div class='alert_text'>
			<strong>Post Icon Deletion Error</strong></div><div style='padding:4px;'>There was a problem deleting the icon(s).<br>Please check the permissions for the 'icon' directory. It should be 777<p><a href='admin_icons.php'>Back to post icons</a></div>
			</div>";
      }
    }
    else
    {
      echo "<div class='alert'>
			<div class='alert_text'>
			<strong>Post Icon Deletion Error</strong></div><div style='padding:4px;'>The file for the icon could not be found.<p>The database entry for this icon has been removed.<p><a href='admin_icons.php'>Back to post icons</a></div>
			</div>";
			$bdb->delete('icons',$value);
    } 
  }
}
else {
  $icons = $bdb->query('icons',"id>'0'");
  //var_dump($smilies);
  
		
  echo "
				<div id='tabstyle_2'>
				<ul>
				<li><a href='admin_icons.php?action=addnew' title='Add a new post icon?'><span>Add a new post icon?</span></a></li>
				</ul>
				</div>
				<div style='clear:both;'></div>";
		echoTableHeading("Post Icon Control", $_CONFIG);
echo "<tr><th colspan='4'>Post Icon Management</th>";
		echo "<tr><td class='area_2' style='padding:8px;' colspan='4'>
    There must always be at least one post icon.</td></tr>";
    echo "<form name='iconupdate' method='POST' action='admin_icons.php?action=delete'>";
    echo "
			<tr>
				<th style='width:25%;'>Post Icon</th>
				<th style='width:25%;text-align:center;'>Delete?</th>
				<th style='width:25%;text-align:center;'>Post Icon</th>
				<th style='width:25%;text-align:center;'>Delete?</th>
			</tr>";
    
    echo "<tr>";
    foreach ($icons as $key => $icon)
    {
      $id = $icon['id'];
      echo "<td class='area_2' style='padding:8px;text-align:center;'><img src='./icon/".$icon['filename']."' border='0'></td>\n";
      echo "<td class='area_1' style='padding:8px;text-align:center;'>";
      if (count($icons) > 1)
        echo "<input type='checkbox' name='{$id}_delete' value='$id'>";
      echo "</td>\n";             
      
      if (($key+1)%2 == 0)
        echo "</tr><tr>";
		}
		if (count($icons)%2 != 0)
		{
      echo "<td class='area_2' style='padding:8px;text-align:center;'></td>\n";
      echo "<td class='area_1' style='padding:8px;text-align:center;'></td>\n";
    }
    echo "</tr>\n";
    echo "<tr><td class='area_1' colspan='4' style='padding:8px;text-align:center;'><input type='submit' value='Submit Changes'><input type='reset' value='Reset Form'></td></tr>";
	
	echo "</table>$skin_tablefooter";
}
require_once("./includes/footer.php");
?>
