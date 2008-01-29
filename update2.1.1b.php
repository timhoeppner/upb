<?php
require_once("config.php");
require_once("./includes/class/func.class.php");
require_once('./includes/header_simple.php');


$tdb->setFp("config", "config");
$tdb->setFp("ext_config", "ext_config");
$tdb->setFP("members","members");

$tdb->add("ext_config", array("name" => "security_code", "value" => "1", "type" => "config", "title" => "Enable Security Code", "description" => "Enable/Disable the security code image for new user registration<br>Enabling this is recommended", "form_object" => "checkbox", "minicat" => "1", "sort" => "16"));
$tdb->add("config", array("name" => "security_code", "value" => "1", "type" => "config"));

$tdb->edit("config",1,array('value'=>'2.1.1b'));
$tdb->edit("ext_config",1,array('value'=>'2.1.1b'));
$tdb->edit("ext_config",8,array('sort'=>'17'));
$tdb->edit("ext_config",9,array('sort'=>'18'));
$tdb->edit("ext_config",16,array('sort'=>'19'));

//move lastvisit information to the member database
$tdb->addField('members', array('lastvisit', 'number', 10));
$members = $tdb->query('members',"id>'0'");
foreach ($members as $member)
{
  $tdb->edit('members',$member['id'],array("lastvisit"=>0));
}

echo "Last visit information updated<p>";

//create superuser
$tdb->addField('members', array('superuser', 'string', 1));
$tdb->edit('members',1,array("superuser"=>"Y"));

echo "Super-user created<p>";

$array = array("title" => "Category Sorting","description" => "Sort the categories in the order you want them to appear on the main page","form_object" => "list");

if($tdb->edit('ext_config',8,$array))
  echo "Settings for Category Sorting updated<p>";


if (file_exists(DB_DIR."/bbcode.tdb"))
{
  $tdb->tdb(DB_DIR."/", "bbcode.tdb");
  $tdb->removeDatabase('bbcode.tdb');
}
//die();
$tdb = new tdb('', '');
$tdb->createDatabase(DB_DIR."/", "bbcode.tdb");
$tdb->tdb(DB_DIR.'/', 'bbcode.tdb');
$tdb->createTable('smilies', array(array('id', 'id'), array('bbcode', 'memo'),array('replace','memo'),array('type','string',4)));
//$tdb->cleanUp();
$tdb->setFp("smilies","smilies");

//type has three possible values
//main is shown on main page, more is shown on more smilies page, none means is not displayed but still available to show in the database
$tdb->add('smilies',array("bbcode"=>" :)","replace"=> " <img src='smilies/smile.gif' border='0' alt=':)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :(", "replace"=>" <img src='smilies/frown.gif' border='0' alt=':('> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" ;)","replace"=> " <img src='smilies/wink.gif' border='0' alt=';)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :P","replace"=> " <img src='smilies/tongue.gif' border='0' alt=':P'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :o","replace"=> " <img src='smilies/eek.gif' border='0' alt=':o'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :D","replace"=> " <img src='smilies/biggrin.gif' border='0' alt=':D'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (C)","replace"=> " <img src='smilies/cool.gif' border='0' alt='(C)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (M)","replace"=> " <img src='smilies/mad.gif' border='0' alt='(M)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (confused)","replace"=> " <img src='smilies/confused.gif' border='0' alt='(confused)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (crazy)","replace"=> " <img src='smilies/crazy.gif' border='0' alt='(crazy)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (hm)","replace"=> " <img src='smilies/hm.gif' border='0' alt='(hm)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (hmmlaugh)","replace"=> " <img src='smilies/hmmlaugh.gif' border='0' alt='(hmmlaugh)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (offtopic)","replace"=> " <img src='smilies/offtopic.gif' border='0' alt='(offtopic)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (blink)","replace"=> " <img src='smilies/blink.gif' border='0' alt='(blink)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (rofl)","replace"=> " <img src='smilies/rofl.gif' border='0' alt='(rofl)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (R)","replace"=> " <img src='smilies/redface.gif' border='0' alt='(R)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (E)","replace"=> " <img src='smilies/rolleyes.gif' border='0' alt='(E)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (wallbash)","replace"=> " <img src='smilies/wallbash.gif' border='0' alt='(wallbash)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (noteeth)","replace"=> " <img src='smilies/noteeth.gif' border='0' alt='(noteeth)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" LOL","replace"=> " <img src='smilies/lol.gif' border='0' alt='LOL'> ","type" => "main"));

//MORE SMILIES
$tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-035.gif[/img]","replace"=>"<img src='smilies/action-smiley-035.gif' border='0' alt='action-smiley-035.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-073.gif[/img]","replace"=>"<img src='smilies/action-smiley-073.gif' border='0' alt='action-smiley-073.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/anti-old.gif[/img]","replace"=>"<img src='smilies/anti-old.gif' border='0' alt='anti-old.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/blamesheep.gif[/img]","replace"=>"<img src='smilies/blamesheep.gif' border='0' alt='blamesheep.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/bump.gif[/img]","replace"=>"<img src='smilies/bump.gif' border='0' alt='bump.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/chainsaw.gif[/img]","replace"=>"<img src='smilies/chainsaw.gif' border='0' alt='chainsaw.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/closed.gif[/img]","replace"=>"<img src='smilies/closed.gif' border='0' alt='closed.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/enforcer.gif[/img]","replace"=>"<img src='smilies/enforcer.gif' border='0' alt='enforcer.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/ernaehrung004.gif[/img]","replace"=>"<img src='smilies/ernaehrung004.gif' border='0' alt='ernaehrung004.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/flour.gif[/img]","replace"=>"<img src='smilies/flour.gif' border='0' alt='flour.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/google.gif[/img]","replace"=>"<img src='smilies/google.gif' border='0' alt='google.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/gramps4.gif[/img]","replace"=>"<img src='smilies/gramps4.gif' border='0' alt='gramps4.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/gunner.gif[/img]","replace"=>"<img src='smilies/gunner.gif' border='0' alt='gunner.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/hatespammers.gif[/img]","replace"=>"<img src='smilies/hatespammers.gif' border='0' alt='hatespammers.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/headshot.gif[/img]","replace"=>"<img src='smilies/headshot.gif' border='0' alt='headshot.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/hug.gif[/img]","replace"=>"<img src='smilies/hug.gif' border='0' alt='hug.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/imo.gif[/img]","replace"=>"<img src='smilies/imo.gif' border='0' alt='imo.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/inq.gif[/img]","replace"=>"<img src='smilies/inq.gif' border='0' alt='inq.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/jackhammer.gif[/img]","replace"=>"<img src='smilies/jackhammer.gif' border='0' alt='jackhammer.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/jk.gif[/img]","replace"=>"<img src='smilies/jk.gif' border='0' alt='jk.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/laser.gif[/img]","replace"=>"<img src='smilies/laser.gif' border='0' alt='laser.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/laser2.gif[/img]","replace"=>"<img src='smilies/laser2.gif' border='0' alt='laser2.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/laser3.gif[/img]","replace"=>"<img src='smilies/laser3.gif' border='0' alt='laser3.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/blahblah.gif[/img]","replace"=>"<img src='smilies/blahblah.gif' border='0' alt='blahblah.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/machine_Gun.gif[/img]","replace"=>"<img src='smilies/machine_Gun.gif' border='0' alt='machine_Gun.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/moo.gif[/img]","replace"=>"<img src='smilies/moo.gif' border='0' alt='moo.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/newbie.gif[/img]","replace"=>"<img src='smilies/newbie.gif' border='0' alt='newbie.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/old.gif[/img]","replace"=>"<img src='smilies/old.gif' border='0' alt='old.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/owned.gif[/img]","replace"=>"<img src='smilies/owned.gif' border='0' alt='owned.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/ripper.gif[/img]","replace"=>"<img src='smilies/ripper.gif' border='0' alt='ripper.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/rocker2.gif[/img]","replace"=>"<img src='smilies/rocker2.gif' border='0' alt='rocker2.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/rocket3.gif[/img]","replace"=>"<img src='smilies/rocket3.gif' border='0' alt='rocket3.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/smash.gif[/img]","replace"=>"<img src='smilies/smash.gif' border='0' alt='smash.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/sniper.gif[/img]","replace"=>"<img src='smilies/sniper.gif' border='0' alt='sniper.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/soon.gif[/img]","replace"=>"<img src='smilies/soon.gif' border='0' alt='soon.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/spam1.gif[/img]","replace"=>"<img src='smilies/spam1.gif' border='0' alt='spam1.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/stupid.gif[/img]","replace"=>"<img src='smilies/stupid.gif' border='0' alt='stupid.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/transporter.gif[/img]","replace"=>"<img src='smilies/transporter.gif' border='0' alt='transporter.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/ttidead.gif[/img]","replace"=>"<img src='smilies/ttidead.gif' border='0' alt='ttidead.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/twocents.gif[/img]","replace"=>"<img src='smilies/twocents.gif' border='0' alt='twocents.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/w00t.gif[/img]","replace"=>"<img src='smilies/w00t.gif' border='0' alt='w00t.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/weirdo.gif[/img]","replace"=>"<img src='smilies/weirdo.gif' border='0' alt='weirdo.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/whenitsdone.gif[/img]","replace"=>"<img src='smilies/whenitsdone.gif' border='0' alt='whenitsdone.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/yeahthat.gif[/img]","replace"=>"<img src='smilies/yeahthat.gif' border='0' alt='yeahthat.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/zap.gif[/img]","replace"=>"<img src='smilies/zap.gif' border='0' alt='zap.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/paranoid.gif[/img]","replace"=>"<img src='smilies/paranoid.gif' border='0' alt='paranoid.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/worthy.gif[/img]","replace"=>"<img src='smilies/worthy.gif' border='0' alt='worthy.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/signs_word.gif[/img]","replace"=>"<img src='smilies/signs_word.gif' border='0' alt='signs_word.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/wacko.gif[/img]","replace"=>"<img src='smilies/wacko.gif' border='0' alt='wacko.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/censored.gif[/img]","replace"=>"<img src='smilies/censored.gif' border='0' alt='censored.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/drunk.gif[/img]","replace"=>"<img src='smilies/drunk.gif' border='0' alt='drunk.gif'>","type"=>"more"));
$tdb->add("smilies",array("bbcode"=>"[img]smilies/finger.gif[/img]","replace"=>"<img src='smilies/finger.gif' border='0' alt='finger.gif'>","type"=>"more"));

if (file_exists(DB_DIR.'/smilies.dat'))
  unlink(DB_DIR.'/smilies.dat');
echo "Smilie database created<p>";
?>
