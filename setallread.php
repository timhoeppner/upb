<?php
require_once('./includes/class/func.class.php');
if(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"])) {
    if($tdb->is_logged_in()) {
        $v_date = mkdate();
        
        $v_file = fopen(DB_DIR."/lastvisit.dat", 'r+');
        fseek($v_file, (($_COOKIE["id_env"] - 1) * 14));
        $ses_info = trim(fread($v_file, 14));
        if($ses_info == '') { $ses_info = $v_date; die("ERROR!!!"); }
        
        fseek($v_file, -14, SEEK_CUR);
        fwrite($v_file, $v_date);
	    fclose($v_file);
	    
        if(!headers_sent()) {
            setcookie("thisvisit", $v_date);
            setcookie("lastvisit", $ses_info);
            setcookie("timezone", $_COOKIE["timezone"], (60 * 60 * 24 * 7));
            if(isset($_COOKIE["remember"])) {
                setcookie("remember", 1, (time() + (60*60*24*7)));
                setcookie("user_env", $_COOKIE["user_env"], (time() + (60*60*24*7)));
                setcookie("uniquekey_env", $_COOKIE["uniquekey_env"], (time() + (60*60*24*7)));
                setcookie("power_env", $_COOKIE["power_env"], (time() + (60*60*24*7)));
                setcookie("id_env", $_COOKIE["id_env"], (time() + (60*60*24*7)));
            }
        }
    }
}

if(!isset($_GET["reload"]) && $_GET["reload"] == "") {
    echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0;URL=setallread.php?reload=i\">";
} elseif($_GET["reload"] == "i" ) {
    echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0;URL=setallread.php?reload=ii\">";
} else {
    echo "<html><body onLoad=\"javascript:history.go(-1);\"> </body></html>";
} //End
?>