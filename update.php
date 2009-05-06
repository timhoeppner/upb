<?php
if (TRUE !== is_writable('config.php')) die('Unable to continue with the installation process.  "config.php" in the root upb directory MUST exist and MUST BE writable.');
	if (filesize('config.php') > 0) {
    require_once('config.php');
	}

$current_update = '2.2.4';

if (substr(UPB_VERSION,0,1) == 1)
{
die("Your version is very outdated and uses a completely new database system.<br>Please install the latest version: $current_update");
}

function get_updates()
{
  $alter = str_replace('.','_',UPB_VERSION);
  $file_list = array();
  $d = dir(".");
  while (false !== ($entry = $d->read())) {
   if (substr_count($entry,'update') == 1)
    $file_list[] = $entry;
  }
  $d->close();

  

  $key_alter = array_search('update'.$alter.'.php',$file_list);

  if ($alter == '2_1_1b')
    $key_alter = array_search('update2_2_1.php',$file_list)-1;

  if ($alter == "1_0")
    $key_alter = -1;

  $files_needed = array_slice($file_list,$key_alter+1);

  return $files_needed;
}

$_SESSION['files'] = get_updates();
$_SESSION['update_version'] = $current_update;
?>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>UPB v2.2.4 Updater</title>
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />
<form action='<?php print $_SERVER['PHP_SELF']; ?>' method='post'>
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>myUPB v2.2.4 Updater</div>
		<table class='main_table' cellspacing='1'>
			<tr>
				<th style='text-align:center;'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_welcome'><div class='welcome_text'>If you have any problems, please seek support at <a href='http://www.myupb.com/'>myupb.com's support forums!</a></div></td>
			</tr>
			<tr>
				<td class='footer_3'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;'>
          <?php
					if (UPB_VERSION != $current_update)
          {
          ?>
          Thank you for choosing my Ultimate PHP Board.<br /><br />
					This script will guide you through the process of updating your myUPB bulletin board.<br />
          <?php
          $dir_777 = is_readable('./') && is_writable('./');
					if(!$dir_777) print "You have to chmod upb's root directory to 0777 before you can proceed";
					else {
          
          echo "<p>You are currently running v".UPB_VERSION." and the current version available is v".$current_update."<br>";
          echo (count($_SESSION['files']) == 1) ? "There is 1 update file that needs to be run.": "There are ".count($_SESSION['files']). " updates which will be run one after the other.";
          echo "<p>Please backup your skin, database and upload directories before proceeding.";
          echo "<p>If you need to input any information you will be prompted.<br>After each section of the upgrade has been completed you will be prompted to proceed to the next step.";
          echo '<p>Click on the "Proceed" to continue<br />';
          var_dump($_SESSION['files']);
	} ?><br /><br />
			<input type='button' onclick="location.href='<?php echo $_SESSION['files'][0];?>';" value='Proceed'>
      <?php
      }
      else
      {
        echo "<p>You are already running the latest release of myUPB.<p>Please delete install.php, complete_update.php and other files as they are a security risk";
      }
      ?>
      </td>
			</tr>
			<tr>
				<td class='footer_3'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
		</table>
		<div class='footer'><img src='skins/default/images/spacer.gif' alt='' title='' /></div>
	</div>
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - <?php echo date("Y",time()); ?></div>
</div>
</body>
</html>