<?php
require_once("config.php");
require_once("./includes/class/func.class.php");
require_once('./includes/header_simple.php');


$tdb->setFp("config", "config");
$tdb->setFp("ext_config", "ext_config");

$array = array("title" => "Category Sorting","description" => "Sort the categories in the order you want them to appear on the main page","form_object" => "list");

if($tdb->edit('ext_config',8,$array))
  echo "Settings for Category Sorting updated<p>";

?>
