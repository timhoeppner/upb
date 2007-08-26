<?php 
/**
 * Registration sucurity image verification
 * Mod By: Fraser
 */

require_once('./inc/encode.inc.php');
$decid = urldecode(md5_decrypt($_GET['id'], $_GET['key'])); 

$img = imagecreatetruecolor(80, 20); 

for($i = 0; $i < 3; $i++) { 
    $lighthex1 = rand($i, 255); 
    $lighthex2 = rand($i, 255); 
    $lighthex3 = rand($i, 255); 
    
    $darkhex1 = rand($i, 100); 
    $darkhex2 = rand($i, 100); 
    $darkhex3 = rand($i, 100); 
    
    $lightcolor = imagecolorallocate($img, $lighthex1, $lighthex2, $lighthex3); 
    $darkcolor = imagecolorallocate($img, $darkhex1, $darkhex2, $darkhex3); 
    imagefill($img, 0, 0, $lightcolor); 
    //$font = 'LucidaConsole.ttf'; 
    imagestring($img, 5, 10, $i, $decid, $darkcolor); 
} 

// Call a valid image exporter
if(function_exists("imagegif")) {
    header("Content-type: image/gif");
    imagegif($img);
} elseif(function_exists("imagejpeg")) {
    header("Content-type: image/jpeg");
    imagejpeg($img, "", 0.5);
} elseif(function_exists("imagepng")) {
    header("Content-type: image/png");
    imagepng($img);
} else {
    die("No image support on this server");
}

imagedestroy($img);
?>