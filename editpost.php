<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once("./includes/class/func.class.php");
	require_once("./includes/class/posts.class.php");
	$posts_tdb = new posts(DB_DIR, "posts.tdb");
	$posts_tdb->setFp("topics", $_GET["id"]."_topics");
	$posts_tdb->setFp("posts", $_GET["id"]);
	//$fRec = $tdb->get("forums", $_GET["id"]);
	//$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
	$pRec = $posts_tdb->get("posts", $_GET["p_id"]);
	$where = "Edit Post";
	//$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"]." <a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Edit Post";
	require_once("./includes/header.php");
	if (!(isset($_GET["id"]) && isset($_GET["t_id"]) && isset($_GET["p_id"]))) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Not enough information to perform this function.</div></div>");
	if (!($tdb->is_logged_in())) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>You are not logged in, therefore unable to perform this action.</div></div>");
	if ($pRec[0]["user_id"] != $_COOKIE["id_env"] && $_COOKIE["power_env"] < 2) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>You do not have the rights to perform this action.</div></div>");
	if (isset($_POST["message"])) {
		$posts_tdb->edit("posts", $_GET["p_id"], array("message" => encode_text(stripslashes($_POST["message"])), "edited_by_id" => $_COOKIE["id_env"], "edited_by" => $_COOKIE["user_env"], "edited_date" => mkdate()));
		echo "
						<div class='alert_confirm'>
						<div class='alert_confirm_text'>
						<strong>Redirecting:</div><div style='padding:4px;'>
						Successfully edited post.
						</div>
						</div>";
		require_once("./includes/footer.php");
		redirect("viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"], 2);
		exit;
	} else {
		echo "
	<form action='editpost.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&p_id=".$_GET["p_id"]."' METHOD=POST name='newentry'>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "

			<tr>
				<td class='area_1' style='padding:8px;' valign='top'><strong>Message:</strong>";
						
		echo "<div style='text-align:center;'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></div></td>
				<td class='area_2'>".bbcodebuttons('look1')."<textarea name='message' id='look1'>".$_POST['newedit']."</textarea>
					<div style='padding:8px;'>
						<a href=\"javascript:SetSmiley(':)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/smile.gif' alt=':)' title=':)' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley(':(')\" ONFOCUS=\"filter:blur()\"><img src='smilies/frown.gif' alt=':(' title=':(' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley(';)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/wink.gif' alt=';)' title=';)' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley(':P')\" ONFOCUS=\"filter:blur()\"><img src='smilies/tongue.gif' alt=':P' title=':P' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley(':o')\" ONFOCUS=\"filter:blur()\"><img src='smilies/eek.gif' alt=':o' title=':o' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley(':D')\" ONFOCUS=\"filter:blur()\"><img src='smilies/biggrin.gif' alt=':D' title=':D' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(C)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/cool.gif' alt='(C)' title='(C)' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(M)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/mad.gif' alt='(M)' title='(M)' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(R)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/redface.gif' alt='(R)' title='(R)' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(E)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/rolleyes.gif' alt='(E)' title='(E)' /></a>&nbsp;&nbsp;
					<br />
						<a href=\"javascript:SetSmiley('(offtopic)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/offtopic.gif' alt='offtopic' title='offtopic' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(rofl)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/rofl.gif' alt='rofl' title='rofl' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(confused)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/confused.gif' alt='confused' title='confused' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(crazy)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/crazy.gif' alt='crazy' title='crazy' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(hm)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/hm.gif' alt='hm' title='hm' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(hmmlaugh)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/hmmlaugh.gif' alt='hmmlaugh' title='hmmlaugh' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(blink)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/blink.gif' alt='blink' title='blink' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(wallbash)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/wallbash.gif' alt='wallbash' title='wallbash' /></a>&nbsp;&nbsp;
						<a href=\"javascript:SetSmiley('(noteeth)')\" ONFOCUS=\"filter:blur()\"><img src='smilies/noteeth.gif' alt='noteeth' title='noteeth' /></a></div></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' style='text-align:center;' colspan='2'>
        <input type=submit value='Edit'>
        <input type=reset value='Reset'>";
        echo "<input type=button onClick=\"javascript:window.location='viewtopic.php?id=".$_GET['id']."&t_id=".$_GET['t_id']."#".$_GET['p_id']."' value='Cancel Edit'>
        </td>
			</tr>";
      echoTableFooter($_CONFIG['skin_dir']);
      echo "
	</form>";
	}
	require_once("./includes/footer.php");
?>
