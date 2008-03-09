<?php
//XML FEED PAGE
require_once("./includes/upb.initialize.php");
require_once('./includes/class/posts.class.php');
$fRec = $tdb->get("forums", $_GET["id"]);

$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
if ($_GET['type'] == "forum")
{
$posts_tdb->setFp("topics", $_GET['id']."_topics");
$posts_tdb->set_forum($fRec);
//var_dump($_SERVER);
$tRecs = $posts_tdb->query("topics", "id>'0'");
$desc = $fRec[0]['des'];
$xml = "<?xml version=\"1.0\"?>";
?>
<?php
$xml .= "<rss version=\"2.0\"><channel>";
$xml .= "<title>".xml_clean($desc)."</title>
<link>".xml_clean($_SERVER['HTTP_REFERER'])."</link>
<description>".xml_clean($desc)."</description>
<language>en-us</language>";
$posts_tdb->set_topic($tRecs);
foreach ($tRecs as $key => $tRec)
{
  $url= "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
  $replace = "viewtopic.php?id=".$_GET['id']."&t_id=".$tRec['id'];
  $newurl = str_replace('xml.php',$replace,$url);
  $xml .= "<item>
  <title>".xml_clean($tRec['subject'])."</title>
  <link>".xml_clean($newurl)."</link>
  <description>".$tRec['subject']."</description>
  </item>";

}
$xml .= "</channel></rss>";
//$posts_tdb->varDump();
echo $xml;
}
else
{
$posts_tdb->setFp("topics", $_GET["id"]."_topics");
$posts_tdb->setFp("posts", $_GET["id"]);
$tRecs = $posts_tdb->get("topics", $_GET["t_id"]);
$query = "id>'0'&&t_id='".$_GET["t_id"]."'";
$desc = $tRecs[0]['subject'];
$pRecs = $posts_tdb->query("posts",$query,1,-1,array('user_name','date','message','id'));
$url= "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
  $replace = "viewtopic.php?id=".$_GET['id']."&t_id=".$_GET['t_id'];
  $newurl = str_replace('xml.php',$replace,$url);
$xml = "<?xml version=\"1.0\"?>";
?>
<?php
$xml .= "<rss version=\"2.0\">
<channel>

<title>".xml_clean($desc)."</title>
<link>".xml_clean($_SERVER['HTTP_REFERER'])."</link>
<description>".xml_clean($desc)."</description>
<language>en-us</language>
";

foreach ($pRecs as $key => $pRec)
{
  $newkey = $key+1;
  $pRecs[$key]['page']= floor(($newkey/$_CONFIG["posts_per_page"])+1);
}

$reverse = array_slice(array_reverse($pRecs),0,10);

foreach ($reverse as $key => $pRec)
{
  $newurl = str_replace('xml.php',$replace,$url);
  $newurl .= '&page='.$pRec['page']."#".$pRec['id'];
  $xml .= "<item>
  <title>Reply by ".xml_clean($pRec['user_name'])." on ".xml_clean(gmdate("M d, Y @ g:i:s a", user_date($pRec["date"])))."</title>
  <link>".xml_clean($newurl)."</link>
  <description>".substr(xml_clean($pRec['message']),0,250)."</description></item>";
}

$xml .= "</channel></rss>";
echo $xml;
}

//replaces characters in strings to make xml compatible
function xml_clean($string)
{
  $original = array("&","\"","\'",">","<");
  $replace = array("&amp;","&quot;","&apos;","&gt;","&lt;");
  $new = str_replace($original,$replace,$string);
  return $new;
}
?>
