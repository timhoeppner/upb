<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2
require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_baduser.php'>Manage banned users</a>";
require_once("./includes/header.php");

if(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"])) {
    if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
        if (isset($_GET["action"])) {
            if($_GET["action"] == "edit" && $_GET["word"] != "") {
                //edit banned user
                $words = explode("\n", file_get_contents(DB_DIR."/banneduser.dat"));
                if(($index = array_search($_GET["word"], $words)) !== FALSE) {
                    if(isset($_POST["newword"])) {
                        echo "Editing banned user...";
                        $words[$index] = trim($_POST["newword"]);
                        $f = fopen(DB_DIR."/banneduser.dat", 'w');
                        fwrite($f, implode("\n", $words));
                        fclose($f);

                        echo "Done!";
                        redirect("admin_banuser.php", 1);
                    } else {
                        echo "<form action='admin_banuser.php?action=edit&word=".((isset($_POST["word"])) ? $_POST['word'] : $_GET['word'])."' method=POST>
                        Change banned username to: <input type=text name=newword value='$words[$index]' size=20>
                        <input type=submit value='Edit'>
                        </form>";
                    }
                } else {
                    echo $_GET["word"]." was not found in the banned users list.";
                }
            } elseif($_GET["action"] == "delete" && $_GET["word"] != "") {
                //delete banned user
                if($_POST["verify"] == "Ok") {
                    // delete the user
                    echo "deleting user from ban list...";
                    $words = explode("\n", file_get_contents(DB_DIR."/banneduser.dat"));
                    if(($index = array_search($_GET["word"], $words)) !== FALSE) unset($words[$index]);
                    $f = fopen(DB_DIR."/banneduser.dat", 'w');
                    fwrite($f, implode("\n", $words));
                    fclose($f);
                    echo "Done!";
                    if($_POST["ref"] != "") redirect($_POST["ref"], 1);
                    else redirect("admin_banuser.php", 1);
                } elseif($verify == "Cancel") {
                    if($_POST["ref"] != "") redirect($_POST["ref"], 1);
                    else redirect("admin_banuser.php", 1);
                } else {
                    ok_cancel("admin_banuser.php?action=delete&word=".$_GET["word"], "Are you sure you want to delete <b>".$_GET["word"]."</b> from the banned users list?<input type='hidden' name='ref' value='".$_GET["ref"]."'>");
                }
            } elseif($_GET["action"] == "addnew") {
                //add new user
                if($_POST["newword"] != "") {
                    echo "Banning user...";
                    if(filesize(DB_DIR.'/banneduser.dat') > 0) {
                        $pre = file_get_contents(DB_DIR."/banneduser.dat");
                    } else $pre = '';
                    $f = fopen(DB_DIR."/banneduser.dat", 'w');
                    fwrite($f, $pre."\n".stripslashes(trim($_POST['word'])));
                    fclose($f);
                    echo "Done!";
                    if($_POST["ref"] != "") redirect($_POST["ref"], 1);
                    else redirect("admin_banuser.php", 1);
                } else {
                    echo "<form action='admin_banuser.php?action=addnew' method=POST><input type='hidden' name='ref' value='".$_GET["ref"]."'>
                    New banned user: <input type=text name=newword size=20 value='".$_GET['word']."'>
                    <input type=submit value='Add'>
                    </form>";
                }
            }
        } else {
            $list = explode("\n", file_get_contents(DB_DIR."/banneduser.dat"));
            echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

            echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

            <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
            echo "<tr><td colspan='3' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

            <tr><td colspan='3' bgcolor='$header'><table width=100% cellspacing=0 cellpadding=0><tr><td><B><font size='$font_l' face='$font_face' color='$font_color_header'>Manage banned users</font></b></td><td align=right><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_banuser.php?action=addnew'><img src='images/add.jpg' border='0'></a></font></td></tr></table></td></tr>";
            if(trim($list[0]) == "") echo "<tr><td bgcolor='$table1' colspan='3'><font size='$font_m' face='$font_face' color='$font_color_main'>No users found.</font></td></tr>";
            else {
                for($i=0;$i<count($list);$i++) {
                    echo "<tr><td bgcolor='$table1' width=80%><font size='$font_m' face='$font_face' color='$font_color_main'><b>$list[$i]</b></font></td>
                    <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_banuser.php?action=edit&word=$list[$i]'>Edit</a></font></td>
                    <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_banuser.php?action=delete&word=$list[$i]'>Delete</a></font></td></tr>";
                }
            }
            echo "</table>$skin_tablefooter";
        }
    } else {
        echo "you are not authorized to be here.";
    }
} else {
    echo "you are not even logged in";
    redirect("login.php?ref=admin_basuser.php", 2);
}

require_once("./includes/footer.php");
?>