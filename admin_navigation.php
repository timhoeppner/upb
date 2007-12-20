<?php
//if(!defined(DB_DIR)) exit('This page must be run under a script wrapper'.DB_DIR);
echo "<center>";
if(isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"])) {
    if($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
echo "
<center><table width='100%' height='100%' cellspacing=1 cellpadding=3 border=0 bgcolor='$border'>
                <tr><td bgcolor='$table1' width=50% valign=top><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_cat.php' target = '_parent'>Manage Categories</a>
                <br><a href='admin_forum.php' target = '_parent'>Manage Forums</a>
                <br><a href='admin_config.php' target = '_parent'>Config Settings</a>
                <br><a href='admin_restore.php' target = '_parent'>Backup/Restore the database</a>
		            <br><a href='admin_checkupdate.php' target = '_parent'>Check for UPDATES</a>
              </td><td bgcolor='$table1' valign=top><font size='$font_m' face='$font_face' color='$font_color_main'><a href='admin_members.php' target = '_parent'>Manage Members</a>
                <br><a href='admin_iplog.php' target = '_parent'>Ip Address Log</a>
                <br><a href='admin_banuser.php' target = '_parent'>Manage Banned users</a>
                <br><a href='admin_badwords.php' target = '_parent'>Manage Bad Words</a>
                <br><a href='admin_smilies.php' target = '_parent'>Manage Smilies</a>
                </td></tr>
                </table>";

    }
}
echo "</center>";
?>
