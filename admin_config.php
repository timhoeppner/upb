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
            //print_r($_POST);
            if($config_tdb->editVars($_POST["action"], $_POST)) echo "Edit Successful";
            else echo "<font color='red'><b>Edit Failed</b></font>";
            require_once("./includes/footer.php");
            redirect($PHP_SELF."?action=".$_POST["action"], 2);
            die();
        }
        
        echo "<center>";
        echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
        echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>";
        echo "<tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
        echo "<tr><td colspan='3' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>";
        
        echo "<tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='2' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Configuration Settings</font></b></td></tr>";
        echo "<tr><td bgcolor='$table1' width=50%><font size='$font_m' face='$font_face' color='$font_color_main'>";
        
        $f = fopen(DB_DIR."/config_org.dat", 'r');
        $raws = fread($f, filesize(DB_DIR."/config_org.dat"));
        $raws = explode(chr(29), $raws);
        $raws1 = explode(chr(31), rtrim($raws[0], chr(31)));

        for($i=0, $max=count($raws1), $howmanyInCol = ceil(count($raws1)/2);$i<$max;$i++) {
            if($i == $howmanyInCol) echo "</font></td><td bgcolor='$table1' width=50%><font size='$font_m' face='$font_face' color='$font_color_main'>";
            $rec = explode(chr(30), $raws1[$i]);
            echo "<a href=\"admin_config.php?action=".$rec[0]."\">".$rec[1]."</a><br>";
        }
        echo "</font></td></tr></table>";
        
        echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>";
        if($_GET["action"] == "Installation Mode") {
            //Insert coding here
        } elseif($_GET["action"] != "") {
            $raws2 = explode(chr(31), $raws[1]);
            $configVars = $config_tdb->getVars($_GET["action"], true);
            echo "<form action=\"admin_config.php?action=".$_GET["action"]."\" method='POST' name='form'>
            <input type='hidden' name='action' value='".$_GET["action"]."'>";
            echo "<input type=\"hidden\" name=\"neworder\" value=\"\">";
            foreach($raws2 as $raw) {
                $rec = explode(chr(30), $raw);
                if($rec[0] == $_GET["action"]) {
                    echo "<tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>".$rec[2]."</font></b></td></tr>";
                    for($i=0, $j=1, $max=count($configVars);$j<$max;$i++) {
                        if($i>$max) { $j++; $i=-1; }//Current Sorting Rec not found after cycling through all available recs, skipping on to find the next sorting rec
                        if($configVars[$i]["minicat"] == $rec[1] && $configVars[$i]["sort"] == $j && $configVars[$i]["form_object"] != "hidden") {
                            echo "<tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>".$configVars[$i]["title"]."</b>";
                            if($configVars[$i]["description"] != "") echo "<br><font size='$font_s'>".$configVars[$i]["description"]."</font>";
                            echo "</font></td><td bgcolor='$table1' width=10%>";
                        
                            switch($configVars[$i]["form_object"]) {
                                case "text":
                                echo "<input type=\"text\" name=\"".$configVars[$i]["name"]."\" value=\"".$configVars[$i]["value"]."\" size='40'>";
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
                                case "list":
                                $sort = $_CONFIG['admin_catagory_sorting'];
                                $order = explode(",",$sort);
                                $cRecs = $tdb->listRec("cats", 1);
                                
                                //var_dump($sort);
                                
                                echo "<select multiple name=\"".$configVars[$i]["name"]."\" size=\"".count($cRecs)."\">";
                                for ($i = 0;$i < count($order);$i++)
                                {
                                  foreach ($cRecs as $cRec)
                                  {
                                    if ($cRec['id'] == $order[$i])
                                    {
                                      echo "<option value='".$cRec['id']."'>".$cRec['id']."::".$cRec['name']."</option>";
                                      $added[] = $cRec['id'];
                                    }
                                  }
                                }
                                
                                echo "</select><br>";
                               
                                echo "<input type=\"button\" value=\"Move Up\" ";
                                echo "onClick=\"change_order(this.form.admin_catagory_sorting.selectedIndex,-1,'category')\">&nbsp;&nbsp;&nbsp;";
                                echo "<input type=\"button\" value=\"Move Down\"";
                                echo "onClick=\"change_order(this.form.admin_catagory_sorting.selectedIndex,+1,'category')\">";
                                break 1;
                            }
                            echo "</td></tr>";
                            $i = -1;
                            $j++;
                        }
                    }
                }
                echo "</tr>
                ";
            }
            echo "<tr><td bgcolor='$table1' width=80% colspan=2><input type=button onClick=\"submitorderform('category')\" value='Edit'></form></td></tr>";

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
        echo "</table>".$skin_tablefooter;
    } else {
        echo "you are not authorized to be here.";
    }
} else {
    echo "you are not logged in<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>";
}
require_once("./includes/footer.php");
?>
