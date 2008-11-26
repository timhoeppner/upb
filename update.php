<?php
require_once("./includes/upb.initialize.php");

$updates = array('2.1.1b','2.2.1','2.2.2','2.2.3');

$lines = explode("\n", file_get_contents('config.php'));
for($i=0;$i<count($lines);$i++) {
    if(FALSE === strpos($lines[$i], 'UPB_VERSION')) continue;
    else
    {
      //echo $lines[$i];
      $sections = explode(",",$lines[$i]);
      $current_version = str_replace("'","",$sections[1]);
      
      }
    }
dump($updates);
//$current_version = "2.1.1b";
echo "Current version: ".$current_version."<p>";

$key = array_search($current_version,$updates);

if (in_array($current_version,$updates))
  echo "Found in array.<p>";

echo "Key found is ".$key."<p>";

echo "Next key is ".($key+1)."<p>";

echo "Next update is ".$updates[$key+1]; 

$array = array(0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red');

$key = array_search('green', $array); // $key = 2;
echo "<p>".$key;
$key = array_search('red', $array);   // $key = 1;
echo "<p>".$key;
?>
