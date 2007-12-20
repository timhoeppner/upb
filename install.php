<?php
// install.php
// designed for Ultimate PHP Board
// Author: Jerroyd Moore, aka Rebles
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.4.1
ignore_user_abort();
if(TRUE !== is_writable('config.php')) die('Unable to continue with the installation process.  "config.php" in the root upb directory MUST exist and MUST BE writable.');
if(filesize('config.php') > 0) {
    require_once('config.php');
    if(INSTALLATION_MODE === FALSE) {
        $msg = '';
        if(isset($_COOKIE['user_env'])) $msg .= $_COOKIE['user_env'];
        else $msg .= 'Someone';
        if(isset($_COOKIE['id_env'])) $msg .= '(User ID:'.$_COOKIE['id_env'].')';
        if(isset($_SERVER['REMOTE_ADDR'])) $msg .= 'with the IP Address "'.$_SERVER['REMOTE_ADDR'].'"';
        $msg .= 'tried to initiate an installation or upgrade file.  Since the installation and upgrade files pose a risk, this could be an attempted hack.  The administrator, you, were notified.  It is advised you delete installation and upgrade files, which are no long use, and ban the IP Address and username, if given.';
        @mail(ADMIN_EMAIL, 'SECURITY ALERT on your forum', $msg);
        die('<b>Security Risk</b>:  Unable to initiate installation. An Administrater must put the forum in Installation Mode.  You\'re IP Address has been sent to the administrater aswell as login information.');
    }
}

if(@$_POST["add"] == "" || !isset($_POST["add"])) {
    //define and create the new database folder
    if(!defined('DB_DIR')) {
        define('DB_DIR', './'.uniqid('data_', true), true);
        $f = fopen('config.php', 'w');
        fwrite($f, "<?php\ndefine('INSTALLATION_MODE', true, true);\ndefine('UPB_VERSION', '2.1.1b', true);\ndefine('DB_DIR', '".DB_DIR."', true);\n?>");?><?php //the open and close makes it syntax highlighter friendly
        fclose($f);
    }
    if(!is_dir(DB_DIR)) {
        if(!mkdir(DB_DIR, 0777)) die('The forum must be able to create a folder in the root forum folder.  Please chmod() the root folder to 777 and rerun the script');
        @mkdir(DB_DIR.'/backup', 0777);
    }
    //end

    //create *.dat files and folders
    $f = fopen(DB_DIR.'/badwords.dat', 'w');
    fwrite($f, "shit\ndamn\ndam\nass\nfuck\ncunt\npussy\nbitch\n");
    fclose($f);

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
    fwrite($f, "configMain Configuration SettingsstatusMembers' Statuses SettingsregistNewly Registered Members Settingsconfig1Main Forum Configstatus2Member statusstatus3Moderator statusstatus4Admin Statusstatus5Member status Colorsstatus6Who's Online User Colorsregist7New Users' Confirmation E-mailregist8Users' Avatars");
    fclose($f);

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
    //$tdb->createDatabase(DB_DIR."/", "smilies.tdb");
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
      array("id", "id")
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
    $tdb->add("ext_config", array("name" => "ver", "value" => "2.0 BETA 1.4", "type" => "config", "form_object" => "hidden", "data_type" => "string"));
    $tdb->add("config", array("name" => "ver", "value" => "2.0 BETA 1.4", "type" => "config"));
    $tdb->add("ext_config", array("name" => "title", "value" => "Discussion Forums", "type" => "config", "title" => "Title", "description" => "Title of the forum", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "1"));
    $tdb->add("config", array("name" => "title", "value" => "Discussion Forums", "type" => "config"));
    $tdb->add("ext_config", array("name" => "table_width_main", "value" => "98%", "type" => "config", "title" => "Table Width", "description" => "This will change the table width of the main section of the forums", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "10"));
    $tdb->add("config", array("name" => "table_width_main", "value" => "98%", "type" => "config"));
    $tdb->add("ext_config", array("name" => "posts_per_page", "value" => "20", "type" => "config", "title" => "Posts Per Page", "description" => "this is how many posts will be displays on each page for topics",  "form_object" => "text", "data_type" => "number", "minicat" => "1", "sort" => "4"));
    $tdb->add("config", array("name" => "posts_per_page", "value" => "20", "type" => "config"));
    $tdb->add("ext_config", array("name" => "topics_per_page", "value" => "40", "type" => "config", "title" => "Topics per Page", "description" => "this is how many topics will be displays on each page for forums", "form_object" => "text", "data_type" => "number", "minicat" => "1", "sort" => "5"));
    $tdb->add("config", array("name" => "topics_per_page", "value" => "40", "type" => "config"));
    $tdb->add("ext_config", array("name" => "logo", "value" => "images/logo.gif", "type" => "config", "title" => "Logo Location", "description" => "can be relative or a url", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "2"));
    $tdb->add("config", array("name" => "logo", "value" => "images/logo.gif", "type" => "config"));
    $tdb->add("ext_config", array("name" => "homepage", "type" => "config", "title" => "Homepage URL", "description" => "can be relative or a url", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "3"));
    $tdb->add("config", array("name" => "homepage", "type" => "config"));

    $tdb->add("ext_config", array("name" => "admin_catagory_sorting", "type" => "config", "title" => "Category Sorting", "description" => "Sort the categories in the order you want them to appear on the main page", "form_object" => "list", "data_type" => "string", "minicat" => "1", "sort" => "16"));
    $tdb->add("config", array("name" => "admin_catagory_sorting", "type" => "config"));
    $tdb->add("ext_config", array("name" => "servicemessage", "type" => "config", "title" => "Service Messages", "description" => "Service Messages appear above the forum, if nothing input, Announcements will not be displayed. Html is allowed.", "form_object" => "textarea", "data_type" => "string", "minicat" => "1", "sort" => "17"));
    $tdb->add("config", array("name" => "servicemessage", "type" => "config"));

    $tdb->add("ext_config", array("name" => "skin_dir", "value" => "./skins/default", "type" => "config", "title" => "Skin Directory", "description" => "leave it unless you upload another skin", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "12"));
    $tdb->add("config", array("name" => "skin_dir", "value" => "./skins/default", "type" => "config"));

    $tdb->add("ext_config", array("name" => "fileupload_location", "value" => "./uploads", "type" => "config", "title" => "Location for file attachments", "description" => "Put the path to the directory for file attachments.<br>e.g. If your forums are located at http://forum.myupb.com, and your uploads directory is at http://forum.myupb.com/uploads, you would simply put 'uploads' (without quotes) in the box.", "form_object" => "text", "data_type" => "number", "minicat" => "1", "sort" => "6"));
    $tdb->add("config", array("name" => "fileupload_location", "value" => "./uploads", "type" => "config"));
    $tdb->add("ext_config", array("name" => "fileupload_size", "value" => "50", "type" => "config", "title" => "Size limits for file upload", "description" => "In kilobytes, type in the maximum size allowed for file uploads", "form_object" => "text", "data_type" => "number", "minicat" => "1", "sort" => "7"));
    $tdb->add("config", array("name" => "fileupload_size", "value" => "50", "type" => "config"));
    $tdb->add("ext_config", array("name" => "censor", "value" => "*censor*", "type" => "config", "title" => "Word to replace bad words", "description" => "Words that will replace bad words in a post", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "13"));
    $tdb->add("config", array("name" => "censor", "value" => "*censor*", "type" => "config"));
    $tdb->add("ext_config", array("name" => "sticky_note", "value" => "[Stick Note]", "type" => "config", "title" => "Sticky Note Text", "description" => "Text that appends the title indicating it is a \"Stickied Topic\" (HTML Tags Allowed)", "form_object" => "text", "data_type" => "string", "minicat" => "1", "sort" => "14"));
    $tdb->add("config", array("name" => "sticky_note", "value" => "[Stick Note]", "type" => "config"));
    $tdb->add("ext_config", array("name" => "sticky_after", "value" => "1", "type" => "config", "title" => "Sticky Note Before or After Title", "description" => "If this is checked, the \"sticky note\" text will appear after the title.  Unchecking this will display it before the title.", "form_object" => "checkbox", "minicat" => "1", "sort" => "15"));
    $tdb->add("config", array("name" => "sticky_after", "value" => "1", "type" => "config"));

    $tdb->add("ext_config", array("name" => "pm_max_outbox_msg", "value" => "50", "type" => "config", "title" => "Max Number of Private Msgs in a Users OutBox", "description" => "Can be set to 0 to infinity", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "18"));
    $tdb->add("config", array("name" => "pm_max_outbox_msg", "value" => "50", "type" => "config"));
    $tdb->add("ext_config", array("name" => "pm_version", "value" => "1.1c", "type" => "config", "form_object" => "hidden", "data_type" => "string"));
    $tdb->add("config", array("name" => "pm_version", "value" => "1.1c", "type" => "config"));

    $tdb->add("ext_config", array("name" => "avatar_width", "value" => "60", "type" => "config", "title" => "Avatars' Width", "description" => "The width (with respect to the height) of user avatars you want to be displayed at in pixels (Cannot be higher than 999)", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "8"));
    $tdb->add("config", array("name" => "avatar_width", "value" => "60", "type" => "config"));
    $tdb->add("ext_config", array("name" => "avatar_height", "value" => "60", "type" => "config", "title" => "Avatars' Height", "description" => "The height (with respect to the width) of user avatars you want to be displayed at in pixels (Cannot be higher than 999)", "form_object" => "text", "data_object" => "number", "minicat" => "1", "sort" => "9"));
    $tdb->add("config", array("name" => "avatar_height", "value" => "60", "type" => "config"));

    $tdb->add("ext_config", array("name" => "Create List", "value" => "more_smilies_create_list.php", "type" => "config", "title" => "Adding More Smilies", "description" => "Click on the link if you have recently added more smilies to your <b>moresmilies</b> directory", "form_object" => "link", "minicat" => "1", "sort" => "11"));
    $tdb->add("config", array("name" => "Create List", "value" => "more_smilies_create_list.php", "type" => "config"));

    //$_REGISTER
    @$tdb->add("ext_config", array("name" => "register_sbj", "value" => $register_sbj, "type" => "regist", "title" => "Register Email Subject", "description" => "this is the subject for confirmation of registration", "form_object" => "text", "data_type" => "string", "minicat" => "7", "sort" => "2"));
    @$tdb->add("config", array("name" => "register_sbj", "value" => $register_sbj, "type" => "regist"));
    @$tdb->add("ext_config", array("name" => "register_msg", "value" => $register_msg, "type" => "regist", "title" => "Register Email Message", "description" => "this is the message for confirmation of registration (options: &lt;login&gt; &lt;password&gt;)", "form_object" => "textarea", "data_type" => "string", "minicat" => "7", "sort" => "3"));
    @$tdb->add("config", array("name" => "register_msg", "value" => $register_msg, "type" => "regist"));
    @$tdb->add("ext_config", array("name" => "admin_email", "value" => $admin_email, "type" => "regist", "title" => "Admin E-mail", "description" => "this is the return address for confirmation of registration", "form_object" => "text", "data_type" => "string", "minicat" => "7", "sort" => "1"));
    @$tdb->add("config", array("name" => "admin_email", "value" => $admin_email, "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar1", "value" => "dome.jpg", "type" => "regist", "title" => "Avatar 1", "description" => "The first avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "2"));
    $tdb->add("config", array("name" => "avatar1", "value" => "dome.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar2", "value" => "chic.jpg", "type" => "regist", "title" => "Avatar 2", "description" => "The second avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "3"));
    $tdb->add("config", array("name" => "avatar2", "value" => "chic.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar3", "value" => "woman.jpg", "type" => "regist", "title" => "Avatar 3", "description" => "The third avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "4"));
    $tdb->add("config", array("name" => "avatar3", "value" => "woman.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar4", "value" => "wizard.jpg", "type" => "regist", "title" => "Avatar 4", "description" => "The fourth avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "5"));
    $tdb->add("config", array("name" => "avatar4", "value" => "wizard.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar5", "value" => "keeper.jpg", "type" => "regist", "title" => "Avatar 5", "description" => "The fifth avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "6"));
    $tdb->add("config", array("name" => "avatar5", "value" => "keeper.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar6", "value" => "knight.jpg", "type" => "regist", "title" => "Avatar 6", "description" => "The sixth avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "7"));
    $tdb->add("config", array("name" => "avatar6", "value" => "knight.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar7", "value" => "snake.jpg", "type" => "regist", "title" => "Avatar 7", "description" => "The seventh avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "8"));
    $tdb->add("config", array("name" => "avatar7", "value" => "snake.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar8", "value" => "spice.jpg", "type" => "regist", "title" => "Avatar 8", "description" => "The eighth avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "9"));
    $tdb->add("config", array("name" => "avatar8", "value" => "spice.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "avatar9", "value" => "vampire.jpg", "type" => "regist", "title" => "Avatar 9", "description" => "The nineth avatar on the selection menu for new users", "form_object" => "text", "data_type" => "string", "minicat" => "8", "sort" => "10"));
    $tdb->add("config", array("name" => "avatar9", "value" => "vampire.jpg", "type" => "regist"));
    $tdb->add("ext_config", array("name" => "newuseravatars", "value" => "true", "type" => "regist", "title" => "Avatars for new users", "description" => "Would you like to define the avatars that members under 50 posts can use? (After 50 posts they may use whatever they like)", "form_object" => "checkbox", "minicat" => "8", "sort" => "1"));
    $tdb->add("config", array("name" => "newuseravatars", "value" => "true", "type" => "regist"));

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
    $tdb->add("ext_config", array("name" => "mod_status1", "value" => "Moderator<br>&nbsp;Trainee", "type" => "status", "title" => "Moderator post status 1", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "1"));
    $tdb->add("config", array("name" => "mod_status1", "value" => "Moderator<br>&nbsp;Trainee", "type" => "status"));
    $tdb->add("ext_config", array("name" => "mod_status2", "value" => "Moderator<br>&nbsp;Clerk", "type" => "status", "title" => "Moderator post status 2", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "3"));
    $tdb->add("config", array("name" => "mod_status2", "value" => "Moderator<br>&nbsp;Clerk", "type" => "status"));
    $tdb->add("ext_config", array("name" => "mod_status3", "value" => "Moderator<br>&nbsp;Shift Manager", "type" => "status", "title" => "Moderator post status 3", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "5"));
    $tdb->add("config", array("name" => "mod_status3", "value" => "Moderator<br>&nbsp;Shift Manager", "type" => "status"));
    $tdb->add("ext_config", array("name" => "mod_status4", "value" => "Moderator<br>&nbsp;Manager", "type" => "status", "title" => "Moderator post status 4", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "7"));
    $tdb->add("config", array("name" => "mod_status4", "value" => "Moderator<br>&nbsp;Manager", "type" => "status"));
    $tdb->add("ext_config", array("name" => "mod_status5", "value" => "Moderator<br>&nbsp;Super-Moderator 1000", "type" => "status", "title" => "Moderator post status 5", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "3", "sort" => "9"));
    $tdb->add("config", array("name" => "mod_status5", "value" => "Moderator<br>&nbsp;Super-Moderator 1000", "type" => "status"));
    $tdb->add("ext_config", array("name" => "admin_status1", "value" => "Administrator<br>&nbsp;Script Junkie", "type" => "status", "title" => "Admin post status 1", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "1"));
    $tdb->add("config", array("name" => "admin_status1", "value" => "Administrator<br>&nbsp;Script Junkie", "type" => "status"));
    $tdb->add("ext_config", array("name" => "admin_status2", "value" => "Administrator<br>&nbsp;Programming<br>&nbsp;King", "type" => "status", "title" => "Admin post status 2", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "3"));
    $tdb->add("config", array("name" => "admin_status1", "value" => "Administrator<br>&nbsp;Programming<br>&nbsp;King", "type" => "status"));
    $tdb->add("ext_config", array("name" => "admin_status3", "value" => "Administrator<br>&nbsp;Programmer<br>&nbsp;Extraordinaire", "type" => "status", "title" => "Admin post status 3", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "5"));
    $tdb->add("config", array("name" => "admin_status3", "value" => "Administrator<br>&nbsp;Programmer<br>&nbsp;Extraordinaire", "type" => "status"));
    $tdb->add("ext_config", array("name" => "admin_status4", "value" => "Administrator<br>&nbsp;CompuGlobal<br>&nbsp;HyperMegaGuy", "type" => "status", "title" => "Admin post status 4", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "7"));
    $tdb->add("config", array("name" => "admin_status4", "value" => "Administrator<br>&nbsp;CompuGlobal<br>&nbsp;HyperMegaGuy", "type" => "status"));
    $tdb->add("ext_config", array("name" => "admin_status5", "value" => "Administrator<br>&nbsp;Programming<br>&nbsp;Guru", "type" => "status", "title" => "Admin post status 5", "description" => "According to post count", "form_object" => "text", "data_type" => "string", "minicat" => "4", "sort" => "9"));
    $tdb->add("config", array("name" => "admin_status5", "value" => "Administrator<br>&nbsp;Programming<br>&nbsp;Guru", "type" => "status"));
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
    
    /*SMILIES
    $tdb->add('smilies',array("bbcode"=>" :)","replace"=> " <img src='smilies/smile.gif' border='0' alt=':)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" :(", "replace"=>" <img src='smilies/frown.gif' border='0' alt=':('> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" ;)","replace"=> " <img src='smilies/wink.gif' border='0' alt=';)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" :P","replace"=> " <img src='smilies/tongue.gif' border='0' alt=':P'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" :o","replace"=> " <img src='smilies/eek.gif' border='0' alt=':o'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" :D","replace"=> " <img src='smilies/biggrin.gif' border='0' alt=':D'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (C)","replace"=> " <img src='smilies/cool.gif' border='0' alt='(C)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (M)","replace"=> " <img src='smilies/mad.gif' border='0' alt='(M)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (confused)","replace"=> " <img src='smilies/confused.gif' border='0' alt='(confused)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (crazy)","replace"=> " <img src='smilies/crazy.gif' border='0' alt='(crazy)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (hm)","replace"=> " <img src='smilies/hm.gif' border='0' alt='(hm)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (hmmlaugh)","replace"=> " <img src='smilies/hmmlaugh.gif' border='0' alt='(hmmlaugh)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (offtopic)","replace"=> " <img src='smilies/offtopic.gif' border='0' alt='(offtopic)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (blink)","replace"=> " <img src='smilies/blink.gif' border='0' alt='(blink)'> ","type" => "main")); 
    $tdb->add('smilies',array("bbcode"=>" (rofl)","replace"=> " <img src='smilies/rofl.gif' border='0' alt='(rofl)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (R)","replace"=> " <img src='smilies/redface.gif' border='0' alt='(R)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (E)","replace"=> " <img src='smilies/rolleyes.gif' border='0' alt='(E)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (wallbash)","replace"=> " <img src='smilies/wallbash.gif' border='0' alt='(wallbash)'> ","type" => "main"));
    $tdb->add('smilies',array("bbcode"=>" (noteeth)","replace"=> " <img src='smilies/noteeth.gif' border='0' alt='(noteeth)'> ","type" => "main"));
    
    //MORE SMILIES (more smilies page)
    $tdb->add('smilies',array("bbcode"=>" LOL","replace"=> " <img src='smilies/lol.gif' border='0' alt='LOL'> ","type" => "main"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-035.gif[/img]","replace"=>"<img src='smilies/action-smiley-035.gif' border='0' alt='action-smiley-035.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/action-smiley-073.gif[/img]","replace"=>"<img src='smilies/action-smiley-073.gif' border='0' alt='action-smiley-073.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/anti-old.gif[/img]","replace"=>"<img src='smilies/anti-old.gif' border='0' alt='anti-old.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/blamesheep.gif[/img]","replace"=>"<img src='smilies/blamesheep.gif' border='0' alt='blamesheep.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/bump.gif[/img]","replace"=>"<img src='smilies/bump.gif' border='0' alt='bump.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/chainsaw.gif[/img]","replace"=>"<img src='smilies/chainsaw.gif' border='0' alt='chainsaw.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/closed.gif[/img]","replace"=>"<img src='smilies/closed.gif' border='0' alt='closed.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/enforcer.gif[/img]","replace"=>"<img src='smilies/enforcer.gif' border='0' alt='enforcer.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/ernaehrung004.gif[/img]","replace"=>"<img src='smilies/ernaehrung004.gif' border='0' alt='ernaehrung004.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/flour.gif[/img]","replace"=>"<img src='smilies/flour.gif' border='0' alt='flour.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/google.gif[/img]","replace"=>"<img src='smilies/google.gif' border='0' alt='google.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/gramps4.gif[/img]","replace"=>"<img src='smilies/gramps4.gif' border='0' alt='gramps4.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/gunner.gif[/img]","replace"=>"<img src='smilies/gunner.gif' border='0' alt='gunner.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/hatespammers.gif[/img]","replace"=>"<img src='smilies/hatespammers.gif' border='0' alt='hatespammers.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/headshot.gif[/img]","replace"=>"<img src='smilies/headshot.gif' border='0' alt='headshot.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/hug.gif[/img]","replace"=>"<img src='smilies/hug.gif' border='0' alt='hug.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/imo.gif[/img]","replace"=>"<img src='smilies/imo.gif' border='0' alt='imo.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/inq.gif[/img]","replace"=>"<img src='smilies/inq.gif' border='0' alt='inq.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/jackhammer.gif[/img]","replace"=>"<img src='smilies/jackhammer.gif' border='0' alt='jackhammer.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/jk.gif[/img]","replace"=>"<img src='smilies/jk.gif' border='0' alt='jk.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/laser.gif[/img]","replace"=>"<img src='smilies/laser.gif' border='0' alt='laser.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/laser2.gif[/img]","replace"=>"<img src='smilies/laser2.gif' border='0' alt='laser2.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/laser3.gif[/img]","replace"=>"<img src='smilies/laser3.gif' border='0' alt='laser3.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/blahblah.gif[/img]","replace"=>"<img src='smilies/blahblah.gif' border='0' alt='blahblah.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/machine_Gun.gif[/img]","replace"=>"<img src='smilies/machine_Gun.gif' border='0' alt='machine_Gun.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/moo.gif[/img]","replace"=>"<img src='smilies/moo.gif' border='0' alt='moo.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/newbie.gif[/img]","replace"=>"<img src='smilies/newbie.gif' border='0' alt='newbie.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/offtopic.gif[/img]","replace"=>"<img src='smilies/offtopic.gif' border='0' alt='offtopic.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/old.gif[/img]","replace"=>"<img src='smilies/old.gif' border='0' alt='old.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/owned.gif[/img]","replace"=>"<img src='smilies/owned.gif' border='0' alt='owned.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/ripper.gif[/img]","replace"=>"<img src='smilies/ripper.gif' border='0' alt='ripper.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/rocker2.gif[/img]","replace"=>"<img src='smilies/rocker2.gif' border='0' alt='rocker2.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/rocket3.gif[/img]","replace"=>"<img src='smilies/rocket3.gif' border='0' alt='rocket3.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/smash.gif[/img]","replace"=>"<img src='smilies/smash.gif' border='0' alt='smash.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/sniper.gif[/img]","replace"=>"<img src='smilies/sniper.gif' border='0' alt='sniper.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/soon.gif[/img]","replace"=>"<img src='smilies/soon.gif' border='0' alt='soon.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/spam1.gif[/img]","replace"=>"<img src='smilies/spam1.gif' border='0' alt='spam1.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/stupid.gif[/img]","replace"=>"<img src='smilies/stupid.gif' border='0' alt='stupid.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/transporter.gif[/img]","replace"=>"<img src='smilies/transporter.gif' border='0' alt='transporter.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/ttidead.gif[/img]","replace"=>"<img src='smilies/ttidead.gif' border='0' alt='ttidead.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/twocents.gif[/img]","replace"=>"<img src='smilies/twocents.gif' border='0' alt='twocents.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/w00t.gif[/img]","replace"=>"<img src='smilies/w00t.gif' border='0' alt='w00t.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/weirdo.gif[/img]","replace"=>"<img src='smilies/weirdo.gif' border='0' alt='weirdo.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/whenitsdone.gif[/img]","replace"=>"<img src='smilies/whenitsdone.gif' border='0' alt='whenitsdone.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/yeahthat.gif[/img]","replace"=>"<img src='smilies/yeahthat.gif' border='0' alt='yeahthat.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/zap.gif[/img]","replace"=>"<img src='smilies/zap.gif' border='0' alt='zap.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/paranoid.gif[/img]","replace"=>"<img src='smilies/paranoid.gif' border='0' alt='paranoid.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/worthy.gif[/img]","replace"=>"<img src='smilies/worthy.gif' border='0' alt='worthy.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/signs_word.gif[/img]","replace"=>"<img src='smilies/signs_word.gif' border='0' alt='signs_word.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/wacko.gif[/img]","replace"=>"<img src='smilies/wacko.gif' border='0' alt='wacko.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/censored.gif[/img]","replace"=>"<img src='smilies/censored.gif' border='0' alt='censored.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/drunk.gif[/img]","replace"=>"<img src='smilies/drunk.gif' border='0' alt='drunk.gif'>","type"=>"more"));
    $tdb->add("smilies",array("bbcode"=>"[img]smilies/finger.gif[/img]","replace"=>"<img src='smilies/finger.gif' border='0' alt='finger.gif'>","type"=>"more"));
    */
    $tdb->tdb(DB_DIR.'/', 'posts.tdb');
    $tdb->createTable('trackforums', array(array('fId', 'number', 7), array('uId', 'number', 7), array('lastvisit', 'number', 14), array('id', 'id')));
    $tdb->createTable('tracktopics', array(array('fId', 'number', 7), array('tId', 'number', 7), array('uId', 'number', 7), array('old', 'number', 1), array('id', 'id')));

    //$tdb->tdb(DB_DIR.'/', 'privmsg.tdb');
    //$tdb->createTable('1', array(array("box", "string", 6), array("from", "number", 7), array("to", "number", 7), array("icon", "string", 10), array("subject", "memo"), array("date", "number", 14), array("message", "memo"), array("id", "id")));
    
    // Add the new table to main.tdb
    $tdb->tdb(DB_DIR, "main.tdb");
    $tdb->createTable("uploads", array(
        array("name", "string", 80),
        array("size", "number", 9),
        array("downloads", "number", 10),
        array("data", "memo"),
        array("id", "id")
    ), 2048);

    if(!headers_sent()) {
        echo "<html><head><title>UPB v2.0 Installation</title></head><body>Thank you for choosing Ultimate PHP Board v2.0.  This script will guide you through the process of installing your brand-new UPB version 2.0!  Just follow the instructions through the very end.<br><br>";
        echo "<form action=".$_SERVER['PHP_SELF']." method='POST'><input type='hidden' name='add' value='1'><input type='submit' value='Proceed'>";
    } else {
        echo '<html><head><title>UPB v2.0 Installation</title></head><body><p>An error has ocurred in the script.  Please see above for details on the error to try to remody the problem.</p>';
    }
} else {
    require_once('./includes/class/func.class.php');
}

if(@$_POST["add"] == "2") {
    //Verify admin account

    $error = "";
    if($_POST["username"] == "" || strlen($_POST["username"]) > 20) $error .= "Your Username is either too short or too long (max 20 chars, min 1 char)<br>";
    if($_POST["pass1"] != $_POST["pass2"]) $error .= "your pass and pass confirm are not matching!<br>";
    if(strlen($_POST["pass1"]) < 5) $error .= "your password has to be longer then 4 characters<br>";
    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $_POST["email"])) $error .= "Not a real e-mail address (ex. admin@host.com)<br>";
    if($_POST["view_email"] == "1") $view_email_checked = "CHECKED";
    else $_POST["view_email"] = "0";
    if(strlen($_POST["sig"]) > 200) $error .= "Your signature is too long (max 200 chars)<br>";
    if($error != "") {
        @$_POST["add"] = 1;
        $add = 1;
    } else {
        @$_POST["add"] = "3";
        $add = 3;
        $admin = array("user_name" => $_POST["username"], "password" => generateHash($_POST["pass1"]), "level" => 3, "email" => $_POST["email"], "view_email" => $_POST["view_email"], "mail_list" => $_POST["mail_list"], "location" => $_POST["location"], "url" => $_POST["url"], "avatar" => $_POST["avatar"], "icq" => $_POST["icq"], "aim" => $_POST["aim"], "msn" => $_POST["msn"], "sig" => $_POST["sig"], "posts" => 0, "date_added" => mkdate());
        $f = fopen(DB_DIR."/lastvisit.dat", 'w');
        fwrite($f, mkdate().str_repeat(' ', (14 - strlen(mkdate()))));
        fclose($f);
        $tdb->add("users", $admin);

        $f = fopen(DB_DIR."/new_pm.dat", 'w');
        fwrite($f, " 0");
        fclose($f);

        $config_file = file('config.php');
        unset($config_file[count($config_file) - 1]);
        $config_file[] = "define('ADMIN_EMAIL', '".$_POST["email"]."', true);";
        $config_file[] = '?>';?><?php //makes it syntax highlighter friendly
        $config_file = implode("\n", $config_file);
        $f = fopen('config.php', 'w');
        fwrite($f, $config_file);
        fclose($f);
        unset($config_file);
    }
}

if(@$_POST['add'] == "4") {
    //
    $where = "Installation ".$_CONFIG["where_sep"]." Complete";
    require_once('./includes/header.php');

    $edit_config = array("title" => $_POST["title"], "fileupload_location" => $_POST["fileupload_location"], "fileupload_size" => $_POST["fileupload_size"], "homepage" => $_POST["homepage"]);
    $edit_regist = array("register_sbj" => $_POST["register_sbj"], "register_msg" => $_POST["register_msg"], "admin_email" => $_POST["admin_email"]);
    if($config_tdb->editVars("config", $edit_config) && $config_tdb->editVars("regist", $edit_regist)) {
        $config_file = file('config.php');
        for($i=0;$i<count($config_file);$i++) {
            if(empty($config_file[$i])) unset($config_file[$i]);
            if(strchr($config_file[$i], "INSTALLATION_MODE")) {
                $config_file[$i] = "define('INSTALLATION_MODE', false, true);";
                break;
            }
        }
        $config_file = implode("\n", $config_file);
        $f = fopen('config.php', 'w');
        fwrite($f, $config_file);
        fclose($f);
        unset($config_file);

        echo "<br>Installation Complete!  If you had any errors or you find that your forum is not working correctly, Visit UPB's support forums at <a href='http://forum.myupb.com/' target='_blank'>forum.myupb.com</a>";
        echo "<br><b>Delete the install.php and update2.1.1b.php NOW, as it is a security risk to leave it in your server.</b>";
        echo "<br><a href='javascript:window.close()'>Close Window</a> -or- <a href='index.php'>Go To Forum</a> -or- <a href='login.php?ref=admin_cat.php?action=addnew'>Login and add categories</a>";
    } else {
        echo "Step Five Failed.  Please seek help from myupb.com.";
    }
}

if(@$_POST["add"] == "3" || @$add == 3) {
    $where = "Installation ".$_CONFIG["where_sep"]." Initial Config Setup";
    require_once('./includes/header.php');
    echo "<form method='POST' action='".$_SERVER['PHP_SELF']."'><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 bgcolor='$table2' align='center'>
    <tr><td colspan='7' bgcolor='$header'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Basic Forum Settings</font></b></td></tr>
    <tr><td width='31%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Title</b><br> <font size='$font_s'>Title of the forum</font></font></td>
    <td width='69%' bgcolor='$table1'><input type='text' name='title' size='40' value='".$_POST["title"]."' tabindex='1'></td></tr>
    <tr><td width='31%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Register Email Subject</b><br> <font size='$font_s'>this is the subject for confirmation of registration</font</font></td>
    <td width='69%'><input type='text' name='register_sbj' size='40' value='".$_POST["register_sbj"]."' tabindex='2'></td></tr>
    <tr><td width='31%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Register Email Message</b><br> <font size='$font_s'>this is the message for confirmation of registration (options: &lt;login&gt; &lt;password&gt;)</font></font></td>
    <td width='69%' bgcolor='$table1'><textarea rows='5' name='register_msg' cols='25' tabindex='3'>".$_POST["register_msg"]."</textarea></td></tr>
    <tr><td width='31%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Location for file attachments</b><br> <font size='$font_s'>Put the path to the directory for file attachments.&nbsp;</font></font></td>
    <td width='69%'><input type='text' name='fileupload_location' size='40' value='".$_POST["fileupload_location"]."' tabindex='4'></td></tr>
    <tr><td width='31%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Size limits for file upload</b><br> <font size='$font_s'>In kilobytes, type in the maximum size allowed for file uploads.</font></font></td>
    <td width='69%' bgcolor='$table1'><input type='text' name='fileupload_size' size='40' value='".$_POST["fileupload_size"]."' tabindex='5'></td></tr>
    <tr><td width='31%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Admin E-mail</b><br> <font = size='$font_s'>this is the return address for confirmation of registration</font></font></td>
    <td width='69%'><input type='text' name='admin_email' size='40' value='".$_POST["admin_email"]."' tabindex='6'></td></tr>
    <tr><td width='31%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Homepage URL</b><br> <font size='$font_s'>can be relative or a url</font></font></td>
    <td width='69%' bgcolor='$table1'><input type='text' name='homepage' size='40' value='".$_POST["homepage"]."' tabindex='7'></td></tr>
    <tr><td width='100%' colspan='2'><input type='submit' value='Submit' name='B1'><input type='reset' value='Reset' name='B2'><BR><i>For more settings, visit the admin Panel after installation</i></td></tr></table>$skin_tablefooter
    <input type='hidden' name='add' value='4'></form>";

}

if(@$_POST["add"] == "1" || @$add == 1) {
    //Set up admin acccount

    $where = "Installation ".$_CONFIG["where_sep"]." Setup Admin Account";
    require_once('./includes/header.php');
    if($homepage == "") $homepage = "http://";
    if($avatar == "") $avatar = "http://";
    echo "$error</center>";
    echo "<br><b>1. Setting up an Administration account</b>
    <form method='POST' action='".$_SERVER['PHP_SELF']."'><center>";
    echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
    echo "<table width='".$_CONFIG["table_width_main"]."' cellspacing=1 cellpadding=3 bgcolor='$table2' align='center'>
    <tr><td colspan='7' bgcolor='$header' align='left' valign='center'><B><font size='$font_l' face='$font_face' color='$font_color_header'>Setup Admin Account</font></b></td></tr>
    <tr><td width='19%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Username<font color='red'>*</font></b></font></td>
    <td width='81%'><input type='text' name='username' size='20' tabindex='1' maxlength='20' value='".$_POST["username"]."'></td></tr>
    <tr><td width='19%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Password<font color='red'>*</font></b></font></td>
    <td width='81%' bgcolor='$table1'><input type='password' name='pass1' size='20' tabindex='2' maxlength='20'></td></tr>
    <tr><td width='19%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Verify Password<font color='red'>*</font></b></font></td>
    <td width='81%'><input type='password' name='pass2' size='20' tabindex='3' maxlength='20'></td></tr>
    <tr><td width='19%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>email<font color='red'>*</font></b></font></td>
    <td width='81%' bgcolor='$table1'><input type='text' name='email' size='20' value='".$_POST["email"]."' tabindex='4'></td></tr>
    <tr><td width='100%' colspan='2''><font size='$font_m' face='$font_face' color='$font_color_main'>Allow other users to see your e-email address? <input type='checkbox' name='view_email' value='1' ".$_POST["view_email_checked"]." tabindex='5'></font></td></tr>
    <tr><td width='19%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Location</b></font></td>
    <td width='81%' bgcolor='$table1'><input type='text' name='location' size='20' value='".$_POST["location"]."' maxlength='25' tabindex='6'></td></tr>
    <tr><td width='19%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Homepage</b></font></td>
    <td width='81%'><input type='text' name='homepage' size='20' value='".$_POST["homepage"]."' tabindex='7' maxlength='50'></td></tr>
    <tr><td width='19%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>Avatar</b></font></td>
    <td width='81%' bgcolor='$table1'><input type='text' name='avatar' size='20' value='".$_POST["avatar"]."' tabindex='8' maxlength='50'></td></tr>
    <tr><td width='19%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>icq</b></font></td>
    <td width='81%'><input type='text' name='icq' size='20' value='".$_POST["icq"]."' tabindex='9' maxlength='20'></td></tr>
    <tr><td width='19%' bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>aim</b></font></td>
    <td width='81%' bgcolor='$table1'><input type='text' name='aim' size='20' value='".$_POST["aim"]."' tabindex='10' maxlength='24'></td></tr>
    <tr><td width='19%'><font size='$font_m' face='$font_face' color='$font_color_main'><b>msn</b></font></td>
    <td width='81%'><input type='text' name='msn' size='20' value='".$_POST["msn"]."' tabindex='11'></td></tr>
    <tr><td width='19%'  bgcolor='$table1'><font size='$font_m' face='$font_face' color='$font_color_main'><b>signature</b></font></td>
    <td width='81%'  bgcolor='$table1'><textarea rows='5' name='sig' cols='30' tabindex='12' maxlength='200'>".$_POST["sig"]."</textarea></td></tr>
    <tr><td width='100%' colspan='2'><input type='submit' value='Submit' name='B1'><input type='reset' value='Reset' name='B2'></td></tr>
    <input type='hidden' name='add' value='2'></form></table>$skin_tablefooter</center>";
}
echo "<p>If you have any problems, please seek support at <a href='http://forum.myupb.com/' target='_blank'>myupb.com's forums!</a></font></p>";
if(@$_POST["add"] >= 1) require_once('./includes/footer.php');
?>
