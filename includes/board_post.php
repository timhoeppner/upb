<?php
		echo "
			<tr>
				<td class='area_2'><br />";
		echoTableHeading("This is a sample of the area used when making a new post", $_CONFIG);
		echo "
			<tr>
				<th colspan='2'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;' valign='top'><strong>Message:</strong>
					<div style='text-align:center;margin-top:20px;margin-bottom:20px;'>
						<img src='skins/default/images/font_buttons.gif' alt='' title='' /></div>
				<td class='area_2'><textarea name='message' id='look1'>[b] bold text [/b]  [i]italic text [/i] [offtopic]offtopic comment[/offtopic]
[center]centered text [/center]
[small]small text [/small]
[quote]quoted text [/quote]
[move]moving text [/move]
[img]images/sample_image.png [/img]
[blue] blue text [/blue][red]red text [/red][purple]purple text [/purple][green] green text[/green][yellow] yellow text[/yellow][white] white text[/white]
Smilies:
:) :( ;) :P :o :D (C) (M) (R) (E) (wallbash) (blink) (hmmlaugh) (crazy) (confused) (rofl) (noteeth)

[url]http://www.google.com[/url] [url=http://www.google.com]google[/url]
email me at : [email]myemail@myserver.com[/email]</textarea>
					<div style='padding:8px;'>
						<img src='smilies/smile.gif' alt=':)' title='' />&nbsp;&nbsp;
						<img src='smilies/frown.gif' alt=':(' title='' />&nbsp;&nbsp;
						<img src='smilies/wink.gif' alt=';)' title='' />&nbsp;&nbsp;
						<img src='smilies/tongue.gif' alt=':P' title='' />&nbsp;&nbsp;
						<img src='smilies/eek.gif' alt=':o' title='' />&nbsp;&nbsp;
						<img src='smilies/biggrin.gif' alt=':D' title='' />&nbsp;&nbsp;
						<img src='smilies/cool.gif' alt='(C)' title='' />&nbsp;&nbsp;
						<img src='smilies/mad.gif' alt='(M)' title='' />&nbsp;&nbsp;
						<img src='smilies/redface.gif' alt='(R)' title='' />&nbsp;&nbsp;
						<img src='smilies/rolleyes.gif' alt='(E)' title='' />&nbsp;&nbsp;<br />
						<img src='smilies/offtopic.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/rofl.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/confused.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/crazy.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/hm.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/hmmlaugh.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/blink.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/wallbash.gif' alt='(E)' title='' />&nbsp;&nbsp;
						<img src='smilies/noteeth.gif' alt='(E)' title='' /><div></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
		$skin_tablefooter";
?>