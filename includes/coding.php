<?php
	function echoTableHeading($display, $_CONFIG) {
		//set $display to 85
		echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>".$display."</div>
		<table class='main_table' cellspacing='1'>
		<tbody>";
	}
	$skin_tablefooter = "
		</tbody>
		</table>
		<div class='footer'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";
?>
