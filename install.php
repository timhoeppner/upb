<?php
	// install.php
	// designed for Ultimate PHP Board
	// Author: Clark
	// Website: http://www.myupb.com
	// Version: 2.2.4
	// Using textdb Version: 4.4.1
session_start();
  ignore_user_abort();
	if (TRUE !== is_writable('config.php')) die('Unable to continue with the installation process.  "config.php" in the root upb directory MUST exist and MUST BE writable.');
	if (filesize('config.php') > 0) {
    require_once('config.php');
	}

if (!defined('DB_DIR'))
  require_once('./install/installation.php');
else
  $current_update = '2.2.4';
  define('UPB_VERSION','1.0');
  echo "update required from v".UPB_VERSION." to v$current_update<p>";

$file_list = array();
$d = dir("install");
while (false !== ($entry = $d->read())) {
   if (substr_count($entry,'update') == 1)
    $file_list[] = $entry;
}
$d->close();

//var_dump($file_list);

function get_updates($new,$filelist)
{
  $ver1 = false;
  if (substr(UPB_VERSION,0,1) != '1')
  {
    $ver_numerical = str_replace('.','',UPB_VERSION);
    $ver_numerical++;
  }
  else
  {
    $ver_numerical = 10;
    $ver1 = true;
  }

  $num_list = array();
  foreach ($filelist as $file)
  {
    $key =  str_replace(array('.php','_','update'),'',$file);
    $num_list[$key] = $file;
  }
  
  
  $update_numerical = str_replace('.','',$new);
  $file_list = array();

  for ($i = $ver_numerical; $i <= $update_numerical;$i++)
  {
    if (array_key_exists($i,$num_list))
      $files_needed[] = $num_list[$i];
  }
  return $files_needed;
}
$_SESSION['files'] = get_updates($current_update,$file_list);
var_dump($_SESSION);
?>
