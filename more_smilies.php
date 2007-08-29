<?php
require_once('./includes/class/func.class.php');
$where = "<b>></b> More Smilies";
require_once(DB_DIR.'/smilies.dat');

sort($files_name);
$count = count($files_name);
$cols_n = 6; //how many columns per row of the table
require_once('./includes/header_simple.php');
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG['table_width_main']."' background='".SKIN_DIR."/images/cat_top_bg.gif'><tr><td colspan='2' bgcolor='white'>

<script language='javascript'>
<!--
function AddSmilie(Which){

add_msg = '[img]';
add_msg += Which;
add_msg +='[/img]';
opener.document.newentry.message.value += add_msg;

}
//-->
</script>
<table><tr>";

for ($i=0;$i<$count;$i++){
    if ($cols > $cols_n - 1){
    echo "</tr><tr>";
    $cols = 0;
    }
    $cols++;
    if ($files_name[$i] != "zzz.gif"){
    echo "<td><table><tr><td><A HREF=\"javascript:AddSmilie('smilies/moresmilies/$files_name[$i]')\" ONFOCUS=\"filter:blur()\">
              <img src=smilies/moresmilies/$files_name[$i] border=0></a></td></tr><tr><td>$files_name[$i]</td></tr></table>
          </td>";
    }
    else {
      $cols--; //if the picture was skipped
    }

}

echo "</tr></table></tr></td></table>$skin_tablefooter</body></html>";
include_once('./includes/footer_simple.php');
?>