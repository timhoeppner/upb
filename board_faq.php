<?
require_once("./includes/class/func.class.php");
$where = "Frequently Asked Questions - FAQ";
require_once("./includes/header.php");

echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "<table cellspacing=1 bgcolor='#000000' WIDTH='".$_CONFIG['table_width_main']."'><tr><td colspan='2' bgcolor='white'><center><table width='695' border='0' cellspacing='1' cellpadding='4' align='center' bgcolor=#FFFFFF>";
?><tr><td align="left"><?php require_once("./includes/board_help.php"); ?><br><br><u>If it's still confusing, look at the following example:  <br><br></u>
<?php require_once("./includes/board_post.php"); ?>
</td></tr><tr><td><u>If you submit the post above, this is how it will look like in the forum:</u></td></tr><tr><td>
<?php require_once("./includes/board_view.php"); ?>
</td></tr></table></center></tr></td></table>
<?php echo $skin_tablefooter;
require_once("./includes/footer.php");
?>