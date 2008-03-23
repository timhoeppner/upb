<?php
//coding.php has gone
//skin.css has gone

require_once("./includes/upb.initialize.php");
//FPs already set in func.class.php
//dump($_POST);
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
			echo "<p>This release contains many new features and bug fixes. For the changelog, please see the readme or visit MyUPB.com";
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

      $members = $tdb->query('users',"level='3'");
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
    $tdb->addField('users', array('newTopicsData', 'memo'));
    $tdb->addField('users', array('lastvisit', 'number', 14));
    $tdb->addField('users', array('reg_code', 'memo'));
    $config_tdb->addField('config_ext', array('data_list', 'memo'));
    $config_tdb->addField('config', array('data_type', 'string', 7)); //TODO: Add code to copy values from config_ext to config
    $tdb->addField('uploads', array('file_loca', 'string', 80));
    $tdb->addField('uploads', array('user_level', 'number', 1));

    if($post_tdb->isTable('trackforums')) $post_tdb->removeTable('trackforums');
    if($post_tdb->isTable('tracktopics')) $post_tdb->removeTable('tracktopics');
    echo "</td></tr>";

    //move lastvisit information to the member database
    $lastvisit_file = file_get_contents(DB_DIR . '/lastvisit.dat');
    $id = 1;
    while(strlen($lastvisit_file) > 0) {
        $lastvisit = substr($lastvisit_file, 0, 14);
        $lastvisit_file = substr($lastvisit_file, 14);
        $tdb->edit('users', $id, array('lastvisit' => $lastvisit));
        $id++;
    }
    echo "Last visit information inserted<p>";

    //create superuser
    $tdb->edit('users', $_POST['superad'], array('level' => 9));
    echo "Super Admin Set<p>";
}

//Move the file OUT of the database and into the uploads directory

$uploads = $tdb->listRec('uploads', 1, -1);
foreach($uploads as $file) {
    $file_name = md5(uniqid(rand(), true));
    $f = fopen($_CONFIG['fileupload_location'].'/'.$file_name, 'xb'); //needed to add filename to fopen
    fwrite($f, $file['data']);
    fclose($f);
    $tdb->edit('uploads', $file['id'], array('user_level' => 0, 'file_loca' => $file_name));
}
$tdb->removeField('uploads', 'data');

$del_list = array('pm_version', 'avatar1', 'avatar2', 'avatar3', 'avatar4', 'avatar5', 'avatar6', 'avatar7', 'avatar8', 'avatar9', 'pm_max_outbox_msg');
foreach($del_list as $string) {
    $config_tdb->delete($string);
}
//How to add more Mini Categories to the config_org.dat file
$post_settings_id = $config_tdb->addMiniCategory('Posting Settings', 'config');
$reg_setting_id = $config_tdb->addMiniCategory('Registration Settings', 'regist', '8', false);

//Make Forum/Users Settings Area, Make another sub category for users for "other" to put security_code in.
$config_tdb->add('security_code', '1', 'regist', 'bool', 'checkbox', $reg_setting_id, '2', 'Enable Security Code', 'Enable the CAPTCHA security code image for new user registration<br><strong>Enabling this is recommended</strong>');
$config_tdb->add('banned_words', 'shit,fuck,cunt,pussy,bitch,arse', 'config', 'text', 'hidden', '', '', '', '');
$config_tdb->add('email_mode', 'true', 'config', 'bool', 'hidden', '', '', '', '');
$config_tdb->add('custom_avatars', '1', 'regist', 'number', 'dropdownlist', '8', '2', 'Custom Avatars', 'Allow users to link or upload their own avatars instead of choosing them locally in images/avatars/', 'a:3:{i:0;s:7:"Disable";i:1;s:4:"Link";i:2;s:6:"Upload";}');

$config_tdb->add('disable_reg', '0', 'regist', 'bool', 'checkbox', $reg_setting_id, '1', 'Disable Registration', 'Checking this will disable public registration (deny access to register.php), and only admins will be able to add users (Add button on "Manage Members" section)');
$config_tdb->add('reg_approval', '0', 'regist', 'bool', 'checkbox', $reg_setting_id, '3', 'Approve New Users', 'Checking this will mean after new users register, their account will be disabled until an admin approves their account via "Manage Members"');

/*  Correct way to edit values in config */

$config = array();
$regist = array();
$config[] = array('name' => 'ver', 'value' => '2.2.1');
$config[] = array("name" => "admin_catagory_sorting", "form_object" => "hidden", "data_type" => "string");
$config[] = array("name" => "posts_per_page", 'minicat'=>$post_settings_id,'sort'=>1);
$config[] = array("name" => "topics_per_page", 'minicat'=>$post_settings_id,'sort'=>2);
$config[] = array('name' => 'fileupload_location', "form_object" => "hidden", "data_type" => "string");
$config[] = array('name' => 'fileupload_size', 'description' => 'In kilobytes, type in the maximum size allowed for file uploads<br><i>Note: Setting to 0 will <b>disable</b> uploads</i>', 'minicat'=>$post_settings_id,'sort'=>4);
$config[] = array('name' => 'censor', 'minicat'=>$post_settings_id,'sort'=>5);
$config[] = array('name' => 'sticky_note', 'minicat'=>$post_settings_id,'sort'=>6);
$config[] = array('name' => 'sticky_after', 'minicat'=>$post_settings_id,'sort'=>7);
$config[] = array('name' => 'newuseravatars', 'value' => '50', 'type' => 'regist', 'data_type' => 'number', 'form_object' => 'text', 'minicat' => '8', 'sort' => '1', 'title' => 'New User Avatars', 'description' => 'Prevent new users from choosing their own avatars (if "Custom Avatars" is enabled), by defining a minimum post count they must have (Set to 0 to disable)');
$regist[] = array('name' => 'register_msg', 'description' => 'This is the message for confirmation of registration.<br>(options: &lt;login&gt;, &lt;password&gt;, and &lt;url&gt;)');
$config_tdb->editVars('config', $config, true);

$tdb = new tdb('', '');
$tdb->createDatabase(DB_DIR."/", "bbcode.tdb");
$tdb->tdb(DB_DIR.'/', 'bbcode.tdb');
$tdb->createTable('smilies', array(array('id', 'id'), array('bbcode', 'memo'),array('replace','memo'),array('type','string',4)));
$tdb->createTable('icons',array(array('id','id'),array('filename','memo')));
$tdb->removeField('users', 'mail_list');
$tdb->cleanUp();
$tdb->setFp("smilies","smilies");
$tdb->setFp("icons","icons");
for ($i = 1;$i<22;$i++)
{
  $filename = 'icon'.$i.'.gif';
  $tdb->add('icons',array("filename"=>$filename));
}

//type has two possible values
//main is shown on main page, more is shown on more smilies page
$tdb->add('smilies',array("bbcode"=>" :)","replace"=> " <img src='./smilies/smile.gif' border='0' alt=':)'> ","type" => "main"));
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

foreach ($more as $smiley)
{
  $tdb->add("smilies",array("bbcode"=>"[img]".$smiley."[/img]","replace"=>"<img src='./smilies/".$smiley."' border='0' alt='".$smiley."'>","type"=>"more"));
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
echoTableFooter(SKIN_DIR);
echo "
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2008</div>
</div>
</body>
</html>";
?>
