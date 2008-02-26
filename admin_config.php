<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_config.php'>Config Settings</a>";
require_once('./includes/header.php');
if($_GET['action'] == '') $_GET['action'] = 'config';

if(isset($_COOKIE["power_env"]) && isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["id_env"])) {
	if($tdb->is_logged_in() && $_COOKIE["power_env"] >= 3) {
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
			</tr>";
    echoTableFooter($_CONFIG['skin_dir']);

		echo "<a name='skip_nav'>&nbsp;</a>
			<div id='tabstyle_2'>
			    <ul>";

		$f = fopen(DB_DIR."/config_org.dat", 'r');
		$raws = fread($f, filesize(DB_DIR."/config_org.dat"));
		$raws = explode(chr(29), $raws);
		$raws1 = explode(chr(31), rtrim($raws[0], chr(31)));

		for($i=0, $max=count($raws1);$i<$max;$i++) {
			$rec = explode(chr(30), $raws1[$i]);
			print "
			        <li><a href='admin_config.php?action=".$rec[0]."' title='".$rec[1]."'><span>".$rec[1]."</span></a></li>";
		}
		echo "
			        <li><a href='admin_config.php?action=installation_mode' title='Toggle Installation Mode'><span>Installation Mode</span></a></li>
					    </ul>
			</div>
			<div style='clear:both;'></div>";

		if($_GET["action"] == "installation_mode") {
			echoTableHeading('Installation Mode Interface', $_CONFIG);
			if(isset($_POST['a'])) {
			    if($_POST['a'] == 'Turn Off') {
			        $newline = "define('INSTALLATION_MODE', false, true);\n";
			    } elseif($_POST['a'] == 'Turn On') {
			        $newline = "define('INSTALLATION_MODE', true, true);\n";
			    }
			    $file = file('config.php');
			    for($i=0,$c=count($file);$i<$c;$i++) {
			        if(strpos($file[$i], 'INSTALLATION_MODE')) $file[$i] = $newline;
			    }
			    $file = implode('', $file);
			    if(FALSE !== ($f = @fopen('config.php', 'w'))) {
    			    fwrite($f, $file);
    			    fclose($f);
    			    print "Successfully toggled the installation mode for the board";
    			    redirect('admin_config.php?action=installation_mode#skip_nav', 0);
			    } else {
			        print "Unable to edit config.php.  Make sure permissions are set correctly (644)";
			    }
			} else {
                print '<form action="admin_config.php?action=installation_mode" method="POST">';
    		    if(INSTALLATION_MODE) {
                    print '<div class="area_2" style="text-align:center;"><strong>Installation Mode is currently in effect<p><input type="submit" name="a" value="Turn Off"></strong></div>';
    		    } else {
    		        print '<div class="area_2" style="text-align:center;"><strong>Installation Mode is currently in off<p><input type="submit" name="a" value="Turn On"></strong></div>';
    		    }
    		    print '</form>';
			}
			echoTableFooter(SKIN_DIR);
		} else {
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
						if($configVars[$i]["minicat"] == $rec[1] && $configVars[$i]["sort"] == $j && $configVars[$i]["form_object"] != "hidden" && $configVars[$i]["name"] != "admin_catagory_sorting") {
							echo "
			<tr>
				<td class='area_1' style='width:35%;padding:8px;'><strong>".$configVars[$i]["title"]."</strong>";
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
								  $contents = array(); //array for valid skin directories
                  if (is_dir($sdir))
                  {
                    $dir = directory($sdir);
                    foreach ($dir as $subdir)
                    {
                      if (is_dir($sdir.$subdir))
                      {
                        if (file_exists($sdir.$subdir.'/index.html'))
                          $contents[] = $subdir;
                      }
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
                  break 1;
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

      echo "<input type=submit value='Edit'>";
      echo "</td>
			</tr>";
echoTableFooter($_CONFIG['skin_dir']);
echo "</form>";

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
