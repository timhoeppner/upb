<?php

require_once('./includes/upb.initialize.php');
require_once('./includes/class/posts.class.php');
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$cRecs = $tdb->listRec("cats", 1);

//dump($cRecs);

foreach ($cRecs as $cRec)
{

$fRec = $tdb->get("forums", $cRec["id"]);
$posts_tdb->setFp("topics", $cRec["id"]."_topics");
$posts_tdb->set_forum($fRec);
$tRecs1 = $posts_tdb->query("topics", "sticky='1'", 1);
$tRecs2 = $posts_tdb->query("topics", "sticky='0'", 1);
if (!empty($tRecs1)) $tRecs = array_merge($tRecs1, $tRecs2);
else $tRecs = $tRecs2;

foreach ($tRecs as $tRec)
{
if ($tRec['monitor'] != "")
{
  $id_array = array();
  $monitors = explode(",",$tRec['monitor']);
  //dump($monitors);
  foreach ($monitors as $key => $monitor)
  {
    $user_id = $tdb->basicQuery('users','email',$monitor);
    $id_array[] = $user_id[0]['id'];
  }
  $monitor_ids = implode(",",$id_array);
  $posts_tdb->edit("topics", $tRec["id"], array("monitor" => $monitor_ids));
}
}

}

?>