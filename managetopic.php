<?php
// managetopic.php
// designed for Ultimate PHP Board
// Author: Jerroyd Moore, aka Rebles
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.4.1
require_once("./includes/class/func.class.php");
require_once("./includes/class/posts.class.php");
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");

if(!isset($_GET["id"]) || !isset($_GET["t_id"])) exitPage("Unable to retrieve topic information, Not enough information provided", true);
if(!$tdb->is_logged_in()) exitPage("You must be logged in.", true);
if($_GET["action"] == "watch") {
    if(!isset($_GET["id"]) || !isset($_GET["t_id"])) exitPage("Not enough information to watch this topic", true);
    $posts_tdb->setFp("topics", $_GET["id"]."_topics");
    $tRec = $posts_tdb->get("topics", $_GET["t_id"]);
    $where = "<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Watch Topic";
    require_once('./includes/header.php');
    $user = $tdb->get("users", $_COOKIE["id_env"]);
    if($tRec[0]["monitor"] == "") {
        $posts_tdb->edit("topics", $_GET["t_id"], array("monitor" => $user[0]["email"]));
        echo "You are now monitoring this topic.";
    } elseif(FALSE === (strpos($tRec[0]["monitor"], ','))) {
        if($tRec[0]["monitor"] == $user[0]["email"]) {
            if($_POST["verify"] == "Ok") {
                $posts_tdb->edit("topics", $_GET["t_id"], array("monitor" => ""));
                echo "You are no longer monitoring this topic.";
            } elseif($_POST["verify"] != "Cancel") {
                ok_cancel($PHP_SELF."?action=watch&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], "Are you sure you no longer wish to monitor this topic?");
                exitPage('', false, true);
            }
        } else {
            $posts_tdb->edit("topics", $_GET["t_id"], array("monitor" => $tRec[0]["monitor"].",".$user[0]["email"]));
            "You are now monitoring this topic.";
        }
    } else {
        $monitor_list = explode(',', $tRec[0]["monitor"]);
        if(!in_array($user[0]["email"], $monitor_list)) {
            if($_POST["verify"] == "Ok") {
                $id_index = array_search($user[0]["email"], $monitor_list);
                unset($monitor_list[$id_index]);
                $monitor_list = implode(",", $monitor_list);
                $posts_tdb->edit("topics", $_GET["t_id"], array("monitor" => $monitor_list));
                echo "You are no longer monitoring this topic.";
            } elseif($_POST["verify"] != "Cancel") {
                ok_cancel($PHP_SELF."?action=watch&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], "Are you sure you no longer wish to monitor this topic?");
                exitPage('', false, true);
            }
        } else {
            $posts_tdb->edit("topics", $_GET["t_id"], array("monitor" => $tRec[0]["monitor"].",".$user[0]["email"]));
            "You are now monitoring this topic.";
        }
    }
    require_once("./includes/footer.php");
    redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], 2);
    exit;
} elseif($_COOKIE["power_env"] > 1) {
    $posts_tdb->setFp("topics", $_GET["id"]."_topics");
    $posts_tdb->setFp("posts", $_GET["id"]);
    $tRec = $posts_tdb->get("topics", $_GET["t_id"]);
    $where = "<a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Topic Properties";
    if($_POST["move_forum"] == "1") {
        require_once('./includes/header.php');
        if($_COOKIE["power_env"] != "3") {
            echo "Unable to move/copy topic, you are not an administrator";
            require_once("./includes/footer.php");
            redirect($PHP_SELF."?id=".$_GET["id"]."&t_id=".$_GET["t_id"], 2);
            exit;
        }
        if($_POST["newId"] != "") {
            if($_POST["update_date"] == "1") $tRec[0]["last_post"] = mkdate();
            $tRec[0]["subject"] = str_replace($_CONFIG['sticky_note'], "", $tRec[0]["subject"]);
            if($_POST["sticky_status"] == "1") $tRec[0]["sticky"] = 1;
            else $tRec[0]["sticky"] = 0;
            if($_POST["closed_status"] == "1") $tRec[0]["locked"] = 1;
            else $tRec[0]["locked"] = 0;
            $p_ids = explode(",", $tRec[0]["p_ids"]);

            $posts_tdb->setfp("newTopics", $_POST["newId"]."_topics");
            $posts_tdb->setFp("newPosts", $_POST["newId"]);

            if($_POST["action"] == "redirect") $posts_tdb->edit("topics", $_GET["t_id"], array("subject" => "MOVED: ".$tRec[0]["subject"], "locked" => 1, "p_ids" => $p_ids[0]));
            unset($tRec[0]["p_ids"]);
            $newT_id = $posts_tdb->add("newTopics", $tRec[0]);
            $fNRec = $tdb->get("forums", $_POST["newId"]);
            @settype($fNRec[0]["topics"], "double");
            @settype($fNRec[0]["posts"], "double");
            $fNRec[0]["topics"]++;
            if($_POST["action"] == "redirect" || $_POST["action"] == "move") {
                $fORec = $tdb->get("forums", $_GET["id"]);
                @settype($fORec[0]["posts"], "double");
            }
            $newSort = array();
            for($i=0;$i<count($p_ids);$i++) {
                $pRec = $posts_tdb->get("posts", $p_ids[$i]);
                if($_POST["action"] == "redirect") {
                    if($pRec[0]["id"] == $p_ids[0]) {
                        $posts_tdb->edit("posts", $p_ids[0], array(
                          "icon" => "icon1.gif",
                          "user_name" => $_COOKIE["user_env"],
                          "date" => mkdate(),
                          "message" => "Topic was moved to forum : ".$fNRec[0]["forum"]."<br> redirecting...<meta http-equiv='refresh' content='1;URL=viewtopic.php?id=".$_POST["newId"]."&t_id=".$newT_id."'>",
                          "user_id" => $_COOKIE["id_env"]));
                    } else $posts_tdb->delete("posts", $pRec[0]["id"], false);
                }
                if($_POST["action"] == "move") {
                    $fORec[0]["posts"]--;
                    $posts_tdb->delete("posts", $pRecs[0]["id"], false);
                }
                $pRec[0]["t_id"] = $newT_id;
                $newSort[] = $posts_tdb->add("newPosts", $pRec[0]);
                $fNRec[0]["posts"]++;
            }

            if($_POST["action"] == "redirect") {
                $tdb->edit("forums", $_GET["id"], array("posts" => ((((int)$fORec[0]["posts"]) - count($p_ids)) + 1)));
                echo "Successfully moved topic and replaced the old one with a redirection";
            } elseif($_POST["action"] == "copy") {
                echo "Successfully copied topic";
            } elseif($_POST["action"] == "move") {
                $posts_tdb->delete("topics", $_GET["t_id"]);
                $tdb->edit("forums", $_GET["id"], array("topics" => ((int)$fRec[0]["topics"] - 1), "posts" => ((int)$fORec[0]["posts"] - count($p_ids))));
                echo "Successfully moved topic";
            }
            $posts_tdb->edit("newTopics", $newT_id, array("p_ids" => implode(",", $newSort)));
            $posts_tdb->sortAndBuild("newTopics", "last_post", "DESC");
            $tdb->edit("forums", $_POST["newId"], array("topics" => $fNRec[0]["topics"], "posts" => $fNRec[0]["posts"]));

            require_once("./includes/footer.php");
            if($_GET["redirect"] != "") redirect($_GET["redirect"], 2);
            else redirect($PHP_SELF."?id=".$_POST["newId"]."&t_id=$newT_id&s=".$_GET["s"], 2);
            exit;
        } else {
            echo "Please select a forum.";
        }
    } elseif($_POST["action"] == "Modify") {
        require_once('./includes/header.php');
        $tNewRec = array();
        if($_POST["open_forum"] == "0") $tNewRec["locked"] = 0;
        else $tNewRec["locked"] = 1;
        $tNewRec["subject"] = str_replace($_CONFIG['sticky_note'], "", htmlentities(stripslashes($_POST["subject"])));
        if($_POST["sticky"] == "1") $tNewRec["sticky"] = 1;
        else $tNewRec["sticky"] = 0;
        $tRec[0]["subject"] = str_replace("[Sticky Note]", "", $tRec[0]["subject"]);
        if($tNewRec["subject"] == $tRec[0]["subject"]) unset($tNewRec["subject"]);
        else {
            $p_ids = explode(",", $tRec[0]["p_ids"]);
            $pRec = $posts_tdb->get("posts", $p_ids[0]);
            $posts_tdb->edit("posts", $pRec[0]["id"], array("subject" => htmlentities(stripslashes($tNewRec["subject"]))));
        }
        $posts_tdb->edit("topics", $_GET["t_id"], $tNewRec);
        echo "Successfully edited topic properties";
        require_once("./includes/footer.php");
        redirect($PHP_SELF."?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$s, "2");
        exit;
    } elseif($_GET["action"] == "CloseTopic" || $_POST["action"] == "CloseTopic") {
        if($tRec[0]["locked"] == 1) echo "This Topic is already locked!";
        else {
            $posts_tdb->edit("topics", $_GET["t_id"], array("locked" => "1"));
            redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"], 0);
        }
    } elseif($_GET["action"] == "OpenTopic" || $_POST["action"] == "OpenTopic") {
        if($tRec[0]["locked"] == 0) echo "This Topic is already unlocked!";
        else {
            $posts_tdb->edit("topics", $_GET["t_id"], array("locked" => "0"));
            redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"], 0);
        }
    } elseif($_POST["action"] == "Delete") {
        require_once('./includes/header.php');
        if($_POST["verify"] == "Ok") {
            $p_ids = explode(",", $tRec[0]["p_ids"]);
            $ids = explode(",", $ids);
            $count = count($ids);
            $num = 0;
            foreach($ids as $p_id) {
                $posts_tdb->delete("posts", $p_id, false);
                $key = array_search($p_id, $p_ids);
                unset($p_ids[$key]);
                $num++;
                unset($key);
            }
            $posts_tdb->reBuild("posts");

            $p_ids = implode(",", $p_ids);
            $posts_tdb->edit("topics", $_GET["t_id"], array("p_ids" => $p_ids));
            echo "Successfully deleted ".$num." Post(s)";
            require_once("./includes/footer.php");
            redirect($PHP_SELF."?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$_GET["s"], "2");
            exit;
        } elseif($_POST["verify"] == "Cancel") {
            unset($_POST["action"]);
        } else {
            require_once('./includes/header.php');
            $posts_tdb->set_topic($tRec);
            $pTmpRecs = $posts_tdb->getPosts("posts");
            $pRecs = array();
            $ids = array();
            foreach($pTmpRecs as $pTmpRec) {
                $del = "del_".$pTmpRec["id"];
                if(isset($_POST[$del])) {
                    $pRecs[] = $pTmpRec;
                    $ids[] = $pTmpRec["id"];
                }
            }
            $ids = implode(",", $ids);
            echo "<b>Are you sure you want to delete the following posts from this topic?</b>
            <center>";
            echoTableHeading("Manage Topic:", $_CONFIG);
            echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'><tr><td width='22%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Name</font></td><td width='78%' bgcolor='$header'><font size='$font_m' face='$font_face' color='$font_color_header'>Message</font></td></tr>";
            $x = 1;
            foreach($pRecs as $pRec) {
                $msg = format_text(UPBcoding(filterLanguage($pRec["message"], $_CONFIG["censor"])));

                if($x == 0) {
                    $table_color = $table1;
                    $table_font = $font1;
                    $x++;
                } else {
                    $table_color = $table2;
                    $table_font = $font2;
                    $x--;
                }
                echo "<tr><td width='22%' valign='top' bgcolor='$table_color'><font size='$font_m' face='$font_face' color='$table_font'><b>".$pRec["user_name"]."</b></td><td width='78%' bgcolor='$table_color'><font size='$font_m' face='$font_face' color='$table_font'>$msg</font></td></tr>";
            }
            echo "</table>$skin_tablefooter<Br>";
            ok_cancel("$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$_GET["s"], "<input type='hidden' name='action' value='Delete'><input type='hidden' name='ids' value ='".$ids."'>");
        }
    }
    if($_GET["action"] == "" && $_POST["action"] == "") {
        require_once('./includes/header.php');
        if($tRec[0]["locked"] == 0) {
            $open = "checked";
            $closed = "";
        } else {
            $open = "";
            $closed = "checked";
        }
        if($tRec[0]["sticky"] == 1) $sticky_checked = "CHECKED";
        else $sticky_checked = "";
        $p_ids = explode(",", $tRec[0]["p_ids"]);
        $pRec = $posts_tdb->get("posts", $p_ids[0]);
        echo "<center>";
        echoTableHeading("Manage Topic:", $_CONFIG);
        echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'><tr><td colspan='2' bgcolor='$table1'>
        <form method='POST' action='$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$_GET["s"]."'>
        <table border='0' width='100%'><tr>
          <td width='16%'><font size='$font_m' face='$font_face' color='$font_color_main'>Topic Name: </font></td>
          <td width='84%'><input type='text' name='subject' size='20' value='".$tRec[0]["subject"]."'></td></tr>
          <tr><td width='16%'><font size='$font_m' face='$font_face' color='$font_color_main'>Stickied: </font></td>
          <td width='84%'><input type='checkbox' name='sticky' value='1' $sticky_checked></td></tr>
          <tr><td width='16%'>Created on:</td>
          <td width='84%'>".gmdate("M d, Y g:i:s a", user_date($pRec[0]["date"]))."</td></tr>
          <tr><td width='16%'>Created by:</td>
          <td width='84%'><font size='$font_m' face='$font_face' color='$font_color_main'><a href='profile.php?action=get&id=".$pRec[0]["user_id"]."'>".$pRec[0]["user_name"]."</font></td></tr>
          <tr><td width='16%'>No. of replies:</td>
          <td width='84%'><font size='$font_m' face='$font_face' color='$font_color_main'>".$tRec[0]["replies"]."</font></td></tr>
          <tr><td width='16%'>Last post on:</td>
          <td width='84%'>".gmdate("M d, Y g:i:s a", user_date($tRec[0]["last_post"]))."</td></tr>
          <tr><td width='16%'>Open/Close Status:</td>
          <td width='84%'><input type='radio' value='0' name='open_forum' id='fp1' $open><label for='fp1'>Open</label>
          <input type='radio' name='open_forum' value='1' id='fp2' $closed><label for='fp2'>Close</label></td></tr>
          <tr><td width='100%' colspan='2'><input type='submit' value='Modify' name='action'><input type='reset' value='Reset' name='B2'>
        </td></tr></table></form>";

        if($_COOKIE["power_env"] == 3) {
            echo "<a href='delete.php?action=delete&t=1&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&ref=managetopic.php'><img src='images/deletetopic.gif' border='0'></a>";
            $options =  "Move Topic To: <select name=newId>";

            $cRecs = $tdb->listRec("cats", 1);
            $cat_sort = explode(",", $_CONFIG["admin_catagory_sorting"]);
            $index = 0;
            $sorted = array();
            $i = 0;
            $max = count($cRecs);
            while($i<$max) {
                if($cat_sort[$index] == $cRecs[$i]["id"]) {
                    $sorted[] = $cRecs[$i];
                    $index++;
                    $i = 0;
                } else $i++;
            }
            $cRecs = $sorted;

            reset($cRecs);
            $fRecs = $tdb->listRec("forums", 1);

            foreach($cRecs as $cRec) {
                foreach($fRecs as $fRec) {
                    if($fRec["cat"] == $cRec["id"] && $fRec["id"] != $_GET["id"]) $options .=  "<option value='".$fRec["id"]."'>".$cRec["name"]." -> ".$fRec["forum"]."</option>\n";
                }
            }
            $options .=  "</select>";

            echo "<BR><form method='POST' action='$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$_GET["s"]."'><input type='hidden' name='move_forum' value='1'>
            <p>$options</p>
            <p><input type='radio' value='redirect' name='action' checked id='fp6'><label for='fp6'><font size='$font_m' face='$font_face' color='$font_color_main'> Move topic and leave a redirect topic in its place</label><br>
            <input type='radio' value='copy' name='action' id='fp5'><label for='fp5'> Copy topic to another forum</label><br>
            <input type='radio' value='move' name='action' id='fp4'><label for='fp4'> Move topic with out leaving a redirect topic</label><br>
            <input type='checkbox' name='update_date' value='1' id='fp3'><label for='fp3'> Update date of the new topic</label><br> <input type='checkbox' name='closed_status' value='1' id='fp7'><label for='fp7'> Close the new topic</label><br> <input type='checkbox' name='sticky_status' value='1' id='fp8'><label for='fp8'> Sticky note the new topic</label></font></p> <p><input type='submit' value='Submit' name='submit2'></p></form>";
        }

        if($_GET["s"] == "1") echo "<a href='$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=0'>Hide the topic's posts</a>";
        else echo "<a href='$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=1'>Show the topic's posts</a>";

        echo "<p>&nbsp;</p></td></tr></table>$skin_tablefooter";

        if($_GET["s"] == "1") {
            echo "<center>";
            echoTableHeading("Manage Topic:", $_CONFIG);
            echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."'><tr><td width='100%' bgcolor='white'>
            <form method='POST' action='$PHP_SELF?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&s=".$_GET["s"]."'><p><input type='submit' value='Delete' name='action'></p><table border='0' width='100%'>";
            $posts_tdb->set_topic($tRec);
            $pRecs = $posts_tdb->getPosts("posts");
            $x = +1;
            $i=0;
            foreach($pRecs as $pRec) {
                if($x == 0) {
                    $table_color = $table1;
                    $table_font = $font1;
                    $x++;
                } else {
                    $table_color = $table2;
                    $table_font = $font2;
                    $x--;
                }
                $msg = format_text(UPBcoding(filterLanguage($pRec["message"], $_CONFIG["censor"])));

                echo "<tr><td width='11%' bgcolor='$table_color' valign='top'><input type='checkbox' name='del_".$pRec["id"]."' value='CHECKED'> <a href='profile.php?id=".$pRec["user_id"]."'><b>".$pRec["user_name"]."</b></a></td><td width='89%' bgcolor='$table_color'>
                $msg</td></tr>";
            }
            echo "</table><p><input type='submit' value='Delete' name='action'>
            </p></td></tr></form></table>$skin_tablefooter";
            $i++;
        }
    }
} else {
   require_once('./includes/header.php');
   echo "You are not logged in, or authorized to view this page.";
}
require_once("./includes/footer.php");
?>