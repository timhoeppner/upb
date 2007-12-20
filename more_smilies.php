<?php
require_once('./includes/class/func.class.php');
$where = "<b>></b> More Smilies";

$cols_n = 6; //how many columns per row of the table
require_once('./includes/header_simple.php');
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG['table_width_main']."' background='".SKIN_DIR."/images/cat_top_bg.gif'><tr><td colspan='2' bgcolor='white'>\n
<table><tr>";

$bdb = new tdb(DB_DIR.'/','bbcode.tdb');
$bdb->setFp("smilies","smilies");
$smilies = $bdb->query("smilies","id>'0'&&type='more'");

foreach ($smilies as $key => $value)
{
  $name = str_replace(array("[img]smilies/","[/img]"),"",$value['bbcode']);
  echo "<td><table><tr><td align='center'><A HREF=\"javascript:moresmilies('".$value['bbcode']."')\" ONFOCUS=\"filter:blur()\">".$value['replace']."</a></td></tr>\n<tr><td align='center'>$name</td></tr></table>
          </td>\n";
  if ($key%6 == 5)
    echo "</tr><tr>";
}

echo "</tr></table></tr></td></table>$skin_tablefooter</body></html>";
include_once('./includes/footer_simple.php');
?>
