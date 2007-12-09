<?php
require_once("config.php");
require_once("./includes/class/func.class.php");
require_once('./includes/header_simple.php');


$tdb->setFp("config", "config");
$tdb->setFp("ext_config", "ext_config");

$array = array("title" => "Category Sorting","description" => "Sort the categories in the order you want them to appear on the main page","form_object" => "list");

if($tdb->edit('ext_config',8,$array))
  echo "Settings for Category Sorting updated<p>";

$tdb->createTable('smilies', array(array('id', 'id'), array('bbcode', 'memo'),array('replace','memo'));
$tdb->setFp("smilies","smilies");
$msg[] = array("bbcode"=>" :)","replace"=> " <img src='smilies/smile.gif'> ", $msg);
    $msg[] = array("bbcode"=>" :(", "replace"=>" <img src='smilies/frown.gif'> ", $msg);
    $msg[] = array("bbcode"=>" ;)","replace"=> " <img src='smilies/wink.gif'> ", $msg);
    $msg[] = array("bbcode"=>" :P","replace"=> " <img src='smilies/tongue.gif'> ", $msg);
    $msg[] = array("bbcode"=>" :o","replace"=> " <img src='smilies/eek.gif'> ", $msg);
    $msg[] = array("bbcode"=>" :D","replace"=> " <img src='smilies/biggrin.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (C)","replace"=> " <img src='smilies/cool.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (M)","replace"=> " <img src='smilies/mad.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (confused)","replace"=> " <img src='smilies/confused.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (crazy)","replace"=> " <img src='smilies/crazy.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (hm)","replace"=> " <img src='smilies/hm.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (hmmlaugh)","replace"=> " <img src='smilies/hmmlaugh.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (offtopic)","replace"=> " <img src='smilies/offtopic.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (blink)","replace"=> " <img src='smilies/blink.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (rofl)","replace"=> " <img src='smilies/rofl.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (R)","replace"=> " <img src='smilies/redface.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (E)","replace"=> " <img src='smilies/rolleyes.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (wallbash)","replace"=> " <img src='smilies/wallbash.gif'> ", $msg);
    $msg[] = array("bbcode"=>" (noteeth)","replace"=> " <img src='smilies/noteeth.gif'> ", $msg);
    $msg[] = array("bbcode"=>" LOL","replace"=> " <img src='smilies/lol.gif'> ", $msg);
//$tdb->removeTable('smilies');
?>
