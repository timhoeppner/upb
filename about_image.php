<?php
    // Ultimate PHP Board
    // Author: Tim Hoeppner aka RR_Pilot, FixITguy
    // Website: http://www.myupb.com
    // Version: 2.0
    // Using textdb Version: 4.3.2
    $where = 'Private Policy';
    require_once('./includes/class/func.class.php');
    require_once("./includes/header_simple.php");
?>
You can add an image to your profile. If you have space on a webserver somewhere, upload your image, then type the URL of the image in the avatar text box to the right. 
The image will be automatically resized to 
<?php echo $_CONFIG['avatar_width']; ?> pixels by 
<?php echo $_CONFIG['avatar_height']; ?> pixels, so when you create your image, make sure it is already this size, otherwise 
your image may look distorted or warped. Please try to 
keep your file size below 20kb, so as not to slow down the board.
<br /><br /><br /><br /><a href=\"javascript: window.close();\">Close Window</a>
<?php require_once("./includes/footer_simple.php"); ?>