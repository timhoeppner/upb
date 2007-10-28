<?php
// Ultimate PHP Board
// Author: PHP Outburst
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.3.2

if(basename($_SERVER['PHP_SELF']) == 'func.class.php') die('This is a wrapper script!');
require_once("./includes/class/error.class.php");
require_once("./config.php");
require_once("./includes/inc/func.inc.php");
require_once("./includes/class/tdb.class.php");
require_once("./includes/class/config.class.php");
require_once("./includes/class/mod_avatar.class.php");
require_once('./includes/inc/post.inc.php');
//whos_online.php included at last line

//UPB's main Vars
$config_tdb = new configSettings();
$_CONFIG = $config_tdb->getVars("config");
$_REGISTER = $config_tdb->getVars("regist");
$_REGIST = $_REGISTER;
$_STATUS = $config_tdb->getVars("status");

//integrate into admin_config
$_CONFIG["where_sep"] = "<b>&gt;</b>";
$_CONFIG["table_sep"] = "<b>::</b>";

$config_tdb->setFp("config", "config");
$config_tdb->setFp("ext_config", "ext_config");

eval(file_get_contents(DB_DIR.'/constants.php'));

class functions extends tdb {
    var $_cache = array();
    var $pulseInterval;

    function functions($dir, $db) {
        $this->cleanup();
        $this->tdb($dir, $db);
        
        // Set the pulse interval 	 
	         //$this->pulseInterval = 60 * 60 * 24 * 7; // 1 week 	 
	         $this->pulseInterval = 60 * 60 * 24; // 1 day 	 
	  	 
	         $this->pulse();
    }

    //$requireArray is case INsensitive
    function getNextRec($fp, $id, $requireArray='') {
        if(!is_array($requireArray)) return $this->get($fp, ++$id);
        while(FALSE !== ($rec = $this->get($fp, ++$id))) {
            $return = TRUE;
            reset($requireArray);
            while (list ($field, $value) = each ($requireArray)) {
                if(strtolower($rec[0][$field]) != strtolower($value)) {
                    $return = false;
                    break 1;
                }
            }
            if($return) return $rec;
        }
        return false;
    }

    //$requireArray is case INsensitive
    function getLastRec($fp, $id, $requireArray='') {
        if(!is_array($requireArray)) return $this->get($fp, --$id);
        while(FALSE !== ($rec = $this->get($fp, --$id))) {
            $return = TRUE;
            reset($requireArray);
            while (list ($field, $value) = each ($requireArray)) {
                if(strtolower($rec[0][$field]) != strtolower($value)) {
                    $return = false;
                    break 1;
                }
            }
            if($return) return $rec;
        }
        return false;
    }

    function login_user($user, $pass, $key) {
        if($this->fp['users'] != 'members') $this->setFp("users", "members");
        $rec = $this->query("users", "user_name='".$user."'", 1, 1);
        if($rec[0]["user_name"] != $user) return false;
        if($rec[0]["password"]{0} != chr(21)) {
            if($rec[0]["password"] == generateHash($pass, $rec[0]["password"])) return $rec[0];
        } elseif(substr($rec[0]["password"], 1) == stripslashes(t_encrypt(substr($pass, 0, (HASH_LENGTH - 1)), $key))) {
            $rec[0]["password"] = generateHash($pass);
            $this->edit("users", $rec[0]["id"], array("password" => $rec[0]["password"]));
            return $rec[0];
        }
        return false;
    }

    function is_logged_in() {
        if($_COOKIE["user_env"] == "" || $_COOKIE["uniquekey_env"] == "" || $_COOKIE["power_env"] == "" || $_COOKIE["id_env"] == "") return false;
        if(!empty($this->_cache["is_logged_in"][$_COOKIE["id_env"]])) {
            if($_COOKIE["user_env"] == $this->_cache["is_logged_in"][$_COOKIE["id_env"]]["user"]
            && $_COOKIE["uniquekey_env"] == $this->_cache["is_logged_in"][$_COOKIE["id_env"]]["uniquekey"]
            && $_COOKIE["power_env"] == $this->_cache["is_logged_in"][$_COOKIE["id_env"]]["power"])
            return true;
        }
        if($this->fp['users'] != 'members') $this->setFp("users", "members");
        $rec = $this->get("users", $_COOKIE["id_env"]);
/*        if(strlen($_COOKIE["password_env"]) != HASH_LENGTH && basename($_SERVER['PHP_SELF']) != "login.php") {
            redirect("logoff.php?ref=login.php", 0);
            exit;
        } */
        if($_COOKIE["user_env"] == $rec[0]["user_name"] && $_COOKIE["uniquekey_env"] == $rec[0]["uniquekey"] && $_COOKIE["power_env"] == $rec[0]["level"]) {
            $this->_cache["is_logged_in"][$_COOKIE["id_env"]] = array(
            "user" => $_COOKIE["user_env"],
            "uniquekey" => $_COOKIE["uniquekey"],
            "power" => $_COOKIE["power_env"]);
            return true;
        }
        return false;
    }

    function getID($fp) {
        $header = array();
        $this->readHeader($fp, $header);
        return $header["curId"];
    }
    
    /** 	 
	      * Sends a pulse to let myupb get some statistical data 	 
	      * 	 
	      */ 	 
	     function pulse() { 	 
	         // If the pulse file doesn't exist create it 	 
	         if(!file_exists(DB_DIR."/pulse.dat")) { 	 
	             $f = fopen(DB_DIR."/pulse.dat", "w"); 	 
	             fwrite($f, "0"); 	 
	             fclose($f); 	 
	  	 
	             clearstatcache(); 	 
	         } 	 
	  	 
	         // Open up the pulse file and find out when the last pulse was sent 	 
	         $f = fopen(DB_DIR."/pulse.dat", "r+"); 	 
	  	 
	         flock($f, LOCK_SH); 	 
	  	 
	         // Retrieve the last pulse time 	 
	         $lastPulse = (float) fread($f, 256); 	 
	  	 
	         if(($lastPulse + $this->pulseInterval) < fcn::gmtime()) { 	 
	             // Need to send out another pulse 	 
	             $url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]; 	 
	             $ver = UPB_VERSION; 	 
	  	 
	             // Get the total number of posts 	 
	             // TODO only grab the required fields, need the TDB update 	 
	             $fList = $this->tdb_listRec("forums", 1); 	 
	             $post_count = 0; 	 
	  	 
	             foreach($fList as $forum) { 	 
	                 $post_count += (int) $forum["posts"]; 	 
	             } 	 
	  	 
	             // Cleanup 	 
	             $fList = null; 	 
	  	 
	             $r = @fopen("http://www.myupb.com/UPBpulse.php?url={$url}&ver={$ver}&post_count={$post_count}", "r"); 	 
	             //$response = @fread($r, 10000); 	 
	             //echo $response; 	 
	             @fclose($r); 	 
	  	 
	             $lastPulse = fcn::gmtime(); 	 
	  	 
	             flock($f, LOCK_EX); 	 
	  	 
	             ftruncate($f, 0); 	 
	             rewind($f); 	 
	             fwrite($f, $lastPulse); 	 
	         } 	 
	  	 
	         flock($f, LOCK_UN); 	 
	  	 
	         fclose($f); 	 
	     }
}



//installation precausion
//globalize resource $tdb to prevent multiple occurances
if(file_exists(DB_DIR."/main.tdb")) {
    $tdb = new functions(DB_DIR.'/', 'main.tdb');
    //$tdb->define_error_handler(array(&$errorHandler, 'add_error'));
    $tdb->setFp('users', 'members');
    $tdb->setFp('forums', 'forums');
    $tdb->setFp('cats', 'categories');
    $tdb->setFp('getpass', 'getpass');
    require_once('./includes/whos_online.php');
}
?>
