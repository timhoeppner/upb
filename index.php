<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

header ("refresh: 600");

if(!file_exists("./db/main.tdb") && file_exists("./db/config2.php")) die("updater has not been run yet. click <a href='update1.x-2.0.php'>here</a> to update.");
if(file_exists("config.php")) {
    require_once("config.php");
    if(!defined('DB_DIR')) die("installer has not been run yet. click <a href='install.php'>here</a> to install.");
}

require_once("./includes/class/func.class.php");
//echo '<pre>'; var_dump($GLOBALS); echo '</pre>';
if($_COOKIE["power_env"] == "" || empty($_COOKIE["power_env"]) || trim($_COOKIE["power_env"]) == "") $_COOKIE["power_env"] = "0";
//upb_session_start();
require_once("./includes/header.php");
if ($_CONFIG["servicemessage"] != "") {
    echoTableHeading("Announcements", $_CONFIG);
    echo "<center><table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG["table_width_main"]."' background='".$_CONFIG["skin_dir"]."/images/cat_top_bg.gif'><tr><td colspan='2' bgcolor='white'>
    <table width='90%' border='0' cellspacing='0' cellpadding='4' align='center'>
    <tr><td width='90%' align='Left'> <font size='$font_m' face='$font_face' color='$font_color_main'>".$_CONFIG["servicemessage"]."</font></td>
    </tr></table></tr></td></table></center>$skin_tablefooter";
}

$posts = new tdb(DB_DIR, "posts.tdb");

$cRecs = $tdb->listRec("cats", 1);
//$cRecs = $tdb->query("cats", "view<'".($_COOKIE["power_env"] + 1)."'");

if($cRecs[0]["id"] == "") {
    echo "<p>No categories have been added yet or this is a private forum.";
    if($_COOKIE["power_env"] != "3") {
        echo " Please contact an Administrator";
        if($_COOKIE["power_env"] > 0) echo " via <a href='newpm.php?id=1'>PM Message</a> or <a href='email.php?id=1'>web email</a>";
    } else {
        echo " To add a Category, <a href='admin_cat.php?action=addnew'>click here</a>.";
    }
    echo '</p>';
} else {
    if(@trim($_CONFIG["admin_catagory_sorting"]) != "") {
        $cSorting = explode(",", $_CONFIG["admin_catagory_sorting"]);
        $k = 0;
        $i = 0;
        $sorted = array();
        while($i<count($cRecs)) {
            if($cSorting[$k] == $cRecs[$i]["id"]) {
                if($_COOKIE["power_env"] >= $cRecs[$i]["view"]) $sorted[] = $cRecs[$i];
                //unset($cRecs[$i]);
                $k++;
                $i = 0;
            } else {
                $i++;
            }
        }
        $cRecs = $sorted;
        unset($sorted,$i,$catdef,$cSorting);
    } else {
        sort($cRecs);
    }
    
    reset($cRecs);
    $t_t = 0;
    $t_p = 0;
    
    foreach($cRecs as $cRec) {
        if($_COOKIE["power_env"] >= $cRec["view"]) {
            echoTableHeading($cRec["name"], $_CONFIG);
            echo "<table width='".$_CONFIG["table_width_main"]."' border='0' cellspacing='1' cellpadding='4' align='center' bgcolor='$border'>
              <tr>
                <td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif width='30' align='center' bgcolor='$header' valign='middle' ><font size='$font_m' face='$font_face' color='$font_color_header'>&nbsp;</font></td>
                <td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header' width=51% valign='middle' ><font size='$font_m' face='$font_face' color='$font_color_header'><B>Forum</B></font></td>
                <td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header' width=7% valign='middle' align='center'><font size='$font_m' face='$font_face' color='$font_color_header'><B>Topics</B></font></td>
                <td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header' width=7% valign='middle' align='center'><font size='$font_m' face='$font_face' color='$font_color_header'><B>Posts</B></font></td>
                <td background=".$_CONFIG["skin_dir"]."/images/title_bg.gif bgcolor='$header' width=30% valign='middle' align='center'><font size='$font_m' face='$font_face' color='$font_color_header'><B>Latest Topic</B></font></td>
              </tr>";
            $cId = $cRec["id"];
            //$fRecs = $tdb->query("forums", "cat='$cId'&&view<'".($_COOKIE["power_env"] + 1)."'");
            if($cRec["sort"] == "") {
                echo "<tr><td colspan=6 align='center' bgcolor=$table1><center>No forums have been added to this Category yet.";
                if($_COOKIE["power_env"] != "3") {
                    echo " Please contact an Administrator";
                    if($_COOKIE["power_env"] > 0) echo " via <a href='newpm.php?id=1'>PM Message</a> or <a href='email.php?id=1'>web email</a>";
                } else {
                    echo " To add a forum, <a href='admin_forum.php?action=addnew&cat_id=".$cRec["id"]."'>click here</a>.";
                }
                echo "</center></td></tr>";
            } else {
                unset($sort);
                $sort = explode(",", $cRec["sort"]);
                while(!empty($sort)) {
                    $fRec = $tdb->get("forums", $sort[0]);
                    $fRec = $fRec[0];
                    if((int)$fRec["view"] <= (int)($_COOKIE["power_env"])) {
                        //if($fRec["cat"] == $cRec["id"]) {
                            $posts->setFp("topics", $fRec["id"]."_topics");
                            $tRec = $posts->listRec("topics", 1, 1);
                            
                            if($fRec["mod"] == "") $mod = "unmoderated";
                            else $mod = $fRec["mod"];
                            if($tRec[0]["id"] == "") {
                                $when = "<div align='center'><center>No Posts</center></div>";
                                $v_icon = "off.gif";
                            } else {
                                $when = gmdate("M d, Y g:i:s a", user_date($tRec[0]["last_post"]))."</font><br><font size='$font_s' face='$font_face' color='$font_color_main'><a href='viewtopic.php?id=".$fRec["id"]."&t_id=".$tRec[0]["id"]."'>".$tRec[0]["subject"]."</a> by: ";
                                if($tRec[0]["user_id"] != "0") $when .= "<a href='profile.php?action=get&id=".$tRec[0]["user_id"]."'><b>".$tRec[0]["user_name"]."</b></a>";
                                else $when .= "<i>a ".$tRec[0]["user_name"]."</i>";
                                if(isset($_COOKIE["lastvisit"])) {
                                    //if(($tRec[0]["last_post"] > $_SESSION['newTopics']['lastVisitForums'][$cId] || $_SESSION['newTopics']['f'.$cId]['t'.$tRec[0]['id']] == 1)) $v_icon = "on.gif";
                                    if($tRec[0]["last_post"] > $_COOKIE["lastvisit"]) $v_icon = "on.gif";
                                    else $v_icon = "off.gif";
                                } else $v_icon = "off.gif";
                            }
                            $t_t += $fRec["topics"];
                            $t_p += $fRec["posts"];
                            if($fRec["topics"] == "0") $v_icon = "off.gif";
                            echo "   <tr>
                              <td width='30' align='center' bgcolor='$table2'><img src='icon/$v_icon' ></td>
                              <td bgcolor='$table1' style=\"Cursor:Hand\" onClick=\"window.location.href='viewforum.php?id=".$fRec["id"]."';\" onMouseOver=\"this.bgColor='$table2'\" onMouseOut=\"this.bgColor='$table1'\"><font size='$font_m' face='$font_face' color='$font_color_main'><a href='viewforum.php?id=".$fRec["id"]."'>".$fRec["forum"]."</a></font><br><font size='$font_s' face='$font_face' color='$font_color_main'>".$fRec["des"]."</font></td>
                              <td bgcolor='$table2' align='center'><font size='$font_s' face='$font_face' color='$font_color_main'>".$fRec["topics"]."</font></td>
                              <td bgcolor='$table1' align='center'><font size='$font_s' face='$font_face' color='$font_color_main'>".$fRec["posts"]."</font></td>
                              <td bgcolor='$table2' align='center'><font size='$font_s' face='$font_face' color='#888888'>$when</font></td>
                            </tr>";                              
                            unset($when);
                        /*} else {
                            echo "<tr><td colspan='6' bgcolor='$table1'><center>Forum's Category ID doesn't match</center></td></tr>";
                        }*/
                    }
                    array_shift($sort);
                    unset($when);
                }
            }
            echo $skin_tablefooter;
        }
        unset($cRec);
    }
}

//start Statistics Table
$whos = whos_online($whos_online_log, $_STATUS);
$whos_t = $whos["users"]+$whos["guests"];
$users_string = "";
if($whos["users"] > 0) $users_string = $whos["who"];

$mem_total = $tdb->getNumberOfRecords("users");
$mem_last = $tdb->listRec("users", $mem_total, 1);

$mt = explode(' ', microtime()); 
$script_end_time = $mt[0] + $mt[1]; 

echoTableHeading("Forum Statistics", $_CONFIG);
echo "      <table width='".$_CONFIG["table_width_main"]."' border='0' cellspacing='1' cellpadding='4' align='center' bgcolor=#14213f>
     <tr>
     <td background='".$_CONFIG["skin_dir"]."/images/title_bg.gif' colspan='3' bgcolor='$category'><font size='$font_m' face='$font_face' color='$font_color_category'><b>Whos Online</b></font></td>
     </tr>
     <tr>  
<td bgColor='$table2' valign='middle' width='49'><div align='center'><font face=verdana size=1><img src='icon/user.gif' width='23' height='20'></font></div></td>
<td colspan='2' width='95%' bgcolor='$table1'><font size='$font_s' face='$font_face' color='$font_color_main'>";
//<i>Who's Online System Offline</i></font></td>";
echo "
     Users online in the last 15 minutes: <b>$whos_t</b>
     <br><b>".$whos["users"]."</b> member(s) and <b>".$whos["guests"]."</b> guest(s).
     <br>".$users_string."
     </font></td>
     </tr>
     <tr>
     <td background='".$_CONFIG["skin_dir"]."/images/title_bg.gif' colspan='3' bgcolor='$category'><font size='$font_m' face='$font_face' color='$font_color_category'><b>Forum Stats</b></font></td>
     </tr>
     <tr>

<td bgColor='$table2' valign='middle' width='49'><div align='center'><font face=verdana size=1><img src='icon/stats.gif'></font></div></td>     
<td colspan='2' width='95%' bgcolor='$table1'><font size='$font_s' face='$font_face' color='$font_color_main'>Total Topics: <B>$t_t</B>
     <br>Total Posts: <B>$t_p</B>
     <br>Total Members: <B>$mem_total</B>
     <br>Newest Member: <B><a href='profile.php?action=get&id=".$mem_last[0]["id"]."'>".$mem_last[0]["user_name"]."</a></B>
     <br>Forum Page Views: <B>$hits_today</B>
     <br>Page Rendering Time: <B>
<font face=verdana size=1>".round($script_end_time - $script_start_time, 5)." seconds</font>
</B></font></td>
     </tr>
     <tr>
     <td background='".$_CONFIG["skin_dir"]."/images/title_bg.gif' colspan='3' bgcolor='$category'><font size='$font_m' face='$font_face' color='$font_color_category'><b>Forum Legend</b></font></td>
     </tr>
     <tr>
     <td colspan='3' bgcolor='$table1'> 
              <table width='100%' border='0' cellspacing='0' cellpadding='3' align='left'>
                <tr> 
                  <td align='left' width='50%'><img src='icon/on.gif' > <font size='$font_s' face='$font_face' color='$font_color_main'>New 
                    posts since last visit </font></td>
                  <td align='left' width='50%'><img src='icon/off.gif' > <font size='$font_s' face='$font_face' color='$font_color_main'>No 
                    new posts since last visit</font></td>
                </tr>
              </table></table>$skin_tablefooter<br>"; 
//End Statistic Table

require_once("./includes/footer.php");
if(empty($_COOKIE["user_env"])) $user = "guest"; 
else $user = $_COOKIE["user_env"]; 

$month = date("m",time());
$year = date("Y",time()); 
if ($REMOTE_HOST == "") $visitor_info = $REMOTE_ADDR; 
else $visitor_info = $REMOTE_HOST; 

$base = "http://" . $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']; 
$x1= "host $REMOTE_ADDR |grep Name"; 
$x2= $REMOTE_ADDR; 
$fp = fopen(DB_DIR."/iplog", "a"); 
$date= "$month $year"; 
fputs($fp, "$visitor_info -$HTTP_USER_AGENT- $user- <br>Date/Time: $date$REMOTE_ADDR:$x2 $x1$base:--------------------------------Next Person<p><br>\r\n");
fclose($fp);

if(filesize(DB_DIR."/iplog") > (1024 * 10)) {
    $fp = fopen(DB_DIR."/iplog", 'r');
    fseek($fp, (filesize(DB_DIR."/iplog") - (1024 * 10)));
    $log = fread($fp, (1024 * 10));
    fclose($fp);
    $fp = fopen(DB_DIR."/iplog", 'w');
    fwrite($fp, $log);
    fclose($fp);
}
?>
