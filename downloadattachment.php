<?php
require_once("./includes/upb.initialize.php");
require_once("./includes/class/upload.class.php");
if (!($tdb->is_logged_in())) {
	die("You do not have permission to download this file");
}
else
{
	if (FALSE === ($fRec = $tdb->get("forums", $_GET["id"]))) exitPage("Download topic does not exist.", true);
	if ((int)$_COOKIE["power_env"] < $fRec[0]["view"]) exitPage("You do not have permission to download this file");
  $upload = new upload(DB_DIR, 0,$_CONFIG['fileupload_location']);
	$upload->getFile((int) $_GET["upload_id"]);
	// Update the download count
	$upload->edit("uploads", (int) $_GET["upload_id"], array("downloads" => ((int)$upload->file["downloads"] + 1)));
	$upload->dumpFile();
}
?>