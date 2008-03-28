<?php
	require_once("./includes/upb.initialize.php");
	require_once("./includes/class/upload.class.php");
    $upload = new upload(DB_DIR, 0,$_CONFIG['fileupload_location']);
    $upload->getFile((int) $_GET["id"]);
	// Update the download count
	$upload->edit("uploads", (int) $_GET["id"], array("downloads" => ((int)$upload->file["downloads"] + 1)));
	$upload->dumpFile();
?>
