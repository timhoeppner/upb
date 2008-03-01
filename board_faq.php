<?php
	require_once("./includes/class/func.class.php");
	$where = "Frequently Asked Questions - FAQ";
	require_once("./includes/header.php");
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
	require_once("./includes/board_help.php");
	require_once("./includes/board_post.php");
	require_once("./includes/board_view.php");
		echo "
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter(SKIN_DIR);
	require_once("./includes/footer.php");
?>