<?php
	// install.php
	// designed for Ultimate PHP Board
	// Author: Jerroyd Moore, aka Rebles
	// Website: http://www.myupb.com
	// Version: 2.2.1
	// Using textdb Version: 4.4.1
	ignore_user_abort();
	if (TRUE !== is_writable('config.php')) die('Unable to continue with the installation process.  "config.php" in the root upb directory MUST exist and MUST BE writable.');
	if (filesize('config.php') > 0) {
		require_once('config.php');
		if (INSTALLATION_MODE === FALSE) {
			$msg = '';
			if (isset($_COOKIE['user_env'])) $msg .= $_COOKIE['user_env'];
			else $msg .= 'Someone';
			if (isset($_COOKIE['id_env'])) $msg .= '(User ID:'.$_COOKIE['id_env'].')';
			if (isset($_SERVER['REMOTE_ADDR'])) $msg .= 'with the IP Address "'.$_SERVER['REMOTE_ADDR'].'"';
			$msg .= 'tried to initiate an installation or upgrade file.  Since the installation and upgrade files pose a risk, this could be an attempted hack.  The administrator, you, were notified.  It is advised you delete installation and upgrade files, which are no long use, and ban the IP Address and username, if given.';
			@mail(ADMIN_EMAIL, 'SECURITY ALERT on your forum', $msg);
			die('<strong>Security Risk</strong>:  Unable to initiate installation. An Administrater must put the forum in Installation Mode.  You\'re IP Address has been sent to the administrater aswell as login information.');
		}
	}
	if(!isset($_POST['add'])) $_POST['add'] = '';
	if ($_POST["add"] == "") {

		//define and create the new database folder
		if (!defined('DB_DIR')) {
			define('DB_DIR', './'.uniqid('data_', true), true);
			$f = fopen('config.php', 'w');
			fwrite($f, "<?php\ndefine('INSTALLATION_MODE', true, true);\ndefine('UPB_VERSION', '2.1.1b', true);\ndefine('DB_DIR', '".DB_DIR."', true);\n?>");
      ?><?php
      //allows syntax highlighting to be work again for coding purposes
			fclose($f);
		}
		if (!is_dir(DB_DIR)) {
			if (!mkdir(DB_DIR, 0777)) die('The forum must be able to create a folder in the root forum folder.  Please chmod() the root folder to 777 and rerun the script');
			@mkdir(DB_DIR.'/backup', 0777);
		}
		//end
		//create *.dat files and folders
		$f = fopen(DB_DIR.'/banneduser.dat', 'w');
		fwrite($f, '');
		fclose($f);
		$f = fopen(DB_DIR.'/hits.dat', 'w');
		fwrite($f, '0');
		fclose($f);
		$f = fopen(DB_DIR.'/hits_record.dat', 'w');
		fwrite($f, 'Apr 6, 2005:1');
		fclose($f);
		$f = fopen(DB_DIR.'/hits_today.dat', 'w');
		fwrite($f, '1112852091:1');
		fclose($f);
		$f = fopen(DB_DIR.'/iplog', 'w');
		fwrite($f, '');
		fclose($f);
		$f = fopen(DB_DIR.'/.htaccess', 'w');
		fwrite($f, 'Order deny,allow
			Deny from all');
		fclose($f);
		$f = fopen(DB_DIR.'/config_org.dat', 'w');
        fwrite($f,
        "config".chr(30)."General".chr(31).
        "status".chr(30)."Members' Statuses".chr(31).
        "regist".chr(30)."New Users".chr(31).
        //.type.chr(30).type name.chr(31)
        chr(29).
        "config".chr(30)."1".chr(30)."Main Forum Config".chr(31).
        "config".chr(30)."9".chr(30)."Posting Settings".chr(31).
        "status".chr(30)."2".chr(30)."Member status".chr(31).
        "status".chr(30)."3".chr(30)."Moderator status".chr(31).
        "status".chr(30)."4".chr(30)."Admin Status".chr(31).
        "status".chr(30)."5".chr(30)."Member status Colors".chr(31).
        "status".chr(30)."6".chr(30)."Who's Online User Colors".chr(31).
        "regist".chr(30)."7".chr(30)."New Users' Confirmation E-mail".chr(31).
        "regist".chr(30)."8".chr(30)."Users' Avatars".chr(31));
        //.type.chr(30).minicat.chr(30).cat name.chr(31)
		fclose($f);
      ?><?php
    //allows syntax highlighting to be work again for coding purposes
		$f = fopen(DB_DIR.'/whos_online.dat', 'w');
		fwrite($f, '');
		fclose($f);
		$f = fopen(DB_DIR.'/constants.php', 'w');
		fwrite($f, 'define("TABLE_WIDTH_MAIN", $_CONFIG["table_width_main"], true);'."\n".'define("SKIN_DIR", $_CONFIG["skin_dir"], true);');
		fclose($f);
		//end
		//begin db files
		require_once('./includes/class/tdb.class.php');
		$tdb = new tdb('', '');
		$tdb->createDatabase(DB_DIR."/", "main.tdb");
		$tdb->createDatabase(DB_DIR."/", "posts.tdb");
		$tdb->createDatabase(DB_DIR."/", "privmsg.tdb");
		$tdb->createDatabase(DB_DIR."/", "bbcode.tdb");
		$tdb->tdb(DB_DIR."/", "main.tdb");
		$tdb->createTable("members", array(
		array("user_name", "string", 20),
			array("password", "string", 49),
			array("uniquekey", "string", 32),
			array("level", "number", 1),
			array("email", "memo"),
			array("view_email", "number", 1),
			array("mail_list", "number", 1),
			array("status", "memo"),
			array("location", "memo"),
			array("url", "memo"),
			array("avatar", "memo"),
			array("avatar_height", "number", 3),
			array("avatar_width", "number", 3),
			array("avatar_hash", "string", 32),
			array("icq", "number", 20),
			array("aim", "string", 24),
			array("yahoo", "memo"),
			array("msn", "memo"),
			array("sig", "memo"),
			array("posts", "number", 7),
			array("date_added", "number", 14),
			array("timezone", "string", 3),
			array('newTopicsData', 'memo'),
			array("id", "id"),
			array("lastvisit","number",14),
		), 20);
		$tdb->createTable("forums", array(
		array("forum", "memo"),
			array("cat", "number", 7),
			array("view", "number", 1),
			array("post", "number", 1),
			array("reply", "number", 1),
			array("des", "memo"),
			array("topics", "number", 7),
			array("posts", "number", 7),
			array("id", "id")
		), 20);
		$tdb->createTable("categories", array(
		array("name", "memo"),
			array("sort", "memo"),
			array("view", "number", 1),
			array("id", "id")
		), 20);
		$tdb->createTable("getpass", array(
		array("passcode_HASH", "string", 49),
			array("time", "number", 14),
			array("user_id", "number", 7),
			array("id", "id")
		), 30);
		$tdb->createTable("config", array(
		array("name", "memo"),
			array("value", "memo"),
			array("type", "string", 6),
			array("id", "id"),
			), 20);
		$tdb->createTable("ext_config", array(
		array("name", "memo"),
			array("value", "memo"),
			array("type", "string", 6),
			array("title", "memo"),
			array("description", "memo"),
			array("form_object", "string", 8),
			array("data_type", "string", 7),
			array("minicat", "number", 2),
			array("sort", "number", 2),
			array("id", "id")
		), 20);
		$tdb->setFp("config", "config");
		$tdb->setFp("ext_config", "ext_config");
		$tdb->tdb(DB_DIR."/", "privmsg.tdb");
		$tdb->createTable("1", array(
		array("box", "string", 6),
			array("from", "number", 7),
			array("to", "number", 7),
			array("icon", "string", 10),
			array("subject", "memo"),
			array("date", "number", 14),
			array("message", "memo"),
			array("id", "id")
		));
		$f = fopen(DB_DIR."/blockedlist.dat", "w");
		fwrite($f, "");
		fclose($f);
		//$_CONFIG
		?><?php
    $tdb->add("ext_config", array("name" => "ver", "value" => "2.2.1", "type" => "config", "form_object" => "hidden", "data_type" => "string"));
		$tdb->add("config", array("name" => "ver", "value" => "2.2.1", "type" => "config"));

		$tdb->add("ext_config", array("name" => "title", "value" => "Discussion Forums", "type" => "config", "title" => "Title", "description" => "Title of the forum", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "1"));
		$tdb->add("config", array("name" => "title", "value" => "Discussion Forums", "type" => "config"));

    $tdb->add("ext_config", array("name" => "table_width_main", "value" => "98%", "type" => "config", "title" => "Table Width", "description" => "This will change the table width of the main section of the forums", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "10"));
		$tdb->add("config", array("name" => "table_width_main", "value" => "98%", "type" => "config"));

    $tdb->add("ext_config", array("name" => "posts_per_page", "value" => "20", "type" => "config", "title" => "Posts Per Page", "description" => "this is how many posts will be displays on each page for topics", "form_object" => "text", "data_type" => "number", "minicat" => "9", "sort" => "1"));
		$tdb->add("config", array("name" => "posts_per_page", "value" => "20", "type" => "config"));

    $tdb->add("ext_config", array("name" => "topics_per_page", "value" => "40", "type" => "config", "title" => "Topics per Page", "description" => "this is how many topics will be displays on each page for forums", "form_object" => "text", "data_type" => "number", "minicat" => "9", "sort" => "2"));
		$tdb->add("config", array("name" => "topics_per_page", "value" => "40", "type" => "config"));

    $tdb->add("ext_config", array("name" => "logo", "value" => "images/logo.gif", "type" => "config", "title" => "Logo Location", "description" => "can be relative or a url", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "2"));
		$tdb->add("config", array("name" => "logo", "value" => "images/logo.gif", "type" => "config"));

    $tdb->add("ext_config", array("name" => "homepage", "type" => "config", "title" => "Homepage URL", "description" => "can be relative or a url", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "3"));
		$tdb->add("config", array("name" => "homepage", "type" => "config"));

    $tdb->add("ext_config", array("name" => "admin_catagory_sorting", "type" => "config", "form_object" => "hidden", "data_type" => "string"));
		$tdb->add("config", array("name" => "admin_catagory_sorting", "type" => "config"));

    $tdb->add("ext_config", array("name" => "servicemessage", "type" => "config", "title" => "Service Messages", "description" => "Service Messages appear above the forum, if nothing input, Announcements will not be displayed. Html is allowed.", "form_object" => "textarea", "data_type" => "string", "minicat" => "1", "sort" => "18"));
		$tdb->add("config", array("name" => "servicemessage", "type" => "config"));

    $tdb->add("ext_config", array("name" => "skin_dir", "value" => "./skins/default", "type" => "config", "title" => "Skin Directory", "description" => "leave it unless you upload another skin", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "11"));
		$tdb->add("config", array("name" => "skin_dir", "value" => "./skins/default", "type" => "config"));

    $tdb->add("ext_config", array("name" => "fileupload_location", "value" => "./uploads", "type" => "config", "title" => "Location for file attachments", "description" => "Put the path to the directory for file attachments.<br />e.g. If your forums are located at http://forum.myupb.com, and your uploads directory is at http://forum.myupb.com/uploads, you would simply put 'uploads' (without quotes) in the box.", "form_object" => "text", "data_type" => "number", "minicat" => "9", "sort" => "3"));
		$tdb->add("config", array("name" => "fileupload_location", "value" => "./uploads", "type" => "config"));
		$tdb->add("ext_config", array("name" => "fileupload_size", "value" => "50", "type" => "config", "title" => "Size limits for file upload", "description" => "In kilobytes, type in the maximum size allowed for file uploads", "form_object" => "text", "data_type" => "number", "minicat" => "9", "sort" => "4"));
		$tdb->add("config", array("name" => "fileupload_size", "value" => "50", "type" => "config"));
		$tdb->add("ext_config", array("name" => "censor", "value" => "*censor*", "type" => "config", "title" => "Word to replace bad words", "description" => "Words that will replace bad words in a post", "form_object" => "text", "data_type" => "string", "minicat" => "9", "sort" => "5"));
		$tdb->add("config", array("name" => "censor", "value" => "*censor*", "type" => "config"));
		$tdb->add("ext_config", array("name" => "sticky_note", "value" => "[Stick Note]", "type" => "config", "title" => "Sticky Note Text", "description" => "Text that appends the title indicating it is a \"Stickied Topic\" (HTML Tags Allowed)", "form_object" => "text", "data_type" => "string", "minicat" => "9", "sort" => "6"));
		$tdb->add("config", array("name" => "sticky_note", "value" => "[Stick Note]", "type" => "config"));
		$tdb->add("ext_config", array("name" => "sticky_after", "value" => "0", "type" => "config", "title" => "Sticky Note Before or After Title", "description" => "If this is checked, the \"sticky note\" text will appear after the title.  Unchecking this will display it before the title.", "form_object" => "checkbox", "minicat" => "9", "sort" => "7"));
		$tdb->add("config", array("name" => "sticky_after", "value" => "0", "type" => "config"));
		$tdb->add("ext_config", array("name" => "pm_max_outbox_msg", "value" => "50", "type" => "config", "title" => "Max Number of Private Msgs in a Users OutBox", "description" => "Can be set to 0 to infinity", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "19"));
		$tdb->add("config", array("name" => "pm_max_outbox_msg", "value" => "50", "type" => "config"));
		$tdb->add("ext_config", array("name" => "avatar_width", "value" => "60", "type" => "config", "title" => "Avatars' Width", "description" => "The width (with respect to the height) of user avatars you want to be displayed at in pixels (Cannot be higher than 999)", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "8"));
		$tdb->add("config", array("name" => "avatar_width", "value" => "60", "type" => "config"));
		$tdb->add("ext_config", array("name" => "avatar_height", "value" => "60", "type" => "config", "title" => "Avatars' Height", "description" => "The height (with respect to the width) of user avatars you want to be displayed at in pixels (Cannot be higher than 999)", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "9"));
		$tdb->add("config", array("name" => "avatar_height", "value" => "60", "type" => "config"));

        $tdb->add("ext_config", array("name" => "security_code", "value" => "1", "type" => "regist", "title" => "Enable Security Code", "description" => "Enable the security code image for new user registration<br><strong>Enabling this is recommended</strong>", "form_object" => "checkbox", "minicat" => "1", "sort" => "16"));
		$tdb->add("config", array("name" => "security_code", "value" => "1", "type" => "regist"));

		$tdb->add("ext_config", array("name" => "banned_words", "value" => "shit,fuck,cunt,pussy,bitch,arse", "type" => "config", "form_object" => "hidden", "data_type" => "string"));
        $tdb->add("config", array("name" => "banned_words", "value" => "shit,fuck,cunt,pussy,bitch,arse", "type" => "config"));

		//$_REGISTER
		$tdb->add("ext_config", array("name" => "register_sbj", "value" => '', "type" => "regist", "title" => "Register Email Subject", "description" => "this is the subject for confirmation of registration", "form_object" => "text", "data_type" => "string", "minicat" => "7", "sort" => "2"));
		$tdb->add("config", array("name" => "register_sbj", "value" => '', "type" => "regist"));
		$tdb->add("ext_config", array("name" => "register_msg", "value" => '', "type" => "regist", "title" => "Register Email Message", "description" => "this is the message for confirmation of registration (options: &lt;login&gt; &lt;password&gt;)", "form_object" => "textarea", "data_type" => "string", "minicat" => "7", "sort" => "3"));
		$tdb->add("config", array("name" => "register_msg", "value" => '', "type" => "regist"));
		$tdb->add("ext_config", array("name" => "admin_email", "value" => '', "type" => "regist", "title" => "Admin E-mail", "description" => "this is the return address for confirmation of registration", "form_object" => "text", "data_type" => "string", "minicat" => "7", "sort" => "1"));
		$tdb->add("config", array("name" => "admin_email", "value" => '', "type" => "regist"));

		$tdb->add("ext_config", array("name" => "newuseravatars", "value" => "50", "type" => "regist", "title" => "Custom Avatars", "description" => "Allow users to choose their own avatars instead of the list in images/avatars directory after they have at least the number of posts indicated in this field", "form_object" => "text", "minicat" => "8", "sort" => "1"));
		$tdb->add("config", array("name" => "newuseravatars", "value" => "50", "type" => "regist"));

    //$_STATUS
		$tdb->add("ext_config", array("name" => "member_status1", "value" => "n00b", "type" => "status", "title" => "Member post status 1", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "2", "sort" => "1"));
		$tdb->add("config", array("name" => "member_status1", "value" => "n00b", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_status2", "value" => "Toilet Cleaner", "type" => "status", "title" => "Member post status 2", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "2", "sort" => "3"));
		$tdb->add("config", array("name" => "member_status2", "value" => "Toilet Cleaner", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_status3", "value" => "Toilet Traveler", "type" => "status", "title" => "Member post status 3", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "2", "sort" => "5"));
		$tdb->add("config", array("name" => "member_status3", "value" => "Toilet Traveler", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_status4", "value" => "Spammer", "type" => "status", "title" => "Member post status 4", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "2", "sort" => "7"));
		$tdb->add("config", array("name" => "member_status4", "value" => "Spammer", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_status5", "value" => "Can o Spam", "type" => "status", "title" => "Member post status 5", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "2", "sort" => "9"));
		$tdb->add("config", array("name" => "member_status5", "value" => "Can o Spam", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_status1", "value" => "Moderator<br />&nbsp;Trainee", "type" => "status", "title" => "Moderator post status 1", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "1"));
		$tdb->add("config", array("name" => "mod_status1", "value" => "Moderator<br />&nbsp;Trainee", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_status2", "value" => "Moderator<br />&nbsp;Clerk", "type" => "status", "title" => "Moderator post status 2", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "3"));
		$tdb->add("config", array("name" => "mod_status2", "value" => "Moderator<br />&nbsp;Clerk", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_status3", "value" => "Moderator<br />&nbsp;Shift Manager", "type" => "status", "title" => "Moderator post status 3", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "5"));
		$tdb->add("config", array("name" => "mod_status3", "value" => "Moderator<br />&nbsp;Shift Manager", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_status4", "value" => "Moderator<br />&nbsp;Manager", "type" => "status", "title" => "Moderator post status 4", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "7"));
		$tdb->add("config", array("name" => "mod_status4", "value" => "Moderator<br />&nbsp;Manager", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_status5", "value" => "Moderator<br />&nbsp;Super-Moderator 1000", "type" => "status", "title" => "Moderator post status 5", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "9"));
		$tdb->add("config", array("name" => "mod_status5", "value" => "Moderator<br />&nbsp;Super-Moderator 1000", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_status1", "value" => "Administrator<br />&nbsp;Script Junkie", "type" => "status", "title" => "Admin post status 1", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "1"));
		$tdb->add("config", array("name" => "admin_status1", "value" => "Administrator<br />&nbsp;Script Junkie", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_status2", "value" => "Administrator<br />&nbsp;Programming<br />&nbsp;King", "type" => "status", "title" => "Admin post status 2", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "3"));
		$tdb->add("config", array("name" => "admin_status1", "value" => "Administrator<br />&nbsp;Programming<br />&nbsp;King", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_status3", "value" => "Administrator<br />&nbsp;Programmer<br />&nbsp;Extraordinaire", "type" => "status", "title" => "Admin post status 3", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "5"));
		$tdb->add("config", array("name" => "admin_status3", "value" => "Administrator<br />&nbsp;Programmer<br />&nbsp;Extraordinaire", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_status4", "value" => "Administrator<br />&nbsp;CompuGlobal<br />&nbsp;HyperMegaGuy", "type" => "status", "title" => "Admin post status 4", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "7"));
		$tdb->add("config", array("name" => "admin_status4", "value" => "Administrator<br />&nbsp;CompuGlobal<br />&nbsp;HyperMegaGuy", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_status5", "value" => "Administrator<br />&nbsp;Programming<br />&nbsp;Guru", "type" => "status", "title" => "Admin post status 5", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "9"));
		$tdb->add("config", array("name" => "admin_status5", "value" => "Administrator<br />&nbsp;Programming<br />&nbsp;Guru", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_post1", "value" => "50", "type" => "status", "title" => "Post count 1", "form_object" => "text", "data_type" => "number", "minicat" => "2", "sort" => "2"));
		$tdb->add("config", array("name" => "member_post1", "value" => "50", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_post2", "value" => "100", "type" => "status", "title" => "Post count 2", "form_object" => "text", "data_type" => "number", "minicat" => "2", "sort" => "4"));
		$tdb->add("config", array("name" => "member_post2", "value" => "100", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_post3", "value" => "250", "type" => "status", "title" => "Post count 3", "form_object" => "text", "data_type" => "number", "minicat" => "2", "sort" => "6"));
		$tdb->add("config", array("name" => "member_post3", "value" => "250", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_post4", "value" => "500", "type" => "status", "title" => "Post count 4", "form_object" => "text", "data_type" => "number", "minicat" => "2", "sort" => "8"));
		$tdb->add("config", array("name" => "member_post4", "value" => "500", "type" => "status"));
		$tdb->add("ext_config", array("name" => "member_post5", "value" => "1000", "type" => "status", "title" => "Post count 5", "form_object" => "text", "data_type" => "number", "minicat" => "2", "sort" => "10"));
		$tdb->add("config", array("name" => "member_post5", "value" => "1000", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_post1", "value" => "0", "type" => "status", "title" => "Post count 1", "form_object" => "text", "data_type" => "number", "minicat" => "3", "sort" => "2"));
		$tdb->add("config", array("name" => "mod_post1", "value" => "0", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_post2", "value" => "100", "type" => "status", "title" => "Post count 2", "form_object" => "text", "data_type" => "number", "minicat" => "3", "sort" => "4"));
		$tdb->add("config", array("name" => "mod_post2", "value" => "100", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_post3", "value" => "250", "type" => "status", "title" => "Post count 3", "form_object" => "text", "data_type" => "number", "minicat" => "3", "sort" => "6"));
		$tdb->add("config", array("name" => "mod_post3", "value" => "250", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_post4", "value" => "500", "type" => "status", "title" => "Post count 4", "form_object" => "text", "data_type" => "number", "minicat" => "3", "sort" => "8"));
		$tdb->add("config", array("name" => "mod_post4", "value" => "500", "type" => "status"));
		$tdb->add("ext_config", array("name" => "mod_post5", "value" => "1000", "type" => "status", "title" => "Post count 5", "form_object" => "text", "data_type" => "number", "minicat" => "3", "sort" => "10"));
		$tdb->add("config", array("name" => "mod_post5", "value" => "1000", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_post1", "value" => "0", "type" => "status", "title" => "Post count 1", "form_object" => "text", "data_type" => "number", "minicat" => "4", "sort" => "2"));
		$tdb->add("config", array("name" => "admin_post1", "value" => "0", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_post2", "value" => "100", "type" => "status", "title" => "Post count 2", "form_object" => "text", "data_type" => "number", "minicat" => "4", "sort" => "4"));
		$tdb->add("config", array("name" => "admin_post2", "value" => "100", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_post3", "value" => "250", "type" => "status", "title" => "Post count 3", "form_object" => "text", "data_type" => "number", "minicat" => "4", "sort" => "6"));
		$tdb->add("config", array("name" => "admin_post3", "value" => "250", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_post4", "value" => "500", "type" => "status", "title" => "Post count 4", "form_object" => "text", "data_type" => "number", "minicat" => "4", "sort" => "8"));
		$tdb->add("config", array("name" => "admin_post4", "value" => "500", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admin_post5", "value" => "1000", "type" => "status", "title" => "Post count 5", "form_object" => "text", "data_type" => "number", "minicat" => "4", "sort" => "10"));
		$tdb->add("config", array("name" => "admin_post5", "value" => "1000", "type" => "status"));
		$tdb->add("ext_config", array("name" => "membercolor", "value" => "#000000", "type" => "status", "title" => "Member status color", "description" => "The color that the status of a regular user will have", "form_object" => "text", "data_type" => "string", "minicat" => "5", "sort" => "1"));
		$tdb->add("config", array("name" => "membercolor", "value" => "#000000", "type" => "status"));
		$tdb->add("ext_config", array("name" => "moderatorcolor", "value" => "#990099", "type" => "status", "title" => "Moderator status color", "description" => "The color that the status of a moderator will have", "form_object" => "text", "data_type" => "string", "minicat" => "5", "sort" => "2"));
		$tdb->add("config", array("name" => "moderatorcolor", "value" => "#990099", "type" => "status"));
		$tdb->add("ext_config", array("name" => "admcolor", "value" => "#BB0000", "type" => "status", "title" => "Admin status color", "description" => "The color that the status of an administrator will have", "form_object" => "text", "data_type" => "string", "minicat" => "5", "sort" => "3"));
		$tdb->add("config", array("name" => "admcolor", "value" => "#BB0000", "type" => "status"));
		//Who's online hex
		$tdb->add("ext_config", array("name" => "userColor", "value" => "9d865e", "type" => "status", "title" => "User Color", "description" => "The color of usernames of regular users in the who's online box", "form_object" => "text", "data_type" => "string", "minicat" => "6", "sort" => "1"));
		$tdb->add("config", array("name" => "userColor", "value" => "9d865e", "type" => "status"));
		$tdb->add("ext_config", array("name" => "modColor", "value" => "006699", "type" => "status", "title" => "Moderator Color", "description" => "The color of usernames of moderators in the who's online box", "form_object" => "text", "data_type" => "string", "minicat" => "6", "sort" => "2"));
		$tdb->add("config", array("name" => "modColor", "value" => "006699", "type" => "status"));
		$tdb->add("ext_config", array("name" => "adminColor", "value" => "BB0000", "type" => "status", "title" => "Admin Color", "description" => "The color of usernames of administrators in the who's online box", "form_object" => "text", "data_type" => "string", "minicat" => "6", "sort" => "3"));
		$tdb->add("config", array("name" => "adminColor", "value" => "BB0000", "type" => "status"));
		//$tdb->sortAndBuild("ext_config", "sort", "ASC");


    $tdb->tdb(DB_DIR.'/', 'bbcode.tdb');
    $tdb->createTable('smilies',array(array('id','id'),array('bbcode','memo'),array('replace','memo'),array('type','string',4)));
    $tdb->createTable('icons',array(array('id','id'),array('filename','memo')));
    $tdb->setFp("smilies","smilies");
    $tdb->setFp("icons","icons");
    for ($i = 1;$i<22;$i++)
    {
      $filename = 'icon'.$i.'.gif';
      $tdb->add('icons',array("filename"=>$filename));
    }

      //SMILIES
	     $tdb->add('smilies',array("bbcode"=>" :)","replace"=> " <img src='./smilies/smile.gif' border='0' alt=':)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" :(", "replace"=>" <img src='./smilies/frown.gif' border='0' alt=':('> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" ;)","replace"=> " <img src='./smilies/wink.gif' border='0' alt=';)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" :P","replace"=> " <img src='./smilies/tongue.gif' border='0' alt=':P'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" :o","replace"=> " <img src='./smilies/eek.gif' border='0' alt=':o'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" :D","replace"=> " <img src='./smilies/biggrin.gif' border='0' alt=':D'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (C)","replace"=> " <img src='./smilies/cool.gif' border='0' alt='(C)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (M)","replace"=> " <img src='./smilies/mad.gif' border='0' alt='(M)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (confused)","replace"=> " <img src='./smilies/confused.gif' border='0' alt='(confused)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (crazy)","replace"=> " <img src='./smilies/crazy.gif' border='0' alt='(crazy)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (hm)","replace"=> " <img src='./smilies/hm.gif' border='0' alt='(hm)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (hmmlaugh)","replace"=> " <img src='./smilies/hmmlaugh.gif' border='0' alt='(hmmlaugh)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (offtopic)","replace"=> " <img src='./smilies/offtopic.gif' border='0' alt='(offtopic)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (blink)","replace"=> " <img src='./smilies/blink.gif' border='0' alt='(blink)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (rofl)","replace"=> " <img src='./smilies/rofl.gif' border='0' alt='(rofl)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (R)","replace"=> " <img src='./smilies/redface.gif' border='0' alt='(R)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (E)","replace"=> " <img src='./smilies/rolleyes.gif' border='0' alt='(E)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (wallbash)","replace"=> " <img src='./smilies/wallbash.gif' border='0' alt='(wallbash)'> ","type" => "main"));
	     $tdb->add('smilies',array("bbcode"=>" (noteeth)","replace"=> " <img src='./smilies/noteeth.gif' border='0' alt='(noteeth)'> ","type" => "main"));

	     //MORE SMILIES (more smilies page)
	     $tdb->add('smilies',array("bbcode"=>" LOL","replace"=> " <img src='./smilies/lol.gif' border='0' alt='LOL'> ","type" => "main"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-035.gif[/img]","replace"=>"<img src='./smilies/action-smiley-035.gif' border='0' alt='action-smiley-035.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-073.gif[/img]","replace"=>"<img src='./smilies/action-smiley-073.gif' border='0' alt='action-smiley-073.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/anti-old.gif[/img]","replace"=>"<img src='./smilies/anti-old.gif' border='0' alt='anti-old.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/blamesheep.gif[/img]","replace"=>"<img src='./smilies/blamesheep.gif' border='0' alt='blamesheep.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/bump.gif[/img]","replace"=>"<img src='./smilies/bump.gif' border='0' alt='bump.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/chainsaw.gif[/img]","replace"=>"<img src='./smilies/chainsaw.gif' border='0' alt='chainsaw.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/closed.gif[/img]","replace"=>"<img src='./smilies/closed.gif' border='0' alt='closed.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/enforcer.gif[/img]","replace"=>"<img src='./smilies/enforcer.gif' border='0' alt='enforcer.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/ernaehrung004.gif[/img]","replace"=>"<img src='./smilies/ernaehrung004.gif' border='0' alt='ernaehrung004.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/flour.gif[/img]","replace"=>"<img src='./smilies/flour.gif' border='0' alt='flour.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/google.gif[/img]","replace"=>"<img src='./smilies/google.gif' border='0' alt='google.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/gramps4.gif[/img]","replace"=>"<img src='./smilies/gramps4.gif' border='0' alt='gramps4.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/gunner.gif[/img]","replace"=>"<img src='./smilies/gunner.gif' border='0' alt='gunner.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/hatespammers.gif[/img]","replace"=>"<img src='./smilies/hatespammers.gif' border='0' alt='hatespammers.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/headshot.gif[/img]","replace"=>"<img src='./smilies/headshot.gif' border='0' alt='headshot.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/hug.gif[/img]","replace"=>"<img src='./smilies/hug.gif' border='0' alt='hug.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/imo.gif[/img]","replace"=>"<img src='./smilies/imo.gif' border='0' alt='imo.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/inq.gif[/img]","replace"=>"<img src='./smilies/inq.gif' border='0' alt='inq.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/jackhammer.gif[/img]","replace"=>"<img src='./smilies/jackhammer.gif' border='0' alt='jackhammer.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/jk.gif[/img]","replace"=>"<img src='./smilies/jk.gif' border='0' alt='jk.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/blahblah.gif[/img]","replace"=>"<img src='./smilies/blahblah.gif' border='0' alt='blahblah.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/machine_Gun.gif[/img]","replace"=>"<img src='./smilies/machine_Gun.gif' border='0' alt='machine_Gun.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/moo.gif[/img]","replace"=>"<img src='./smilies/moo.gif' border='0' alt='moo.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/newbie.gif[/img]","replace"=>"<img src='./smilies/newbie.gif' border='0' alt='newbie.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/offtopic.gif[/img]","replace"=>"<img src='./smilies/offtopic.gif' border='0' alt='offtopic.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/old.gif[/img]","replace"=>"<img src='./smilies/old.gif' border='0' alt='old.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/owned.gif[/img]","replace"=>"<img src='./smilies/owned.gif' border='0' alt='owned.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/ripper.gif[/img]","replace"=>"<img src='./smilies/ripper.gif' border='0' alt='ripper.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/rocker2.gif[/img]","replace"=>"<img src='./smilies/rocker2.gif' border='0' alt='rocker2.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/rocket3.gif[/img]","replace"=>"<img src='./smilies/rocket3.gif' border='0' alt='rocket3.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/smash.gif[/img]","replace"=>"<img src='./smilies/smash.gif' border='0' alt='smash.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/sniper.gif[/img]","replace"=>"<img src='./smilies/sniper.gif' border='0' alt='sniper.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/soon.gif[/img]","replace"=>"<img src='./smilies/soon.gif' border='0' alt='soon.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/spam1.gif[/img]","replace"=>"<img src='./smilies/spam1.gif' border='0' alt='spam1.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/stupid.gif[/img]","replace"=>"<img src='./smilies/stupid.gif' border='0' alt='stupid.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/transporter.gif[/img]","replace"=>"<img src='./smilies/transporter.gif' border='0' alt='transporter.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/ttidead.gif[/img]","replace"=>"<img src='./smilies/ttidead.gif' border='0' alt='ttidead.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/twocents.gif[/img]","replace"=>"<img src='./smilies/twocents.gif' border='0' alt='twocents.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/w00t.gif[/img]","replace"=>"<img src='./smilies/w00t.gif' border='0' alt='w00t.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/weirdo.gif[/img]","replace"=>"<img src='./smilies/weirdo.gif' border='0' alt='weirdo.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/whenitsdone.gif[/img]","replace"=>"<img src='./smilies/whenitsdone.gif' border='0' alt='whenitsdone.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/yeahthat.gif[/img]","replace"=>"<img src='./smilies/yeahthat.gif' border='0' alt='yeahthat.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/zap.gif[/img]","replace"=>"<img src='./smilies/zap.gif' border='0' alt='zap.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/paranoid.gif[/img]","replace"=>"<img src='./smilies/paranoid.gif' border='0' alt='paranoid.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/worthy.gif[/img]","replace"=>"<img src='./smilies/worthy.gif' border='0' alt='worthy.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/signs_word.gif[/img]","replace"=>"<img src='./smilies/signs_word.gif' border='0' alt='signs_word.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/wacko.gif[/img]","replace"=>"<img src='./smilies/wacko.gif' border='0' alt='wacko.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/censored.gif[/img]","replace"=>"<img src='./smilies/censored.gif' border='0' alt='censored.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/drunk.gif[/img]","replace"=>"<img src='./smilies/drunk.gif' border='0' alt='drunk.gif'>","type"=>"more"));
	     $tdb->add("smilies",array("bbcode"=>"[img]smilies/finger.gif[/img]","replace"=>"<img src='./smilies/finger.gif' border='0' alt='finger.gif'>","type"=>"more"));


    $tdb->tdb(DB_DIR.'/', 'posts.tdb');
		//$tdb->tdb(DB_DIR.'/', 'privmsg.tdb');
		//$tdb->createTable('1', array(array("box", "string", 6), array("from", "number", 7), array("to", "number", 7), array("icon", "string", 10), array("subject", "memo"), array("date", "number", 14), array("message", "memo"), array("id", "id")));
		// Add the new table to main.tdb
		$tdb->tdb(DB_DIR, "main.tdb");
		$tdb->createTable("uploads", array(
		array("name", "string", 4096),
			array("size", "number", 9),
			array("downloads", "number", 10),
			array("data", "memo"),
			array("id", "id")
		), 2048);
		if (!headers_sent()) {
			echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>UPB v2.1.1b Installer</title>
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />";
			echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>";
			echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>myUPB v2.1.1b Installer</div>
		<table class='main_table' cellspacing='1'>
			<tr>
				<th style='text-align:center;'>&nbsp;</th>
			</tr>
			<tr>
				<td class='area_welcome'><div class='welcome_text'>If you have any problems, please seek support at <a href='http://www.myupb.com/'>myupb.com's support forums!</a></div></td>
			</tr>
			<tr>
				<td class='footer_3'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;'>
					Thank you for choosing my Ultimate PHP Board.<br /><br />
					This script will guide you through the process of installing your new myUPB bulletin board.<br />
					Just select \"Proceed\" below and follow the instructions.<br /><br />";
			echo "<input type='hidden' name='add' value='1' /><input type='submit' value='Proceed' />";
			echo "</td>
			</tr>
			<tr>
				<td class='footer_3'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>";
			echo "
		</table>
		<div class='footer'><img src='skins/default/images/spacer.gif' alt='' title='' /></div>
	</div>
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2008</div>
</div>
</body>
</html>";
		} else {
			echo "
				<p>An error has ocurred in the script.  Please see above for details on the error to try to remody the problem.</p>";
		}
	} else {
		require_once('./includes/class/func.class.php');
	}
	if ($_POST["add"] == "2") {
		//Verify admin account
		$error = "";
		if ($_POST["username"] == "" || strlen($_POST["username"]) > 20) $error .= "<div style='text-align:center;font-weight:bold;'>Your Username is either too short or too long (max 20 chars, min 1 char)</div><br /><br />";
		if ($_POST["pass1"] != $_POST["pass2"]) $error .= "<div style='text-align:center;font-weight:bold;'>your pass and pass confirm are not matching!</div><br /><br />";
		if (strlen($_POST["pass1"]) < 5) $error .= "<div style='text-align:center;font-weight:bold;'>your password has to be longer then 4 characters!</div><br /><br />";
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["email"])) $error .= "<div style='text-align:center;font-weight:bold;'>Not a real e-mail address (ex. admin@host.com)</div><br /><br />";
		if ($_POST["view_email"] == "1") $view_email_checked = "CHECKED";
		else $_POST["view_email"] = "0";
		if (strlen($_POST["sig"]) > 200) $error .= "<div style='text-align:center;font-weight:bold;'>Your signature is too long (max 200 chars)</div><br /><br />";
		if ($error != "") {
			$_POST["add"] = 1;
		} else {
			$_POST["add"] = "3";
			$admin = array("user_name" => $_POST["username"], "password" => generateHash($_POST["pass1"]), "level" => 9, "email" => $_POST["email"], "view_email" => $_POST["view_email"], "mail_list" => $_POST["mail_list"], "location" => $_POST["location"], "url" => $_POST["url"], "avatar" => $_POST["avatar"], "icq" => $_POST["icq"], "aim" => $_POST["aim"], "msn" => $_POST["msn"], "sig" => $_POST["sig"], "posts" => 0, "date_added" => mkdate(),"lastvisit" => mkdate());
			$tdb->add("users", $admin);
			$f = fopen(DB_DIR."/new_pm.dat", 'w');
			fwrite($f, " 0");
			fclose($f);
			$config_file = file('config.php');
			unset($config_file[count($config_file) - 1]);
			$config_file[] = "define('ADMIN_EMAIL', '".$_POST["email"]."', true);";
			$config_file[] = '?>';

			?><?php
			$config_file = implode("\n", $config_file);
			$f = fopen('config.php', 'w');
			fwrite($f, $config_file);
			fclose($f);
			unset($config_file);
		}
	}
	if ($_POST['add'] == "4") {
		//
		$where = "Installation ".$_CONFIG["where_sep"]." Complete";
		require_once('./includes/header.php');
		$edit_config = array("title" => $_POST["title"], "fileupload_location" => $_POST["fileupload_location"], "fileupload_size" => $_POST["fileupload_size"], "homepage" => $_POST["homepage"]);
		$edit_regist = array("register_sbj" => $_POST["register_sbj"], "register_msg" => $_POST["register_msg"], "admin_email" => $_POST["admin_email"]);
		if ($config_tdb->editVars("config", $edit_config) && $config_tdb->editVars("regist", $edit_regist)) {
			$config_file = file('config.php');
			for($i = 0; $i < count($config_file); $i++) {
				if (empty($config_file[$i])) unset($config_file[$i]);
				if (strchr($config_file[$i], "INSTALLATION_MODE")) {
					$config_file[$i] = "define('INSTALLATION_MODE', false, true);";
					break;
				}
			}
			$config_file = implode("\n", $config_file);
			$f = fopen('config.php', 'w');
			fwrite($f, $config_file);
			fclose($f);
			unset($config_file);
			echo "
			<div class='alert_confirm'>
				<div class='alert_confirm_text'>
					<strong>myUPB Installation Complete!</div><div style='padding:4px;'>
					If you had any errors or you find that your forum is not working correctly, visit myUPB's support forums at <a href='http://www.myupb.com/' target='_blank'>www.myupb.com</a><br /><br />
					Delete the install.php and update1.x-2.0.php NOW, as it is a security risk to leave it in your server.<br /><br />
					<a href='javascript:window.close()'>Close Window</a> -or- <a href='index.php'>Go To Forum</a> -or- <a href='login.php?ref=admin_forums.php?action=add_cat'>Login and add categories</a>
				</div>
			</div>";
			require_once('./includes/footer.php');
		} else {
			echo "<div class='alert'><div class='alert_text'><strong>Step Five Failed!</strong></div><div style='padding:4px;'>Please seek help from <a href='http://www.myupb.com/' target='_blank'>www.myupb.com</a>.</div></div>";
			require_once('./includes/footer.php');
		}
	}
	if ($_POST["add"] == "3") {
		$where = "Installation ".$_CONFIG["where_sep"]." Initial Config Setup";
		echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>UPB v2.1.1b Installer</title>
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />";
		echo "
<form method='post' action='".$_SERVER['PHP_SELF']."'>";
		echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>Step #2: Setting up the configuration file</div>
		<table class='main_table' cellspacing='1'>
			<tr>
				<th colspan='2'>Basic Forum Settings</td>
			</tr>

      <tr>
				<td class='area_1' style='width:45%;'><strong>Title</strong><br />Title of the forum</td>
				<td class='area_2'><input type='text' name='title' size='40' value='".$_POST["title"]."' tabindex='1'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Register Email Subject</strong><br />
					This is the subject for confirmation of registration</td>
				<td class='area_2'><input type='text' name='register_sbj' size='40' value='".$_POST["register_sbj"]."' tabindex='2'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Register Email Message</strong><br />
					this is the message for confirmation of registration (options: &lt;login&gt; &lt;password&gt;)</td>
				<td class='area_2'><textarea rows='5' name='register_msg' cols='25' tabindex='3'>".$_POST["register_msg"]."</textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Location for file attachments</strong><br />
					Put the path to the directory for file attachments.&nbsp;</td>
				<td class='area_2'><input type='text' name='fileupload_location' size='40' value='".$_POST["fileupload_location"]."' tabindex='4'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Size limits for file upload</strong><br />
					In kilobytes, type in the maximum size allowed for file uploads.</td>
				<td class='area_2'><input type='text' name='fileupload_size' size='40' value='".$_POST["fileupload_size"]."' tabindex='5'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Admin E-mail</strong><br />
					this is the return address for confirmation of registration</td>
				<td class='area_2'><input type='text' name='admin_email' size='40' value='".$_POST["admin_email"]."' tabindex='6'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Homepage URL</strong><br />
					can be relative or a url</td>
				<td class='area_2'><input type='text' name='homepage' size='40' value='".$_POST["homepage"]."' tabindex='7'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_2' style='text-align:center;font-weight:bold;padding:12px;line-height:20px;' colspan='2'>For more settings, visit the admin Panel after installation.</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='hidden' name='add' value='4'><input type='submit' value='Submit' name='B1'><input type='reset' value='Reset' name='B2'></td>
			</tr>
		</table>
		<div class='footer'><img src='skins/default/images/spacer.gif' alt='' title='' /></div>
	</div>
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2008</div>
</div>
</body>
</html>";
	}
	if ($_POST["add"] == "1") {
		//Set up admin acccount
		$where = "Step #1: Setting up your admin account";
		$required = "#ff0000";
		if ($homepage == "") $homepage = "http://";
		if ($avatar == "") $avatar = "http://";
		echo "$error";
		echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
<head>
<title>UPB v2.1.1b Installer</title>
<link rel='stylesheet' type='text/css' href='skins/default/css/style.css' />
</head>
<body>
<div id='upb_container'>
	<div class='main_cat_wrapper2'>
		<table class='main_table_2' cellspacing='1'>
			<tr>
				<td id='logo'><img src='skins/default/images/logo.png' alt='' title='' /></td>
			</tr>
		</table>
	</div>
	<br />
	<br />";
		echo "
<form method='POST' action='".$_SERVER['PHP_SELF']."'>";
		echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>Step #1: Creating your admin account</div>
		<table class='main_table' cellspacing='1'>
			<tr>
				<th colspan='2'><strong><span style='color:$required;'>*</span> is a required field</strong></th>
			</tr>
			<tr>
				<td class='area_1' style='width:35%;'><strong>Username: <span style='color:$required;'>*</span></td>
				<td class='area_2'><input type='text' name='username' size='20' tabindex='1' maxlength='20' value='".$_POST["username"]."'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Password: <span style='color:$required;'>*</span></strong></td>
				<td class='area_2'><input type='password' name='pass1' size='20' tabindex='2' maxlength='20'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Verify Password: <span style='color:$required;'>*</span></strong></td>
				<td class='area_2'><input type='password' name='pass2' size='20' tabindex='3' maxlength='20'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Email: <span style='color:$required;'>*</span></strong></td>
				<td class='area_2'><input type='text' name='email' size='20' value='".$_POST["email"]."' tabindex='4'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Make email visible to the public?</strong></td>
				<td class='area_2'><input type='checkbox' name='view_email' value='1' ".$_POST["view_email_checked"]." tabindex='5'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Location:</strong></td>
				<td class='area_2'><input type='text' name='location' size='20' value='".$_POST["location"]."' maxlength='25' tabindex='6'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Homepage:</strong></td>
				<td class='area_2'><input type='text' name='homepage' size='20' value='".$_POST["homepage"]."' tabindex='7' maxlength='50'></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Avatar:</strong></td>
			<td class='area_2'>Choose an avatar in your UserCP after logging in.</td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='bar_icq'><strong>ICQ:</strong></td>
				<td class='area_2'><input type='text' name='icq' size='20' value='".$_POST["icq"]."' tabindex='9' maxlength='20'></td>
			</tr>
			<tr>
				<td class='bar_aim'><strong>AIM:</strong></td>
				<td class='area_2'><input type='text' name='aim' size='20' value='".$_POST["aim"]."' tabindex='10' maxlength='24'></td>
			</tr>
			<tr>
				<td class='bar_msnm'><strong>MSN:</strong></td>
				<td class='area_2'><input type='text' name='msn' size='20' value='".$_POST["msn"]."' tabindex='11'></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='area_1'><strong>Signature:</strong><br />Your signature is appended to each of your messages</td>
				<td class='area_2'><textarea rows='10' name='sig' cols='45' tabindex='12' maxlength='200'>".$_POST["sig"]."</textarea></td>
			</tr>
			<tr>
				<td class='footer_3' colspan='2'><img src='./skins/default/images/spacer.gif' alt='' title='' /></td>
			</tr>
			<tr>
				<td class='footer_3a' colspan='2' style='text-align:center;'><input type='hidden' name='add' value='2'><input type='submit' value='Submit' name='B1'><input type='reset' value='Reset' name='B2'></td>
			</tr>
		</table>
		<div class='footer'><img src='skins/default/images/spacer.gif' alt='' title='' /></div>
	</div>
</form>
<br />
<div class='copy'>Powered by myUPB&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href='http://www.myupb.com/'>PHP Outburst</a>
	&nbsp;&nbsp;&copy;2002 - 2008</div>
</div>
</body>
</html>";
	}
?>
