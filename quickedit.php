<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3
// Quick Reply Script for using AJAX by Clark
require_once("./includes/class/func.class.php");
require_once("./includes/class/posts.class.php");
$posts_tdb = new posts(DB_DIR, "posts.tdb");

$posts_tdb->setFp("topics", $_POST["forumid"]."_topics");
$posts_tdb->setFp("posts", $_POST["forumid"]);
//$fRec = $tdb->get("forums", $_GET["id"]);
//$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
$pRec = $posts_tdb->get("posts", $_POST["postid"]);
if ($_POST['type'] == 'getpost')
{
  $output .= "<form action='editpost.php?id=".$_POST["forumid"]."&t_id=".$_POST["threadid"]."&p_id=".$_POST["postid"]."' method='POST' id='quickedit' name='quickedit'>";
  $output .= "<input type='hidden' id='forumid' name='userid' value='".$_POST["forumid"]."'>";
  $output .= "<input type='hidden' id='userid' name='userid' value='".$_POST["userid"]."'>";
  $output .= "<input type='hidden' id='threadid' name='threadid' value='".$_POST["threadid"]."'>";
  $output .= "<input type='hidden' id='postid' name='postid' value='".$_POST["postid"]."'>";
  $output .= "<textarea name='newedit' id='newedit' cols='60' rows='18'>".$pRec[0]['message']."</textarea><br>";
  $output .= "\n<input type='button' onclick='javascript:getEdit(document.getElementById(\"quickedit\"),\"".$_POST['divname']."\");'\' name='qedit' value='Save Edit'>";
  $output .= "\n<input type='submit' name='submit' value='Go Advanced'>";
  $output .= "</form>";
  echo $output;
}
else
{
  if(!(isset($_POST["userid"]) && isset($_POST["forumid"]) && isset($_POST["threadid"]) && isset($_POST["postid"]))) exitPage("Not enough information to perform this function.");
  if(!($tdb->is_logged_in())) exitPage("You are not logged in, therefore unable to perform this action.");

  if($pRec[0]["user_id"] != $_COOKIE["id_env"] && $_COOKIE["power_env"] < 2) exitPage("You are not authorized to edit this post.");

  $msg = format_text(filterLanguage(UPBcoding(htmlentities(utf8_decode(stripslashes($_POST["newedit"])))), $_CONFIG["censor"]));
  $dbmsg = htmlentities(stripslashes(utf8_decode($_POST["newedit"])),ENT_NOQUOTES);

  $posts_tdb->edit("posts", $_POST["postid"], array("message" => $dbmsg, "edited_by_id" => $_COOKIE["id_env"], "edited_by" => $_COOKIE["user_env"], "edited_date" => mkdate()));
//clearstatcache();
  $posts_tdb->cleanup();
  $posts_tdb->setFp("posts", $_POST["forumid"]);
  $pRec2 = $posts_tdb->get("posts", $_POST["postid"]);
  if(!empty($pRec2[0]['edited_by']) && !empty($pRec2[0]['edited_by_id']) && !empty($pRec2[0]['edited_date'])) 
    $edited = '<table width="95%" border="1" cellspacing="0" cellpadding="3"><tr><td>Last edited by: <a href="profile.php?action=get&id='.$pRec2[0]['edited_by_id'].'" target="_new">'.$pRec2[0]['edited_by'].'</a> on '.gmdate("M d, Y g:i:s a", user_date($pRec2[0]['edited_date'])).'</td></tr></table>';

  echo "$msg<!--divider-->$edited";
}
?>
