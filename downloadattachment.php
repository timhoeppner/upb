<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Website: http://www.myupb.com
// Version: 2.1
// Using textdb Version: 4.4.3

include "./includes/class/tdb.class.php";
include "./includes/class/upload.class.php";
include "./config.php";

$upload = new upload(DB_DIR, 0);

$upload->getFile((int) $_GET["id"]);

// Update the download count
$dl = $upload->file["downloads"] + 1;
$upload->edit("uploads", (int) $_GET["id"], array("downloads" => $dl));

$upload->dumpFile();
?>