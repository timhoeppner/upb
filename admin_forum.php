<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once("./includes/class/func.class.php");
$where = "<a href='admin.php'>Admin</a> ".$_CONFIG["where_sep"]." <a href='admin_forum.php'>Manage Forums</a>";
require_once('./includes/header.php');
$post_tdb = new functions(DB_DIR, "posts.tdb");

if(!$tdb->is_logged_in() || $_COOKIE["power_env"] != 3) exitPage("you are not authorized to be here.<meta http-equiv='refresh' content='2;URL=login.php?ref=admin.php'>");
if (isset($_GET["action"])) {
    if($_GET["action"] == "edit") {
        if(isset($_GET["id"])) {
            $fRec = $tdb->get("forums", $_GET["id"]);
            if(isset($_POST["u_forum"])) {
                if($_POST["cat"] != $fRec[0]["cat"]) {
                    $cRec = $tdb->get("cats", $fRec[0]["cat"]);
                    $cRec[0]["sort"] = explode(",", $cRec[0]["sort"]);
                    $key = array_search($fRec[0]["id"], $cRec[0]["sort"]);
                    unset($cRec[0]["sort"][$key]);
                    $tdb->edit("cats", $cRec[0]["id"], array("sort" => implode(",", $cRec[0]["sort"])));
                    unset($key, $cRec);
                    $cRec = $tdb->get("cats", $_POST["cat"]);
                    if($cRec[0]["sort"] != "") $cRec[0]["sort"] .= ",".$fRec[0]["id"];
                    else $cRec[0]["sort"] = $fRec[0]["id"];
                    $tdb->edit("cats", $_POST["cat"], array("sort" => $cRec[0]["sort"]));
                }

                $tdb->edit("forums", $_GET["id"], array("forum" => $_POST["u_forum"], "cat" => $_POST["cat"], "des" => $_POST["des"], "view" => $_POST["u_view"], "post" => $_POST["u_post"], "reply" => $_POST["u_reply"]));
                echo "Forum successfully edited.";
                redirect($_SERVER['PHP_SELF'], 2);
            } else {
                $cRecs = $tdb->listRec("cats", 1);
                $select = "<Select name=cat>\n";
                foreach($cRecs as $cRec) {
                    if($cRec["id"] == $fRec[0]["cat"]) $select .= "<option value='".$cRec["id"]."' selected>".$cRec["name"]."</option>";
                    else $select .= "<option value='".$cRec["id"]."'>".$cRec["name"]."</option>";
                }
                $select .= "</select>";

                $whoView = "<select size='1' name='u_view'>".createUserPowerMisc($fRec[0]["view"], 1)."</select>";
                $whoPost = "<select size='1' name='u_post'>".createUserPowerMisc($fRec[0]["post"], 1)."</select>";
                $whoReply = "<select size='1' name='u_reply'>".createUserPowerMisc($fRec[0]["reply"], 1)."</select>";

                echo "<form action='".$_SERVER['PHP_SELF']."?action=edit&id=".$_GET["id"]."' method=POST>
                    Name of forum: <input type=text name=u_forum size='40' maxlength=50 value='".$fRec[0]["forum"]."'>
                    <br>Category: $select
                    <br>Who can see this forum: $whoView
                    <br>Who can post in this forum: $whoPost
                    <br>Who can reply in this forum: $whoReply
                    <br>Description: <textarea cols=30 rows=5 maxlength=50 name=des>".$fRec[0]["des"]."</textarea>
                    <br><input type=submit value='Edit'>
                    </form>";
            }
        } else {
            echo "No id selected.";
        }
    } elseif($_GET["action"] == "delete") {
        //delete a forum
        if(isset($_GET["id"])) {
            if($_POST["verify"] == "Ok") {
                $fRec = $tdb->get("forums", $_GET["id"]);
                $cRec = $tdb->get("cats", $fRec[0]["cat"]);
                $sort = explode(",", $cRec[0]["sort"]);
                for($i=0;$i<count($sort);$i++) {
                    if($sort[$i] == $_GET["id"]) {
                        unset($sort[$i]);
                        break;
                    }
                }
                $sort = implode(",", $sort);
                $tdb->edit("cats", $cRec[0]["id"], array("sort" => $sort));
                $tdb->delete("forums", $_GET["id"]);
                $post_tdb->removeTable($_GET["id"]);
                $post_tdb->removeTable($_GET["id"]."_topics");
                $post_tdb->cleanup();
                echo "Successfully deleted forum.";
            } elseif($verify == "Cancel") {
                redirect($_SERVER['PHP_SELF'], 0);
            } else {
                ok_cancel("admin_forum.php?action=delete&id=".$_GET["id"], "Are you sure you want to delete this forum?");
            }
        } else {
            echo "No id selected.";
        }
    } elseif($_GET["action"] == "addnew") {
        //add new forum
        if(isset($_POST["u_forum"])) {
            $record = array(
            "forum" => $_POST["u_forum"],
            "cat" => $_POST["cat"],
            "view" => $_POST["u_view"],
            "post" => $_POST["u_post"],
            "reply" => $_POST["u_reply"],
            "des" => $_POST["des"],
            "topics" => 0,
            "posts" => 0
            );
            $_GET["id"] = $tdb->add("forums", $record);

            $cRec = $tdb->get("cats", $_POST["cat"]);
            if($cRec[0]["sort"] == "") $sort = $_GET["id"];
            else $sort = $cRec[0]["sort"].",".$_GET["id"];
            $tdb->edit("cats", $_POST["cat"], array("sort" => $sort));

            $post_tdb->createTable($_GET["id"], array(
            array("icon", "string", 10),
            array("user_name", "string", 20),
            array("date", "number", 14),
            array("message", "memo"),
            array("user_id", "number", 7),
            array("t_id", "number", 7),
            array('edited_by', 'string', 20),
            array('edited_by_id', 'number', 7),
            array('edited_date', 'number', 14),
            array("id", "id"),
            array("upload_id", "number", 10)
            ));
            //chown(DB_DIR."/".$_GET["id"].".memo", "nobody");
            //chown(DB_DIR."/".$_GET["id"].".ref", "nobody");
            //chown(DB_DIR."/".$_GET["id"], "nobody");
            $post_tdb->createTable($_GET["id"]."_topics", array(
            array("icon", "string", 10),
            array("subject", "memo"),
            array("topic_starter", "string", 20),
            array("sticky", "number", 1),
            array("replies", "number", 9),
            array("locked", "number", 1),
            array("views", "number", 7),
            array("last_post", "number", 14),
            array("user_name", "string", 20),
            array("user_id", "number", 7),
            array("monitor", "memo"),
            array("p_ids", "memo"),
            array("id", "id")
            ), 30);
            //chown(DB_DIR."/".$_GET["id"]."_topics.memo", "nobody");
            //chown(DB_DIR."/".$_GET["id"]."_topics.ref", "nobody");
            //chown(DB_DIR."/".$_GET["id"]."_topics", "nobody");
            echo "Successfully added new Forum <font color=green>".$_POST["u_forum"]."</font>";
            if($_POST['command'] == 'Add and Add another forum to the selected Category') redirect($_SERVER['PHP_SELF'].'?action=addnew&cat_id='.$_POST['cat'], 2);
            elseif($_POST['command'] == 'Add and Add another forum') redirect($_SERVER['PHP_SELF'].'?action=addnew', 2);
            else redirect($_SERVER['PHP_SELF'], 2);
        } else {
            $cRecs = $tdb->listRec("cats", 1);
            $select = "<Select name=cat>\n";
            foreach($cRecs as $cat) {
                if(isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['id']) $select .= "<option value='".$cat["id"]."' selected>".$cat["name"]."</option>";
                else $select .= "<option value='".$cat["id"]."'>".$cat["name"]."</option>";
            }
            $select .= "</select>";

            $whoView = "<select size='1' name='u_view'>".createUserPowerMisc(0, 1)."</select>";
            $whoPost = "<select size='1' name='u_post'>".createUserPowerMisc(1, 1)."</select>";
            $whoReply = "<select size='1' name='u_reply'>".createUserPowerMisc(1, 1)."</select>";

            echo "<form action='admin_forum.php?action=addnew' method=POST>
                Name of new forum: <input type=text name=u_forum maxlength=50 size='40'>
                <br>Category: $select
                <br>Who can see this forum: $whoView
                <br>Who can post in this forum: $whoPost
                <br>Who can reply in this forum: $whoReply
                <br>Description: <textarea cols=30 rows=5 maxlength=70 name=des></textarea>
                <br><input type=submit value='Add'> <input type=submit name='command' value='Add and Add another forum' size='10'> <input type=submit name='command' value='Add and Add another forum to the selected Category' size='15'>
                </form>";
        }
    }
} else {
    $fRecs = $tdb->listRec("forums", 1);
    if(empty($fRecs)) redirect('admin_forum.php?action=addnew', 0);
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);

    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>

        <tr><td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif colspan='10' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>Admin Panel Navigation</center></font></b></td></tr>";
    echo "<tr><td colspan='10' bgcolor='$header' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'><center>";  require_once("admin_navigation.php");  echo"</center></font></b></td></tr>

        <tr><td colspan='10' bgcolor='$header'><table width=100% cellspacing=0 cellpadding=0><tr><td width=30%><B><font size='$font_l' face='$font_face' color='$font_color_header'>Manage Forums</font></b></td><td align=right><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_forum.php?action=addnew'><img src='images/add.jpg' border='0'></a></font></td></tr></table></td></tr>
        <tr>
        <td width=52% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Name</font></td>
        <td width=6% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Cat ID</font></td>
        <td width=5% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>view</font></td>
        <td width=5% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>post</font></td>
        <td width=5% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>reply</font></td>
        <td width=9% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>space in topics table</font></td>
        <td width=9% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>space in posts table</font></td>
        <td width=4% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Edit?</font></td>
        <td width=5% bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Delete?</font></td></tr>
        ";
    foreach($fRecs as $fRec) {
        $post_tdb->setFp("topics", $fRec["id"]."_topics");
        $post_tdb->setFp("posts", $fRec["id"]);
        $t_txt = "<b>".(round(($post_tdb->getNumberOfRecords("topics")/5000), 1)-100)*(-1)."%</b>";
        $p_txt = "<b>".(round(($post_tdb->getNumberOfRecords("posts")/5000), 1)-100)*(-1)."%</b>";

        $whoView = createUserPowerMisc($fRec["view"], 3);
        $whoPost = createUserPowerMisc($fRec["post"], 3);
        $whoReply = createUserPowerMisc($fRec["reply"], 3);

        //show each category
        echo "<tr height=10>
            <td bgcolor='$table1' width=52%><font size='$font_m' face='$font_face' color='$font_color_main'>".$fRec["forum"]."</td>
            <td bgcolor='$table1' width=6%><font size='$font_m' face='$font_face' color='$font_color_main'>".$fRec["cat"]."</font></td>
            <td bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'>$whoView</font></td>
            <td bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'>$whoPost</font></td>
            <td bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'>$whoReply</font></td>
            <td bgcolor='$table1' width=9%><font size='$font_m' face='$font_face' color='$font_color_main'>$t_txt</font></td>
            <td bgcolor='$table1' width=9%><font size='$font_m' face='$font_face' color='$font_color_main'>$p_txt</font></td>
            <td bgcolor='$table1' width=4%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_forum.php?action=edit&id=".$fRec["id"]."'>Edit</a></td>
            <td bgcolor='$table1' width=5%><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_forum.php?action=delete&id=".$fRec["id"]."'>Delete</a></font></td></tr>";
    }
    echo "</table>$skin_tablefooter<br><br>
        <IFRAME SRC='index.php' WIDTH=".$_CONFIG["table_width_main"]." HEIGHT='300'></IFRAME>";
}
require_once("./includes/footer.php");
?>