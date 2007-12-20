<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_cat.php'>Manage Categories</a>";

require_once('./includes/header.php');

if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
        if($_GET["action"] == "edit") {
            //edit categories
            if(isset($_GET["id"])) {
                if(isset($_POST["u_cat"])) {
                    $newlist = explode("&list",$_POST['neworder']);
                    array_shift($newlist);
                    $u_sort = "";
                    foreach ($newlist as $key => $value)
                    {
                      list($id,$title) = explode("=",$value);
                      list($catid,$name) = explode("::",$title);
                      $u_sort .= $catid;
                      if ($key < count($newlist)-1)
                        $u_sort .= ",";
                    }
                    
                    $tdb->edit("cats", $_GET["id"], array("name" => $_POST["u_cat"], "view" => $_POST["u_view"], "sort" => $u_sort));
                    echo "Category successfully edited.";
                    redirect($_SERVER['PHP_SELF'], 2);
                } else {
                    $cRec = $tdb->get("cats", $_GET["id"]);
                    echo "<form action='admin_cat.php?action=edit&id=".$_GET["id"]."' method='POST' name='form'>
                    <table align='center'>
                    <input type=\"hidden\" name=\"neworder\" value=\"\">
                    <tr><td>Change category id# ".$_GET["id"]." to:</td><td> <input type=text name=u_cat value='".$cRec[0]["name"]."' size='40'></td></tr>
                    
                    <tr><td>Who can see this category? </td><td><select size='1' name='u_view'>";
                    echo createUserPowerMisc($cRec[0]["view"], 1);
                    echo "</select></td>";
                    $fRecs = $tdb->query("forums", "cat='".$_GET["id"]."'");
                    if ($fRecs !== false)
                    {
                    echo "<tr><td valign='top'>Sort the Forums in this category</td><td>";
                    
                    $sort = $cRec[0]["sort"];
                    $order = explode(",",$sort);
                    
                    echo "<select multiple name=\"fsort\" size=\"".count($fRecs)."\">";
                      
                    for ($i = 0;$i < count($order);$i++)
                    {
                      foreach ($fRecs as $fRec)
                      {
                        if ($fRec['id'] == $order[$i])
                          echo "<option value='".$fRec['id']."'>".$fRec['id']."::".$fRec['forum']."</option>";
                      }
                    }
                    echo "</select><br>";
                    echo "<input type=\"button\" value=\"Move Up\" ";
    echo "onClick=\"change_order(this.form.fsort.selectedIndex,-1,'forum')\">&nbsp;&nbsp;&nbsp;";
    echo "<input type=\"button\" value=\"Move Down\"";
    echo "onClick=\"change_order(this.form.fsort.selectedIndex,+1,'forum')\">";
                    echo "</td></tr><tr><td colspan='2'><input type='button' onClick=\"submitorderform('forum','full')\" value='Edit'></td></tr>";
                    }
                    else
                    {  
                    echo "<tr><td colspan='2'>There are no forums in this category";                    
                    echo "</td></tr><tr><td colspan='2'><input type='button' onClick=\"submitorderform('forum','empty')\" value='Edit'></td></tr>";
                    }
                    echo "</table>
                    
                    </table></form>";
                }
            } else {
                echo "No id selected.";
            }
        } elseif($_GET["action"] == "delete") {
            //delete categories
            if(isset($_GET["id"])) {
                if($_POST["verify"] == "Ok") {
                    $sort = explode(",", $_CONFIG['admin_catagory_sorting']);
                    if(($i = array_search($_GET["id"], $sort)) !== FALSE) unset($sort[$i]);
                    //var_dump($sort);
                    $config_tdb->editVars("config", array("admin_catagory_sorting" => implode(",", $sort),"type"=>"delcat"));
                    
                    $tdb->delete("cats", $_GET["id"]);
                    echo "Successfully deleted category.";
                    redirect($_SERVER['PHP_SELF'], 2);
                } elseif($_POST["verify"] == "Cancel") {
                    redirect($_SERVER['PHP_SELF'], 0);
                } else {
                    $cRec = $tdb->basicQuery("cats", "id",$_GET['id']);
                    ok_cancel("admin_cat.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete category '".$cRec[0]['name']."' ?");
                }
            } else {
                echo "No id selected.";
            }
        } elseif($_GET["action"] == "addnew") {
            //add new category
            if(isset($_GET['a'])) {
                
                $cat_id = $tdb->add("cats", array("name" => $_POST["u_cat"], "view" => $_POST["u_view"]));
                $cat_sort = $config_tdb->getVars('config');
                $cRecs = $tdb->listRec("cats", 1);
                foreach ($cRecs as $cRec)
                    $ids[] = $cRec['id'];  

                $config_tdb->editVars("config",array("admin_catagory_sorting" => implode(",", $ids),"type"=>"addcat"));
                echo "Successfully added new category <font color=green>".$_POST["u_cat"]."</font>";
                
                if($_POST['command'] == 'Add and Add another Category') redirect($_SERVER['PHP_SELF'].'?action=addnew', 2);
                elseif ($_POST['command'] == 'Add and Add forums to this category') redirect('admin_forum.php?action=addnew&cat_id='.$cat_id, 2);
                else redirect($_SERVER['PHP_SELF'], 2);
            } else {
                echo "<form action='admin_cat.php?action=addnew&a=1' method=POST>
                Name of new category: <input type=text name=u_cat size='40'><br>Who can see the category: <select size='1' name='u_view'>
                ".createUserPowerMisc(0, 1)."</select><br><input type=submit value='Add'> <input type=submit name='command' value='Add and Add another Category'> <input type=submit name='command' value='Add and Add forums to this category'> 
                </form>";
            }
    } else {
        $cats = $tdb->listRec("cats", 1);
        if(empty($cats)) redirect('admin_cat.php?action=addnew', 0);
        $c = count($cats);
        echoTableHeading(str_replace("<b>></b>", "<b>::</b>", substr($where, 20)), $_CONFIG);
        echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

        <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='4' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
	echo "<tr><td colspan='4' bgcolor='$header' bgcolor='$header'><center>";  require_once("admin_navigation.php");  echo"</center></td></tr>

        <tr><td colspan='4' bgcolor='$header'><table width=100% cellspacing=0 cellpadding=0><tr><td><B><font size='$font_l' face='$font_face' color='$font_color_header'>Manage Categories</font></b></td><td align=right><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_cat.php?action=addnew'><img src='images/add.jpg' border='0'></a></font></td></tr></table></td></tr>
        <tr><td width=60% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Forum Name</font></b></td>
        <td width=20% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>View</font></b></td>
        <td width=10% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Edit?</font></b></td>
        <td width=10% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Delete?</font></b></td>
        </tr>";
        if($cats[0]["name"] == "") {
            echo "<tr><td bgcolor='$table1' colspan='3'><font size='$font_m' face='$font_face' color='$font_color_main'>No records found</font></td></tr>";
        } else {
            foreach($cats as $cat) {
                //show each category
                $view = createUserPowerMisc($cat["view"], 2);
                
                echo "<tr><td bgcolor='$table1' width=60%><font size='$font_m' face='$font_face' color='$font_color_main'><b>".$cat["name"]."</b></font></td>
                <td bgcolor='$table1' width=20%><font size='$font_m' face='$font_face' color='$font_color_main'>$view</font></td>
                <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_cat.php?action=edit&id=".$cat["id"]."'>Edit</a></font></td>
                <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_cat.php?action=delete&id=".$cat["id"]."'>Delete</a></font></td></tr>";
            }
        }
        echo "</table>$skin_tablefooter<br><br> 
        <IFRAME SRC='index.php' WIDTH=".$_CONFIG["table_width_main"]." HEIGHT='300'></IFRAME>";
    }
} else {
    echo "you are not authorized to be here.<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>";
}

require_once("./includes/footer.php");
?>
