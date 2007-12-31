<?php
	//if(!defined(DB_DIR)) exit('This page must be run under a script wrapper'.DB_DIR);
	if (isset($_COOKIE["user_env"]) && isset($_COOKIE["uniquekey_env"]) && isset($_COOKIE["power_env"]) && isset($_COOKIE["id_env"])) {
		if ($tdb->is_logged_in() && $_COOKIE["power_env"] == 3) {
			echo "
				<div style='width:50%;float:left;line-height:20px;text-align:center;'><span class='link_1'>
				<a href='admin_cat.php' target = '_parent'>Manage Categories</a><br />
				<a href='admin_forum.php' target = '_parent'>Manage Forums</a><br />
				<a href='admin_config.php' target = '_parent'>Config Settings</a><br />
				<a href='admin_restore.php' target = '_parent'>Backup/Restore the database</a><br />
				<a href='admin_checkupdate.php' target = '_parent'>Check for UPDATES</a></span></div>
				<div style='width:50%;float:right;line-height:20px;text-align:center;'><span class='link_1'>
				<a href='admin_members.php' target = '_parent'>Manage Members</a><br />
				<a href='admin_iplog.php' target = '_parent'>Ip Address Log</a><br />
				<a href='admin_banuser.php' target = '_parent'>Manage Banned users</a><br />
				<a href='admin_badwords.php' target = '_parent'>Manage Bad Words</a><br />
				<a href='admin_cleanup.php' target = '_parent'>Clean up (old search files)</a></span></div>";
		}
	}
?>