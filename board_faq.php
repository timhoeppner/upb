<?php
	require_once("./includes/upb.initialize.php");
	$where = "Frequently Asked Questions - FAQ";
	require_once("./includes/header.php");
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
			echo "
			<tr>
				<th>Using BBcode</th>
			</tr>
			<tr>
				<td class='area_2'>
Smilies can be added to a post by clicking on the icons.
<br />
Using BBcode is simple. You can either enter them manually or use the list and buttons about. 
Either select the text you wish to apply the formatting to or click the button and then place the text between the [] [/] tags e.g. [b]bold[/b]<br>
Text can be made bold by putting the text between [b][/b] e.g. [b]bold text[/b]
Text can also be made italic by putting the text between
<br />
<br />
<br />
A link can be added into a post in several ways. If you wish to add a clickable text link like
'http://www.whatever.com' use this code [url]http://www.whatever.com[/url].
<br />
<br />
If you wish to put a link behind a text use this code [url=http://www.whatever.com]click here[/url]
<br />
<br />
Also an email link can be added. For email links use [email]myemail@myserver.com[/email]
<br />
<br />
To add a picture to a post use [img]http://www.images.com/mypicture.jpg[/img]
<br />
<br />
Another feauture is an offtopic comment. For this use [offtopic] offtopic comment [/offtopic].</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
echo "<tr><th>RSS Feeds</th></tr><tr><td class='area_2'>The <img src='images/rss.png' class='rss' alt='RSS'> icon indicates there is an RSS Feed available.<p>This allows you to keep track of new topics or posts without visiting the forum.<p>Just click on the image and subscribe to the feed using your browser or paste the URL into the RSS Reader software you are using.</td></tr>";
		echo "
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>";
		echoTableFooter(SKIN_DIR);
	require_once("./includes/footer.php");
?>
