<?php
//coding.php has gone
//skin.css has gone

require_once("config.php");
require_once("./includes/class/func.class.php");
$tdb->setFp("config", "config");
$tdb->setFp("ext_config", "ext_config");
$tdb->setFP("members","members");
dump($_POST);
$proceed = true;
  echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>\n
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n
<head>\n
<title>UPB v2.1.1b -> v2.2.1 Upgrade</title>\n
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />\n
</head>\n
<body>\n
<div id='upb_container'>\n
	<div class='main_cat_wrapper2'>\n
		<table class='main_table_2' cellspacing='1'>\n
			<tr>\n
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>\n
			</tr>\n
		</table>\n
	</div>\n
	<br />\n
	<br />\n";

  echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>myUPB v2.1.1b -> v2.2.1 Upgrade</div>
		<form method='POST' action='".$_SERVER['PHP_SELF']."'>
    <table class='main_table' cellspacing='1'><tbody>";
if (!isset($_POST['next']) or empty($_POST))
{
      echo "<tr>
				<th colspan='2'><strong>Welcome to myUPB v2.2.1</strong></th>
			</tr>
			<tr>
			<td class='area_2' colspan='2'>Welcome to myUPB v2.2.1<p>
			You are currently using v".UPB_VERSION;
			if (UPB_VERSION != "2.1.1b")
			{
        echo "<p>You will need to upgrade to version 2.1.1b first in order to upgrade to version 2.2.1<br>This is due to configuration changes that have been implemented";
        $proceed = false;
      }
      else
			echo "<p>This release contains many new features and bug fixes";
			echo "</td>
			</tr>";
			echo "<tr>
				<th colspan='2'><strong>Super Administrator Creation</strong></th>
			</tr>
			<tr>
			<td colspan='2' class='area_2'>Please choose an administrator account to be a Super Administrator account.<p>
			A Super Administrator's account cannot be deleted or banned and it's usergroup can't be changed.<p>This is to prevent an administrator hijacking the forum and removing the admin rights of the board owner.<br><strong>Once selected it can't be changed.</strong>
			</td>
      </tr>";
      echo "
      <tr><td class='area_1' style='width:35%;padding:8px;'>Super Administrator Account</td>
      <td class='area_2'>";
      $tdb->addField('members', array('newTopicsData', 'memo'));
      //$tdb->addField('members', array('superuser', 'string', 1));
      //$tdb->addField('members', array('lastvisit', 'number', 10));

      $members = $tdb->query('members',"level='3'");
      echo "<select id='superad' name='superad' size='1'>";
      foreach ($members as $member)
      {
          echo "<option value='".$member['id']."'>".$member['user_name']."</option>";
      }
      echo "</select>";
      //dump($members);
      echo "</td></tr>";

}
else if($_POST['next'] == 2)
{
echo "<tr>
				<th colspan='2'><strong>Updating Database</strong></th>
			</tr>
			<tr>
			<td colspan='2' class='area_2'>
			<td class='area_2'>";
      //$tdb->addField('members', array('superuser', 'string', 1));
      //$tdb->addField('members', array('lastvisit', 'number', 10));
      echo "</select>";
      //dump($members);
      echo "</td></tr>";

//move lastvisit information to the member database


foreach ($members as $member)
{
  //$tdb->edit('members',$member['id'],array("lastvisit"=>0));
}

echo "Last visit information inserted<p>";

//create superuser
//$tdb->addField('members', array('superuser', 'string', 1));
//$tdb->edit('members',1,array("superuser"=>"Y"));

echo "Super-user created<p>";
}




//$tdb->add("ext_config", array("name" => "security_code", "value" => "1", "type" => "config", "title" => "Enable Security Code", "description" => "Enable/Disable the security code image for new user registration<br>Enabling this is recommended", "form_object" => "checkbox", "minicat" => "1", "sort" => "16"));
//$tdb->add("config", array("name" => "security_code", "value" => "1", "type" => "config"));
//$tdb->add("ext_config", array("name" => "banned_words", "value" => "shit,fuck,cunt,pussy,bitch,arse", "type" => "config", "title" => "Banned Words", "description" => "Enter any words you want to censor separated by commas", "form_object" => "text", "minicat" => "1", "sort" => "12"));
//$tdb->add("config", array("name" => "banned_words", "value" => "shit,fuck,cunt,pussy,bitch,arse", "type" => "config"));

/*$tdb->edit("config",1,array('value'=>'2.1.1b'));
$tdb->edit("ext_config",1,array('value'=>'2.1.1b'));
$tdb->edit("ext_config",20,array('sort'=>'17'));
$tdb->edit("ext_config",16,array('sort'=>'19'));
$tdb->edit("ext_config",10,array('sort'=>'11'));

$tdb->edit("ext_config",10,array('form_object'=>'drop','title'=>'Skin Selection','description'=>'Choose a skin'));

$tdb->edit("ext_config",11,array('form_object'=>'text'));
die();
$res = $tdb->query('ext_config',"id>'0'");
dump($res);
$tdb->edit("ext_config",21,array('value'=>'1'));
$tdb->edit("config",21,array('value'=>'1'));
die();

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

//$tdb = new tdb('', '');
//$tdb->createDatabase(DB_DIR."/", "bbcode.tdb");
$tdb->tdb(DB_DIR.'/', 'bbcode.tdb');
//$tdb->createTable('smilies', array(array('id', 'id'), array('bbcode', 'memo'),array('replace','memo'),array('type','string',4)));
//$tdb->createTable('icons',array(array('id','id'),array('filename','memo')));
//$tdb->cleanUp();
$tdb->setFp("smilies","smilies");
$tdb->setFp("icons","icons");
//for ($i = 1;$i<22;$i++)
//{
//  $filename = 'icon'.$i.'.gif';
//  $tdb->add('icons',array("filename"=>$filename));
//}

//type has two possible values
//main is shown on main page, more is shown on more smilies page
/*$tdb->add('smilies',array("bbcode"=>" :)","replace"=> " <img src='./smilies/smile.gif' border='0' alt=':)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :(", "replace"=>" <img src='./smilies/frown.gif' border='0' alt=':('> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" ;)","replace"=> " <img src='./smilies/wink.gif' border='0' alt=';)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :P","replace"=> " <img src='./smilies/tongue.gif' border='0' alt=':P'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :o","replace"=> " <img src='./smilies/eek.gif' border='0' alt=':o'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" :D","replace"=> " <img src='./smilies/biggrin.gif' border='0' alt=':D'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (C)","replace"=> " <img src='./smilies/cool.gif' border='0' alt='(C)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (M)","replace"=> " <img src='./smilies/mad.gif' border='0' alt='(M)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (confused)","replace"=> " <img src='./smilies/confused.gif' border='0' alt='(confused)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (crazy)","replace"=> " <img src='./smilies/crazy.gif' border='0' alt='(crazy)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (hm)","replace"=> " <img src='./smilies/hm.gif' border='0' alt='(hm)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (hmmlaugh)","replace"=> " <img src='./smilies/hmmlaugh.gif' border='0' alt='(hmmlaugh)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (offtopic)","replace"=> " <img src='./smilies/offtopic.gif' border='0' alt='(offtopic)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (blink)","replace"=> " <img src='./smilies/blink.gif' border='0' alt='(blink)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (rofl)","replace"=> " <img src='./smilies/rofl.gif' border='0' alt='(rofl)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (R)","replace"=> " <img src='./smilies/redface.gif' border='0' alt='(R)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (E)","replace"=> " <img src='./smilies/rolleyes.gif' border='0' alt='(E)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (wallbash)","replace"=> " <img src='./smilies/wallbash.gif' border='0' alt='(wallbash)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" (noteeth)","replace"=> " <img src='./smilies/noteeth.gif' border='0' alt='(noteeth)'> ","type" => "main"));
$tdb->add('smilies',array("bbcode"=>" LOL","replace"=> " <img src='./smilies/lol.gif' border='0' alt='LOL'> ","type" => "main"));

//MORE SMILIES -- need to add code to check for custom smilies added to more smilies folder.
$more = directory("./smilies/moresmilies/","gif,jpg");
dump($more);
foreach ($more as $smiley)
{
  $tdb->add("smilies",array("bbcode"=>"[img]".$smiley."[/img]","replace"=>"<img src='./smilies/".$smiley."' border='0' alt='".$smiley."'>","type"=>"more"));
  echo "\$tdb->add(\"smilies\",array(\"bbcode\"=>\"[img]smilies/".$smiley."[/img]\",\"replace\"=>\"<img src='./smilies/".$smiley."' border='0' alt='".$smiley."'>\",\"type\"=>\"more\"))<p>";
  rename('./smilies/moresmilies/'.$smiley,'./smilies/'.$smiley);
}
$more = directory("./smilies/moresmilies/","gif,jpg");
if (count($more) == 0)
{
  $contents = directory("./smilies/moresmilies/");
  foreach ($contents as $file)
    unlink("./smilies/moresmilies/".$file);
}
rmdir("./smilies/moresmilies");

if (file_exists(DB_DIR.'/smilies.dat'))
  unlink(DB_DIR.'/smilies.dat');
echo "Smilie database created<p>";
echo "More Smilies files converted<p>";
*/
echo "<tr>
				<td colspan='2' class='footer_3'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>";
			if ($proceed === true)
      {
        if (isset($_POST['next']))
          $next = (int) $_POST['next'] + 1;
        else
          $next = 2;

        echo "<tr>
				<td colspan='2' class='footer_3a' style='text-align:center;'><input type='hidden' name='next' value='$next'><input type='submit' value='Next >>' name='submit'></td>
			</tr>";
			}
echoTableFooter($_CONFIG['skin_dir']);
echo "
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2008</div>
</div>
</body>
</html>";
?>
