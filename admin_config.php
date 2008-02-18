<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_config.php'>Config Settings</a>";
require_once('./includes/header.php');

if(isset($_COOKIE["power_env"]) && isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["id_env"])) {
	if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
		if($_POST["action"] != "") {
			if(file_exists('./includes/admin/'.$_POST['action'].'.config.php')) include('./includes/admin/'.$_POST['action'].'.config.php');
      if($result = $config_tdb->editVars($_POST["action"], $_POST)) 
      {
        echo "
	<div class='alert_confirm'>
		<div class='alert_confirm_text'>
		<strong>Redirecting:</div><div style='padding:4px;'>
		Successfully edited.
		</div>
	</div>";
			
      }
      else echo "
<div class='alert'><div class='alert_text'>
<strong>Error!</strong></div><div style='padding:4px;'>Edit Failed.</div></div>";
			require_once("./includes/footer.php");
			redirect($PHP_SELF."?action=".$_POST["action"], 2);
      die();
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

		echoTableHeading("Configuration areas", $_CONFIG);
		
		echo "
			<tr>
				<th colspan='2'>In these areas you can set the core configuration settings for your board.</th>
			</tr>";

		echo "
			<tr>
				<td class='area_2' style='width:50%;text-align:center;padding:12px;line-height:20px;' valign='top'><span class='link_1'>";
		
		$f = fopen(DB_DIR."/config_org.dat", 'r');
		$raws = fread($f, filesize(DB_DIR."/config_org.dat"));
		$raws = explode(chr(29), $raws);
		$raws1 = explode(chr(31), rtrim($raws[0], chr(31)));

		for($i=0, $max=count($raws1), $howmanyInCol = ceil(count($raws1)/2);$i<$max;$i++) {
			if($i == $howmanyInCol) echo "</span></td>
				<td class='area_2' style='width:50%;text-align:center;padding:12px;line-height:20px;' valign='top'><span class='link_1'>";
			$rec = explode(chr(30), $raws1[$i]);
			echo "<a href=\"admin_config.php?action=".$rec[0]."\">".$rec[1]."</a><br>";
		}
		echo "</span></td>
			</tr>
$skin_tablefooter";
		
		if($_GET["action"] == "Installation Mode") {
			//Insert coding here
		} elseif($_GET["action"] != "") {
			$raws2 = explode(chr(31), $raws[1]);
			$configVars = $config_tdb->getVars($_GET["action"], true);
			//dump($configVars);
      echo "<form action=\"admin_config.php?action=".$_GET["action"]."\" method='POST' name='form'><input type='hidden' name='action' value='".$_GET["action"]."'>";
			echo "<input type=\"hidden\" name=\"neworder\" value=\"\">";
		echoTableHeading("&nbsp;", $_CONFIG);
			foreach($raws2 as $raw) {
				$rec = explode(chr(30), $raw);
				if($rec[0] == $_GET["action"]) {

					echo "
			<tr>
				<th colspan='2'>".$rec[2]."</th>
			</tr>";
					for($i=0, $j=1, $max=count($configVars);$j<$max;$i++) {
						if($i>$max) { $j++; $i=-1; }//Current Sorting Rec not found after cycling through all available recs, skipping on to find the next sorting rec
						if($configVars[$i]["minicat"] == $rec[1] && $configVars[$i]["sort"] == $j && $configVars[$i]["form_object"] != "hidden") {
							echo "
			<tr>
				<td class='area_1' style='width:35%;padding:8px;'>".$configVars[$i]["sort"]."<strong>".$configVars[$i]["title"]."</strong>";
							if($configVars[$i]["description"] != "") echo "<br />".$configVars[$i]["description"]."";
							echo "</td>
				<td class='area_2'>";
						
							switch($configVars[$i]["form_object"]) {
								case "text":
								echo "<input type=\"text\" name=\"".$configVars[$i]["name"]."\" value=\"".stripslashes($configVars[$i]["value"])."\" size='40'>";
								break 1;
								case "password":
								echo "<input type=\"password\" name=\"".$configVars[$i]["name"]."\" value=\"".$configVars[$i]["value"]."\" size='40'>";
								break 1;
								case "checkbox":
								echo $configVars[$i]["value"];
                if((bool) $configVars[$i]["value"]) $checked = " checked";
								else $checked = "";
								echo "<input type=\"checkbox\" name=\"".$configVars[$i]["name"]."\" value=\"1\" size='40'".$checked.">";
								break 1;
								case "textarea":
								echo "<textarea cols=30 rows=10 name=\"".$configVars[$i]["name"]."\">".$configVars[$i]["value"]."</textarea>";
								break 1;
								case "link":
								case "url":
								case "URL":
								if($configVars[$i]["data_type"] != "") $target = " target=\"".$configVars[$i]["data_type"]."\"";
								else $target = "";
								echo "<a href=\"".$configVars[$i]["value"]."\"".$target.">".$configVars[$i]["name"]."</a>";
								break 1;
								
								case "drop":
								  $sdir = './skins/';
								  $contents = array(); //array of valid skin directories
                  if (is_dir($sdir)) 
                  {
                    if ($dh = opendir($sdir)) 
                    {
                      while (($file = readdir($dh)) !== false) 
                      {
                        echo "filename: $file : filetype: " . filetype($sdir . $file) . "\n<br>";
                        if ($file != "." and $file != ".." and is_dir($sdir.$file))
                        {
                          if (file_exists($sdir.$file.'/index.html'))
                            $contents[] = $file;
                        }
                      }   
                      closedir($dh);
                    }
                  }

                  echo "<select id='".$configVars[$i]["name"]."' name=\"".$configVars[$i]["name"]."\" size=\"1\">";
								  
                  $explode = explode("/",$configVars[$i]["value"]);
                  $current = array_reverse($explode);
                  
                  echo "<option value='".$sdir.$current[0]."' selected>".ucwords($current[0])."</option>"; 
                  foreach ($contents as $skindir)
								  {
                    if ($sdir.$skindir != $configVars[$i]["value"])
                      echo "<option value='".$sdir.$skindir."'>".ucwords($skindir)."</option>";
                  }
                  echo "</select>";
                  echo $configVars[$i]["value"];
                  break 1;
								
								case "list":
                  
                  $cRecs = $tdb->listRec("cats", 1);
                  if ($cRecs === false)
                  {
                    echo "No categories have been added yet";
                    break 1;
                  }
                  else
                  {
                    
                    $sort = $_CONFIG['admin_catagory_sorting'];
                    $order = explode(",",$sort);
                    
                    if ($sort == "") //check if config is set correctly if not use data from database
                    {
                      $order = array();
                      foreach ($cRecs as $value)
                      {
                        $order[] = $value['id'];
                      }
                    }
                    
                    // UN-COMMENT IF CATEGORY SORT DATA GETS CORRUPTED DURING TESTING    
                    //echo "cRecs ^".dump($cRecs)."<p>";
                    
                    //echo "order ^".dump($order)."<p>";
                    
                    //checks to make sure all categories are available for sorting
                    if (count($cRecs) > count($order)) 
                    {
                      foreach ($cRecs as $value)
                      {
                        
                        if (!in_array($value['id'],$order))
                          $order[] = $value['id'];
                      }
                    }
                            
                  echo "<table border='0'>";
                  echo "<tr><td rowspan='2'>";
                  echo "<select id='".$configVars[$i]["name"]."' multiple name=\"".$configVars[$i]["name"]."\" size=\"".count($cRecs)."\">";
                  for ($k = 0;$k < count($order);$k++)
                  {
                    foreach ($cRecs as $cRec)
                    {
                      if ($cRec['id'] == $order[$k])
                      {
                        echo "<option value='".$cRec['id']."'>".$cRec['name']."</option>";
                      }
                    }
                  }
                  echo "</select></td></tr>";
                  echo "<tr><td><br><img src='./images/up.gif' ";
                  echo "onClick=\"moveOptionsUp('".$configVars[$i]["name"]."');".$configVars[$i]["name"].".focus()\"><p>";
                  echo "<img src='./images/down.gif' ";
                  echo "onClick=\"moveOptionsDown('".$configVars[$i]["name"]."');".$configVars[$i]["name"].".focus()\"></td></tr></table>";
                  break 1;
                  }
							}
							echo "</td>
			</tr>";
							$i = -1;
							$j++;
						}
					}
				}
			}
echo "		<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>";
			echo "
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'>";
      
      if ($cRecs === false)
        echo "<input type=button onClick=\"submitorderform('category','empty')\" value='Edit'>";
      else
        echo "<input type=button onClick=\"submitorderform('category','full')\" value='Edit'>";
      echo "</td>
			</tr>
$skin_tablefooter</form>";

/*
print '<pre>'; print_r($configVars);
$all_config = $config_tdb->query("config", "type='".$_GET['action']."'");
echo '

<b>Basic</b>:

';
print_r($all_config);
$all_config = $config_tdb->query("ext_config", "type='".$_GET['action']."'");
echo '

<b>Extensive</b>:

';
print_r($all_config);
$all_config = $config_tdb->listRec("ext_config", 1, -1);
echo '

<b>All Ext</b>:

';
print_r($all_config);
print '</pre>';
*/
		}
	} else {
		echo "
<div class='alert'><div class='alert_text'>
<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not authorized to be here.</div></div>";
	}
} else {
	echo "
<div class='alert'><div class='alert_text'>
<strong>Access Denied!</strong></div><div style='padding:4px;'>you are not logged in.</div></div>
<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>";
}
require_once("./includes/footer.php");
?>
