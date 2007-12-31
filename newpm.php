<?php
	// Private Messaging System
	// Add on to Ultimate PHP Board V2.0
	// Original PM Version (before _MANUAL_ upgrades): 2.0
	// Addon Created by J. Moore aka Rebles
	// Using textdb Version: 4.4.2
	require_once("./includes/class/func.class.php");
	require_once("./includes/inc/post.inc.php");
	$where = "<a href='pmsystem.php'>Messenger</a> ".$_CONFIG["where_sep"]." Replying to message";
	if ($tdb->is_logged_in() === false) exitPage("You are not even Logged in.");
	$PrivMsg = new functions(DB_DIR."/", "privmsg.tdb");
	$PrivMsg->setFp("CuBox", ceil($_COOKIE["id_env"]/120));
	if ($_GET["action"] == "ClearOutBox") {
		require_once("./includes/header.php");
		$recs = $PrivMsg->query("CuBox", "box='outbox'&&from='".$_COOKIE["id_env"]."'", 1);
		$recs = array_reverse($recs);
		$c_outbox_recs = count($recs); //extra one for the pm just added to the outbox.
		if ($c_outbox_recs > $_CONFIG["pm_max_outbox_msg"] && $recs[0]["id"] != "") {
			for($i = ($_CONFIG["pm_max_outbox_msg"]); $i < ($c_outbox_recs); $i++) {
				$PrivMsg->delete("CuBox", $recs[$i]["id"], false);
			}
			$PrivMsg->reBuild("CuBox");
		}
		echo "
			<div class='alert_confirm'>
			<div class='alert_confirm_text'>
			<strong>Redirecting:</div><div style='padding:4px;'>
			Message Sucessfully Sent.
			</div>
			</div>
			!";
		require_once("./includes/footer.php");
		if ($_GET["ref"] != "" && $_GET["section"] != "" && $_GET["r"] != "") redirect($_POST["ref"]."?section=".$_GET["section"]."&id=".$_GET["r"], "2");
		else redirect("pmsystem.php", "2");
		exit;
	} elseif($_POST["s"] == 1) {
		$_POST['subject'] = htmlentities(stripslashes($_POST['subject']));
		$_POST['message'] = htmlentities(stripslashes($_POST['message']));
		$error_msg = "";
		if (!isset($_POST["icon"])) {
			$error_msg .= "
				<div class='alert'><div class='alert_text'>
				<strong>Caution!</strong></div><div style='padding:4px;'>Must be submitted through the form.</div></div>";
		}
		if (chop($_POST["message"]) == "") {
			$error_msg .= "
				<div class='alert'><div class='alert_text'>
				<strong>Caution!</strong></div><div style='padding:4px;'>You must provide a message.</div></div>";
		}
		if ($_POST["to"] == "" || $_POST["to"] == "0") {
			$error_msg .= "Select a Username<br />";
		} elseif($_POST["to"] == $_COOKIE["id_env"]) {
			$error_msg .= "
				<div class='alert'><div class='alert_text'>
				<strong>Caution!</strong></div><div style='padding:4px;'>You cannot send yourself a Private Message.</div></div>
				";
		} else {
			$ids = getUsersPMBlockedList($_COOKIE["user_env"]);
			if (true === array_search($_COOKIE["id_env"], $ids)) {
				$error_msg .= "
					<div class='alert'><div class='alert_text'>
					<strong>Denied!</strong></div><div style='padding:4px;'>The User you are sending does not wish to recieve messages from you. (You are blocked)</div></div>";
			}
		}
		if ($error_msg == "") {
			$to_info = $tdb->get("users", $_POST["to"]);
			$PrivMsg->setFp("ToBox", ceil($_POST["to"]/120));
			if ($_POST["icon"] == "") $_POST["icon"] = "icon1.gif";
			if (trim($_POST["subject"]) == "") $_POST["subject"] = "No Subject";
			if (isset($_POST["del"]) && isset($_POST["r"])) $PrivMsg->delete("CuBox", $_POST["r"]);
			$PrivMsg->add("ToBox", array("box" => "inbox", "from" => $_COOKIE["id_env"], "to" => $_POST["to"], "icon" => $_POST["icon"], "subject" => $_POST["subject"], "date" => mkdate(), "message" => chop($_POST["message"])));
			$PrivMsg->add("CuBox", array("box" => "outbox", "from" => $_COOKIE["id_env"], "to" => $_POST["to"], "icon" => $_POST["icon"], "subject" => $_POST["subject"], "date" => mkdate(), "message" => chop($_POST["message"])));
			$f = fopen(DB_DIR."/new_pm.dat", 'r+');
			fseek($f, (((int)$_POST["to"] * 2) - 2));
			$new_pm = trim(fread($f, 2));
			(int)$new_pm++;
			if (strlen($new_pm) == 3) $new_pm = 99;
			elseif(strlen($new_pm) == 1) $new_pm = " ".$new_pm;
			fseek($f, (((int)$_POST["to"] * 2) - 2));
			fwrite($f, $new_pm);
			fclose($f);
			redirect("newpm.php?action=ClearOutBox&ref=".$_POST["ref"]."&section=".$_POST["section"]."&r=".$_POST["r"], '2');
			exit;
		} else {
			if ($_POST["r"] != "") $_GET["r_id"] = $_POST["r"];
			$sbj = $subject;
			$msg = $message;
		}
	}
	require_once('./includes/header.php');
	if ($error_msg != "") echo $error_msg;
	if (isset($_GET["r_id"]) && is_numeric($_GET["r_id"])) {
		$reply = $PrivMsg->get("CuBox", $_GET["r_id"]);
		$u_reply = $tdb->get("users", $reply[0]["from"]);
		$send_to = $u_reply[0]['user_name']."<input type='hidden' name='to' value='".$reply[0]["from"]."'>";
		if (!isset($sbj)) {
			while (substr($reply[subject], 0, 4) == "RE: ") {
				$reply[0]["subject"] = substr($reply[0]["subject"], 5);
			}
			$sbj = "RE: ".$reply[0]["subject"];
		}
		$hed = "Replying to ".$u_reply[0]["user_name"]."'s message";
		$iframe = "
			<tr>
				<td class='review_container'><div class='review_sub'>
					<iframe src='viewpm_simple.php?id=".$_GET["r_id"]."' class='review_frame' scrolling='auto' frameborder='0'></iframe></div></td>
			</tr>";
	} else {
		if (!isset($_GET['to'])) exitPage('You must click on "send pm" from a user\'s profile or post.');
		if (!is_numeric($_GET['to'])) exitPage('Invalid User.');
		$send_to = $tdb->get('users', $_GET['to']);
		$send_to = $send_to[0]['user_name'].'<input type="hidden" name="to" value="'.$_GET['to'].'">';
		$hed = "New Topic";
		$iframe = "";
	}
	$icons = message_icons();
	echo "
		<script language='JavaScript'>
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
		</script>
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
		<script language=\"JavaScript\">
		<!--
		function openChild(file,window) {
		childWindow=open(file,window,'resizable=no,width=400,height=200');
		if (childWindow.opener == null) childWindow.opener = self;
		}
		//-->
		</script>";
	echo "
		<div id='tabstyle_2'>
		<ul>
		<li><a href='pmsystem.php?section=inbox'><span>View Inbox</span></a></li>
		<li><a href='pmsystem.php?section=outbox'><span>View Outbox</span></a></li>
		<li><a href='pmblocklist.php'><span>Manage Blocked Users</span></a></li>
		<li><a href='pmblocklist.php?action=adduser'><span>Block a User</span></a></li>
		</ul>
		</div>
		<div style='clear:both;'></div>";
	echo "<form action='".$_SERVER['PHP_SELF'].(isset($_GET['to']) ? "?to=".$_GET['to'] : '')."' method='POST' name='newentry' onSubmit='submitonce(this)' enctype='multipart/form-data'><input type='hidden' name='s' value='1'><input type='hidden' name='r' value='".$_GET["r_id"]."'>";
	echoTableHeading("Replying to ".$u_reply[0]["user_name"]."'s Message", $_CONFIG);
	echo "
			<tr>
				<th colspan='2'>$hed</th>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>User Name:</strong></td>
				<td class='area_2'>".$_COOKIE["user_env"]."</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Send to:</strong></td>
				<td class='area_2'>".$send_to."</td>
			</tr>
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Subject:</strong></td>
				<td class='area_2'><input type='text' name='subject' size='40' value='".$sbj."'></td>
			</tr>
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
					<div style='text-align:center;'><a href=\"javascript: window.open('more_smilies.php','Smilies','width=750,height=350,resizable=yes,scrollbars=yes'); void('');\">show more smilies</a></div></td>
				<td class='area_2'><textarea name='message' id='look1'>".$msg."</textarea>
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
				<td class='footer_3' colspan='6'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='6' style='text-align:center;'><input type='submit' value='Send PM' onclick='check_submit()'></td>
			</tr>
		$skin_tablefooter
	</form>";
	echoTableHeading("".$u_reply[0]["user_name"]."'s Message to you:", $_CONFIG);
	echo "
	$iframe
	$skin_tablefooter";
	require_once("./includes/footer.php");
?>