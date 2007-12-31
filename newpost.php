<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2
	require_once('./includes/class/func.class.php');
	require_once('./includes/inc/post.inc.php');
	require_once("./includes/class/upload.class.php");
	$fRec = $tdb->get("forums", $_GET["id"]);
	$posts_tdb = new functions(DB_DIR."/", "posts.tdb");
	$posts_tdb->setFp("topics", $_GET["id"]."_topics");
	$posts_tdb->setFp("posts", $_GET["id"]);
	$where = "<a href='viewforum.php?id=".$_GET["id"]."'>".$fRec[0]["forum"]."</a> ".$_CONFIG["where_sep"];
	if ($_GET["t_id"] == "") {
		$where .= " New Topic";
	} else {
		$tRec = $posts_tdb->get("topics", $_GET["t_id"]);
		$where .= " <a href='viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."'>".$tRec[0]["subject"]."</a> ".$_CONFIG["where_sep"]." Post Reply";
	}
	if (!isset($a)) $a = 0;
	require_once('./includes/header.php');
	if (!($tdb->is_logged_in())) {
		$_COOKIE["user_env"] = "guest";
		$_COOKIE["power_env"] = 0;
		$_COOKIE["id_env"] = 0;
	}
	if ($_COOKIE["power_env"] < $fRec[0]["post"] && $_GET["t"] == 1 || $_COOKIE["power_env"] < $fRec[0]["reply"] && $_GET["t"] == 0) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>You do not have the rights to perform this action.</div></div>");
	if (!($_GET["id"] != "" && is_numeric($_GET["id"]))) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Invalid Forum ID/Information.</div></div>");
	if (!($_GET["t_id"] != "" && is_numeric($_GET["t_id"]) || $_GET["t"] != 0)) exitPage("<div class='alert'><div class='alert_text'>
		<strong>Caution!</strong></div><div style='padding:4px;'>Invalid Topic ID/Information.</div></div>");
	if ($_POST["a"] == "1") {
		if (isset($_POST['subject'])) $_POST['subject'] = htmlentities(stripslashes($_POST["subject"]));
		$_POST['message'] = htmlentities(stripslashes($_POST["message"]));
		if ($_POST["icon"] == "") exitPage("<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>Please select a message icon.</div></div>");
		if ($_GET["t"] == 1 && trim($_POST["subject"]) == "") exitPage("<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>You must enter a subject!</div></div>");
		if ($_POST["message"] == "") exitPage("<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>You must type in a message!</div></div>");
		if ($_GET["t"] != 1 && isset($_GET["t_id"]) && (bool) $tRec[0]["locked"]) exitPage("<div class='alert'><div class='alert_text'>
			<strong>Caution!</strong></div><div style='padding:4px;'>The topic is closed to further posting.</div></div>");
		//FILE UPLOAD BEGIN
		$uploadText = '';
		$uploadId = 0;
		if (trim($_FILES["file"]["name"]) != "") {
			$upload = new upload(DB_DIR, $_CONFIG["fileupload_size"]);
			$uploadId = $upload->storeFile($_FILES["file"]);
			if ($uploadId === false) $uploadId = 0;
		}
		//END
		if ($_GET["t"] == 1) {
			if (!isset($_POST["sticky"])) $_POST["sticky"] = "0";
			if (!isset($_POST["locked"])) $_POST["locked"] = "0";
			$_POST["subject"] = trim($_POST["subject"], $_CONFIG['stick_note']);
			if (trim($_POST["subject"]) == "") exitPage("<div class='alert'><div class='alert_text'>
				<strong>Caution!</strong></div><div style='padding:4px;'>You must enter a subject!</div></div>");
			$_GET["t_id"] = $posts_tdb->add("topics", array(
			"icon" => $_POST["icon"],
				"subject" => $_POST["subject"],
				"topic_starter" => $_COOKIE["user_env"],
				"sticky" => $_POST["sticky"],
				"replies" => "0",
				"views" => "0",
				"locked" => $_POST["locked"],
				"last_post" => mkdate(),
				"user_name" => $_COOKIE["user_env"],
				"user_id" => $_COOKIE["id_env"] ));
			echo "
	<div class='alert_confirm'>
		<div class='alert_confirm_text'>
		<strong>Redirecting:</div><div style='padding:4px;'>
		Making new topic....
		</div>
	</div>";
			$tdb->edit("forums", $_GET["id"], array("topics" => ((int)$fRec[0]["topics"] + 1), "posts" => ((int)$fRec[0]["posts"] + 1)));
			$redirect = "viewforum.php?id=".$_GET["id"];
			$pre = "";
		} else {
			echo "
	<div class='alert_confirm'>
		<div class='alert_confirm_text'>
		<strong>Redirecting:</div><div style='padding:4px;'>
		Adding Reply....
		</div>
	</div>";
			$tdb->edit("forums", $_GET["id"], array("posts" => ((int)$fRec[0]["posts"] + 1)));
			$rec = $posts_tdb->get("topics", $_GET["t_id"]);
			if (isset($_POST["unstick"])) $rec[0]["sticky"] = "0";
			if ($rec[0]["monitor"] != "") {
				$local_dir = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
				$e_sbj = "New Reply in \"".$rec[0]["subject"]."\"";
				$e_msg = "You, or someone else using this e-mail address has requested to watch this topic: ".$rec[0]["subject"]." at ".$local_dir."/index.php\n\n".$_COOKIE["user_env"]." wrote:\n".$_POST["message"]."\n\n- - - - -\nSince this user has replied, you have been taken off the monitor list.  There may have been other users who have replied since then.  To read the rest of this topic, visit ".$local_dir."/viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."\nOr you can reply immediately if you forum cookies are valid by visiting ".$local_dir."/newpost.php?id=".$_GET["id"]."&t=0&t_id=".$_GET["t_id"]."&page=".$_GET["page"];
				$e_hed = "From: ".$_REGISTER["admin_email"]."\r\nReply-To: no-reply@".$_SERVER["server_name"]."\r\n";
				if (strpos($rec[0]["monitor"], ",") !== FALSE) $monitor = explode($rec[0]["monitor"], ",");
				else $monitor[0] = $rec[0]["monitor"];
				for($i = 0; $i < count($monitor); $i++) {
					@mail($monitor[$i], $e_sbj, $e_msg, $e_hed);
				}
			}
			$posts_tdb->edit("topics", $_GET["t_id"], array("replies" => ((int)$rec[0]["replies"] + 1), "last_post" => mkdate(), "user_name" => $_COOKIE["user_env"], "sticky" => $rec[0]["sticky"], "user_id" => $_COOKIE["id_env"], "monitor" => ""));
			if ($_GET["page"] == "") $_GET["page"] = 1;
			$redirect = "viewtopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"];
			$pre = $rec[0]["p_ids"].",";
		}
		clearstatcache();
		$posts_tdb->sort("topics", "last_post", "DESC");
		clearstatcache();
		$p_id = $posts_tdb->add("posts", array(
		"icon" => $_POST["icon"],
			"user_name" => $_COOKIE["user_env"],
			"date" => mkdate(),
			"message" => $uploadText.$_POST["message"],
			"user_id" => $_COOKIE["id_env"],
			"t_id" => $_GET["t_id"],
			"upload_id" => $uploadId ));
		$posts_tdb->edit("topics", $_GET["t_id"], array("p_ids" => $pre.$p_id));
		//$tdb->setFp('rss', 'rssfeed');
		//if($fRec[0]['view'] == 0) $tdb->add('rss', array('subject' => ((isset($_POST['subject'])) ? $_POST['subject'] : 'RE: ' . $rec[0]['subject']), 'user_name' => $_COOKIE['user_env'], 'date' => mkdate(), 'message' => $_POST['message'], 'f_id' => $_GET['id'], 't_id' => $_GET['t_id']));
		if ($_COOKIE["power_env"] != "0") {
			$user = $tdb->get("users", $_COOKIE["id_env"]);
			$tdb->edit("users", $_COOKIE["id_env"], array("posts" => ((int)$user[0]["posts"] + 1)));
		}
		redirect($redirect, 1);
	} else {
		$message = "";
		if (!isset($_GET["page"])) $_GET["page"] = 1;
		if ($_GET["t"] == 1) {
			$tpc = "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Subject:</strong></td>
				<td class='area_2'><input type=text name=subject size=40></td>
			</tr>";
			if ($_COOKIE["power_env"] == 3) $sticky = "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Sticky:</strong></td>
				<td class='area_2'><input type=checkbox name=sticky size=40 value=\"1\"></td>
			</tr>";
			$hed = "New Topic";
			$iframe = "";
		} else {
			if ($_GET['quote'] == 1) {
				$hed = "Reply Quote";
				$reply = $posts_tdb->get("posts", $_GET['p_id']);
				$message = "[quote]".$reply[0]["message"]."[/quote]";
			}
			else $hed = "Reply";
			$tpc = "";
			if ($_COOKIE["power_env"] == 3) $sticky = "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Un-Sticky:</strong></td>
				<td class='area_2'><input type=checkbox name=unstick size=40 value=\"1\"></td>
			</tr>";
			$iframe = "<br />
					<div class='main_cat_wrapper'>
						<div class='cat_area_1'>Topic overview:</div>
						<table class='main_table' cellspacing='1'>
							<tr>
								<td class='review_container'><div class='review_sub'>
									<iframe src='viewtopic_simple.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."' class='review_frame' scrolling='auto' frameborder='0'></iframe></div></td>
							</tr>
						</table>
						<div class='footer'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></div>
					</div><br />";
		}
		$icons = message_icons();

		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function SetSmiley(Which) {
			if (document.newentry.message.createTextRange) {
			document.newentry.message.focus();
			document.selection.createRange().duplicate().text = Which;
			} else {
			document.newentry.message.value += Which;
			}
			}
			//-->
			</SCRIPT>
			<script language='JavaScript'>
			function submitonce(theform){
			if (document.all||document.getElementById){
			for (i=0;i<theform.length;i++){
			var tempobj=theform.elements[i]
			if (tempobj.type.toLowerCase()=='submit'||tempobj.type.toLowerCase()=='reset')
			tempobj.disabled=true
			}
			}
			}
			</script>
			<form action='newpost.php?id=".$_GET["id"]."&t=".$_GET["t"]."&quote=".$_GET["quote"]."&t_id=".$_GET["t_id"]."&page=".$_GET["page"]."' method=POST name='newentry' onSubmit='submitonce(this)' enctype='multipart/form-data'>
			<input type='hidden' name='a' value='1'>";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
		echo "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>User Name:</strong></td>
				<td class='area_2'>".$_COOKIE["user_env"]."</td>
			</tr>
			$tpc
			$sticky
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Message Icon:</strong></td>
				<td class='area_2'><div style='width:610px;'><input type=radio name=icon value='icon1.gif' CHECKED><img src='./icon/icon1.gif'>$icons</div></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='".$_CONFIG["skin_dir"]."/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;' valign='top'><strong>Message:</strong>
					<div style='text-align:center;margin-top:20px;margin-bottom:20px;'>";
						toolMapImage();
		echo "</div>
					<div style='text-align:center;'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=350,height=450,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></div></td>
				<td class='area_2'><textarea name='message' id='look1'>".$message."</textarea>
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
			</tr>";
		if (!(($_CONFIG["fileupload_size"] == "0" || $_CONFIG["fileupload_size"] == "") && $_CONFIG["fileupload_location"] == "")) {
			echo "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Attach file:</strong></td>
				<td class='area_2'><input type=file name='file' value='file_name' size=20><br /<br />
					Valid file types: txt, gif, jpg, jpeg, zip.
					<br />Maximum file size is ".$_CONFIG["fileupload_size"]." Kb. If your file does not meet the requirements, the file will be rejected with no warning.</td>
			</tr>";
		}
		echo "
			<tr>
				<td class='footer_3a' style='text-align:center;' colspan='2'><input type=submit value='Submit' onclick='return check_submit()'></td>
			</tr>
		$skin_tablefooter
	</form>
	".$iframe."";
	}
	require_once('./includes/footer.php');
?>