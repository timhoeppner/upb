<?php
require_once('./includes/upb.initialize.php');
$where = "<b>></b> More Smilies";

$cols_n = 6; //how many columns per row of the table
require_once('./includes/header_simple.php');
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<div id='simple_border'>
			<div class='simple_head'>Viewing additional smilies</div>
			<div class='simple_sub_smilie'>Click on a smilie image below to have it added to your post.</div>
			<table id='simple_table' style='background-color:#ffffff;' cellspacing='12'><tr>";

$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
$bdb->setFp("smilies","smilies");
$smilies = $bdb->query("smilies","id>'0'&&type='more'");

foreach ($smilies as $key => $value)
{
  $name = strmstr(strstr_after($value['replace'], "/"),"'",true);
  echo "<td class='simple_smilie_box'><A HREF=\"javascript:moresmilies('".$value['bbcode']."')\" ONFOCUS=\"filter:blur()\">".$value['replace']."</a></td>\n";
  if ($key%6 == 5)
    echo "</tr><tr>";
}

echo "</tr></table></tr></td></table></div></body></html>";
include_once('./includes/footer_simple.php');

?>

