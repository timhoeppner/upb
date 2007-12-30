<?php
$font_s="1";
$font_m="1";
$font_l="1";
$font_face="verdana";
$font_color_main="#000000";
$font_color_header="#FFFFFF";
$font_color_category="#FFFFFF";
$bgcolor="#000000";
$link="#000020";
$vlink="#000020";
$alink="#000020";
$header="#24374A";
$divider="#18284c";
$category="#24374A";
$table1="#eff1f3";
$font1="#18284c";
$table2="#FFFFFF";
$font2="#18284c";
$border="#000000";
$alternatingcolor1="#eff1f3";
$alternatingcolor2="#FFFFFF";
$statscolor="#eff1f3";
$statscolor1="#eff1f3";
$statscolor2="#FFFFFF";
$hovercolor1="#eff1f3";
$hovercolor2="#FFFFFF";
$outsideborder="#eef0f5";
$insideborder="#FFFFFF";


function echoTableHeading($display, $_CONFIG) { //set $display to 85
    echo "<table cellspacing=0 cellpadding=0 width='".$_CONFIG["table_width_main"]."' align=center>
                  	<tbody>
                   	<tr id=cat>
                   	<td width=30><img src='".$_CONFIG["skin_dir"]."/images/cat_top_left.gif' width=132 border=0></td>
                  	<td valign=middle background=".$_CONFIG["skin_dir"]."/images/cat_top_bg.gif border=0><p align=center><b><font face='Verdana' size='1' color='#ffffff'><b>".$display."</b></font></b></p></td>
                   	<td width=30><p align=right><img src='".$_CONFIG["skin_dir"]."/images/cat_top_right.gif' width=134 border=0></p></td>
    	           	</tr>
    		   	</tbody>
    		   	</table>";
}
$skin_tablefooter="<table cellspacing=0 cellpadding=0 width='".$_CONFIG["table_width_main"]."' align=center border=0>

                   <tbody> 

       		   <tr> 

        	   <td width='100%' valign='top' align=left background='".$_CONFIG["skin_dir"]."/images//cat_bottom_bg.jpg'><img src='".$_CONFIG["skin_dir"]."/images//cat_bottom_left.gif' border=0></td>

       	 	   <td valign='top' align=right><img src='".$_CONFIG["skin_dir"]."/images//cat_bottom_right.gif' border=0></td>

        	   </tr>

                   </tbody> 

                   </table><br>";

?>
