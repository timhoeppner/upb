<?php
		echo "
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2'>
<span class='link_1'>If you submit the post above, then this is how it will look in a topic:</span></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2'><br />";
		echoTableHeading("This is just a sample topic", $_CONFIG);
		echo "
			<tr>
				<th><div class='post_name'><a href='#'>Sample User</a></div></th>
				<th><div style='float:left;'><img src='icon/icon1.gif'></div>
					<div align='right'>
						<div class='button_pro1'><a href='#'>X</a></div>
						<div class='button_pro1'><a href='#'>Edit</a></div>
						<div class='button_pro1'><a href='#'>\"Quote\"</a></div>
						<div class='button_pro1'><a href='#'>Add Reply</a></div>
					</div></th>
			</tr>
			<tr>
				<td class='area_2' valign='top' style='width:15%;'><br />
					<img src='images/avatars/noavatar.gif' alt='' title='' /><br />
					<div class='post_info'><strong>N00bie!</strong></div>
					<div class='post_info'>
						<strong>Posts:</strong> 15
						<br />
						<strong>Registered:</strong>
						<br />
						00-00-0000
					</div>
				</td>
				<td class='area_2' valign='top'>
					<div style='padding:12px;margin-bottom:20px;'>
						<b> bold text </b>  <i>italic text </i> <small>small text </small><font color='blue' size='1' face='tahoma'>Offtopic: offtopic comment</font>   <br />
						<center>centered text </center>  <br />
						<blockquote><font size='1' face='tahoma'>quote:</font><hr>quoted text <br /><hr></blockquote>  <br />
						<marquee>moving text </marquee>  <br />
						<img src='images/sample_image.png' alt='' title='' />  <br />
						<font color='blue'> blue text </font><font color='red'>red text </font><font color='fuchsia'>purple text </font><font color='lime'> green text</font><font color='yellow'> yellow text</font><font color='white'> white text</font>  <br />
						<br />Smilies:  <br />
						<img src='smilies/smile.gif'> <img src='smilies/frown.gif'> <img src='smilies/wink.gif'> <img src='smilies/tongue.gif'> <img src='smilies/eek.gif'> <img src='smilies/biggrin.gif'> <img src='smilies/cool.gif'> <img src='smilies/mad.gif'> <img src='smilies/redface.gif'> <img src='smilies/rolleyes.gif'> <img src='smilies/wallbash.gif'> <img src='smilies/blink.gif'> <img src='smilies/hmmlaugh.gif'> <img src='smilies/crazy.gif'> <img src='smilies/confused.gif'> <img src='smilies/rofl.gif'> <img src='smilies/noteeth.gif'> <br />
						<br /><a href='http://www.google.com' target='_blank'>www.google.com</a>&nbsp;&nbsp; <a href='http://www.google.com' target='_blank'>google</a>
						<br />email me at : <a href='mailto:myemail@myserver.com'>myemail@myserver.com</a> <br />
					</div>
					<div style='padding:12px;'><div class='signature'>Sample Signature....</div></div></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2'>
<div class='post_edited'>Last edited by: <a href='#'><strong>Sample Editor</strong></a> on 00-00-0000</div>
					<div class='button_pro2'><a href='#'>Profile</a></div>
					<div class='button_pro2'><a href='#'>Homepage</a></div>
					<div class='button_pro2'><a href='#'>Email Sample User</a></div></td>
			</tr>
		$skin_tablefooter
				</td>
			</tr>";
?>