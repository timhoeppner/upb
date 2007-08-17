<?php
require_once('./includes/class/func.class.php');
require_once('./includes/header_simple.php');
if(!(isset($_COOKIE["power_env"]) && isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["id_env"]))) exitPage('you are not logged in<meta http-equiv="refresh" content="2;URL=login.php?ref=admin.php">', true);
if(!($tdb->is_logged_in() && $_COOKIE['power_env'] == 3)) exitPage('You are nt authorized to be here', true);

 
$data_file = DB_DIR.'/smilies.dat';
$write_avatar = fopen($data_file, 'w');
$stuff = "<?php
\$files_name = array(";

fwrite($write_avatar, $stuff);

if ($handle = opendir('./smilies/moresmilies/')) {
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != "..") {  
            //$msg = "\"$i\" => \"$file\",";
            $msg = "\"$file\",";
            fwrite($write_avatar, $msg);
            $i++;  
        } 
    }
    closedir($handle); 
}
fwrite($write_avatar, "\"zzz.gif\");\r\n"); // this line is there because I didn't realy know
                                            // how to not put "," after the last file
fwrite($write_avatar, "?>");
fclose($write_avatar);

@chmod($data_file, 0777);

echo "the list has been succesfully created";
require_once('./includes/footer_simple.php');
?>