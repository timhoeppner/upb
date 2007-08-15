<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

require_once('./includes/class/func.class.php');
$where = "Members List";
require_once('./includes/header.php');

if($tdb->is_logged_in()) {
    if($_GET["page"] == "") $_GET["page"] = 1;
    $users = $tdb->listRec("users", ($_GET["page"] * $_CONFIG["topics_per_page"] - $_CONFIG["topics_per_page"] + 1), $_CONFIG["topics_per_page"]);
    
    $c = $tdb->getNumberOfRecords("users");
    if ($c <= $_CONFIG["topics_per_page"]) {
        $num_pages = 1;
    } elseif (($c % $_CONFIG["topics_per_page"]) == 0) {
        $num_pages = ($c / $_CONFIG["topics_per_page"]);
    } else {
        $num_pages = ($c / $_CONFIG["topics_per_page"]) + 1;
    }
    $num_pages = (int) $num_pages;
    
    if($num_pages == 1) {
        $pageStr = "<font face='$font_face' size='$font_s'><span class=pagenumstatic>$num_pages</span></font>";
    } else {
        //$pageStr = "<font face='$font_face' size='$font_s'><span class=pagenumstatic>";
        
        for($i=1;$i<=$num_pages;$i++) {
            if($_GET["page"] == $i){
                $pageStr .= $i."</span> ";
            } else {
                $pageStr .= "<font face='$font_face' size='$font_s'><span class=pagenum><a href='showmembers.php?page=".$i."'>".$i."</a></span> ";
            }
        }
        //$pageStr .= "</font></span>";
        unset($num_pages);
    }
    
    echo "<table border='0' cellspacing='0' cellpadding='4' width='".$_CONFIG["table_width_main"]."' align='center'><tr>
    <td><font size='$font_m' face='$font_face' color='$font_color_main'>".$pageStr."</font></td></tr></table><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
    <tr><td colspan='8' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Members List</font></b></td></tr>";
      echo "<tr>
        <th bgcolor='$table1' width='3%'><font size='$font_m' face='$font_face' color='$font_color_main'>ID</font></td>
        <th bgcolor='$table1' width='15%'><font size='$font_m' face='$font_face' color='$font_color_main'>Username</font></td>
        <th bgcolor='$table1' width='20%'><font size='$font_m' face='$font_face' color='$font_color_main'>Location</font></td>
        <th bgcolor='$table1' width='5%'><font size='$font_m' face='$font_face' color='$font_color_main'>Posts</font></td>
        <th bgcolor='$table1' width='15%'><font size='$font_m' face='$font_face' color='$font_color_main'>AIM</font></td>
        <th bgcolor='$table1' width='15%'><font size='$font_m' face='$font_face' color='$font_color_main'>MSN</font></td>
        <th bgcolor='$table1' width='15%'><font size='$font_m' face='$font_face' color='$font_color_main'>Yahoo!</font></td>
        <th bgcolor='$table1' width='12%'><font size='$font_m' face='$font_face' color='$font_color_main'>ICQ</font></td>
      </tr>";
    if($users[0]["id"] == "") {
        echo "<tr><td bgcolor='$table1' colspan='3'><font size='$font_m' face='$font_face' color='$font_color_main'>No records found</font></td></tr>";
    } else {
        foreach($users as $user) {
            if($user["level"] == "2") $userColor = $_STATUS["moderatcolor"];
            elseif($user["level"] == "3") $userColor = $_STATUS["admcolor"];
            else $userColor = $_STATUS["membercolor"];
/* location, # of posts, aim, msn, yahoo, icq */
          echo "<tr>
            <td bgcolor='$table1' width='3%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'><b>".$user["id"]."</b></font></td>
            <td bgcolor='$table1' width='15%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'><a href='profile.php?action=get&id=".$user["id"]."'>".$user["user_name"]."</a></font></td>
            <td bgcolor='$table1' width='20%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>".$user["location"]."</font></td>
            <td bgcolor='$table1' width='5%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>".$user["posts"]."</font></td>
            <td bgcolor='$table1' width='15%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>";
          if($user["aim"] != "") echo "<a href='aim:goim?screenname=".$user["aim"]."'><img src='images/aol.gif' border='0'>&nbsp;".$user["aim"]."</a>";  echo "</font></td>
            <td bgcolor='$table1' width='15%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>";
          if($user["msn"] != "") echo "<a href='http://members.msn.com/".$user["msn"]."' target='_blank'><img src='images/msn.gif' border='0'>&nbsp;".$user["msn"]."</a>";  echo "</font></p></td>
            <td bgcolor='$table1' width='15%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>";
          if($user["yahoo"] != "") echo "<a href='http://edit.yahoo.com/config/send_webmesg?.target=".$user["yahoo"]."&.src=pg'><img border=0 src='http://opi.yahoo.com/online?u=".$user["yahoo"]."&m=g&t=0'>&nbsp;".$user["yahoo"]."</a>"; echo "</font></p></td>
            <td bgcolor='$table1' width='15%'><p align='center'><font size='$font_m' face='$font_face' color='$userColor'>";
          if($user["icq"] != "") echo "<a href='http://wwp.icq.com/scripts/contact.dll?msgto=".$user["icq"]."&action=message'><img src='images/icq.gif' border='0'>&nbsp;".$user["icq"]."</a>";  echo "</font></td>
          </tr>";
        }
    }
    echo "</table>$skin_tablefooter";

    echo "<table border='0' cellspacing='0' cellpadding='4' width='".$_CONFIG["table_width_main"]."' align='center'><tr>
    <td><font size='$font_m' face='$font_face' color='$font_color_main'>".$pageStr."</font></td></tr></table><center>";
} else {
    echo "you are not authorized to be here.  Please <a href='login.php?ref='>log in</a> to view the list";
}

require_once('./includes/footer.php');
?>
