<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_badwords.php'>Manage Badwords</a>";
require_once('./includes/header.php');

if(!(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"]))) {
	echo "you are not even logged in";
	redirect("login.php?ref=admin_badwords.php", 2);
}
if(!($tdb->is_logged_in() && $_COOKIE["power_env"] == 3)) exitPage("you are not authorized to be here.");
if($_GET["action"] == "delete" && $_GET["word"] != "") {
	if($_POST["verify"] == "Ok") {
		echo "deleting bad word...";
		$words = explode("\n", file_get_contents(DB_DIR."/badwords.dat"));
		if(($index = array_search($_GET["word"], $words)) !== FALSE) unset($words[$index]);
		$f = fopen(DB_DIR."/badwords.dat", 'w');
		fwrite($f, implode("\n", $words));
		fclose($f);
		echo "Done!";
		redirect("admin_badwords.php", 1);
	} elseif($_POST["verify"] == "Cancel") redirect("admin_badwords.php", 1);
	else ok_cancel("admin_badwords.php?action=delete&word=".$_GET["word"], "Are you sure you want to delete <b>".$_GET["word"]."</b> from the badword list?");
} elseif($_GET["action"] == "addnew") {
	if($_POST["newword"] != "") {
		echo "adding new word...";
		if(filesize(DB_DIR.'/badwords.dat') > 0) {
		    $pre = file_get_contents(DB_DIR."/badwords.dat");
		} else $pre = '';
		$f = fopen(DB_DIR."/badwords.dat", 'w');
		fwrite($f, $pre."\n".stripslashes(trim($_POST['newword'])));
		fclose($f);
		echo "Done!";
		redirect("admin_badwords.php", 1);
	} else {
		?> <form action="admin_badwords.php?action=addnew" method=POST>
                New badword: <input type="text" name="newword" size="20">
                <input type="submit" value="Add">
                </form><?php
	}
} else {
	$words = explode("\n", file_get_contents(DB_DIR."/badwords.dat"));
	echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
	echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

            <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='3' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
	echo "<tr><td colspan='3' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

            <tr><td colspan='3' bgcolor='$header'><table width=100% cellspacing=0 cellpadding=0><tr><td><B><font size='$font_l' face='$font_face' color='$font_color_header'>Manage Badwords</font></b></td><td align=right><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_badwords.php?action=addnew'><img src='images/add.jpg' border='0'></a></font></td></tr></table></td></tr>";
	if(trim($words[0]) == "") {
		echo "<tr><td bgcolor='$table1' colspan='3'><font size='$font_m' face='$font_face' color='$font_color_main'>No words found.</font></td></tr>";
	} else {
		for($i=0;$i<count($words);$i++) {
			echo "<tr><td bgcolor='$table1' width=90%><font size='$font_m' face='$font_face' color='$font_color_main'><b>$words[$i]</b></font></td>
                    <td bgcolor='$table1' width=10%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_badwords.php?action=delete&word=$words[$i]'>Delete</a></font></td></tr>";
		}
	}
	echo "</table>$skin_tablefooter";
}
require_once("./includes/footer.php");
?>