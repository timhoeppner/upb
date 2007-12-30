<?php
// Ultimate PHP Board
// Author: PHP Outburst
// Website: http://www.myupb.com
// Version: 2.0

// Ultimate PHP Board Functions

//php registered_global off
//prevent exploits for users who have registered globals on
foreach($GLOBALS["_GET"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_POST"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_COOKIE"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_SERVER"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_ENV"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_FILES"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}
foreach($GLOBALS["_REQUEST"] as $varname => $varvalue) {
    if(isset($$varname)) unset($$varname);
}

require_once("./includes/inc/date.inc.php");
require_once("./includes/inc/encode.inc.php");
require_once("./includes/inc/privmsg.inc.php");

function exitPage($text, $include_header=false, $include_footer=true, $footer_simple=false) { 
    require_once("./includes/class/config.class.php");
    $config_tdb = new configSettings();
    $_CONFIG = $config_tdb->getVars("config");
    $tdb = new functions(DB_DIR.'/', 'main.tdb');
    //$tdb->define_error_handler(array(&$errorHandler, 'add_error'));
    $tdb->setFp('users', 'members');
    
    if($include_header) require_once('./includes/header.php'); 
    echo "<br \>".$text; 
    if($footer_simple) $footer = "footer_simple.php"; 
    else $footer = "footer.php"; 
    if($include_footer) require_once('./includes/' . $footer);
    exit; 
} 

function redirect($where, $time) {
    echo "<meta http-equiv='refresh' content='$time;URL=$where'>";
    exit;
}

function checkAlphaNumeric($string) {
    if(function_exists("ctype_alnum")) return ctype_alnum($string);
    if(ereg("^[a-zA-Z0-9]", $string)) return true;
    return false;
}

function deleteWhiteIndex(&$array) {
    for($i=0;$i<count($array);$i++) {
        $array[$i] = trim($array[$i]);
        if($array[$i] == "") {
            unset($array[$i]);
        }
    }
}

function createUserPowerMisc($user_power, $list_format, $exclude_guests=false) {
    //$list_format choices:
    //$list_format = 1; ==> dropdown list of current Power and above
    //$list_format = 2; ==> text only of current power and above
    //$list_format = 3; ==> short text only of current power and above
    //$list_format = 4; ==> text only of current power only
    //$list_format = 5; ==> short text only of current power only
    //$list_format = 6; ==> short dropdown list of current Power and above
    //$list_format = 7; ==> dropdown list of current Power only
    //$list_format = 8; ==> short dropdown list of current Power only
    
    $dropdown = false;
    $allText = false;
    $allShortText = false;
    $oneText = false;
    $oneShortText = false;
    $shortDropDown = false;
    $oneDropDown = false;
    $oneShortDropDown = false;
    if($list_format == 1) $dropdown = true;
    elseif($list_format == 2) $allText = true;
    elseif($list_format == 3) $allShortText = true;
    elseif($list_format == 4) $oneText = true;
    elseif($list_format == 5) $oneShortText = true;
    elseif($list_format == 6) $shortDropDown = true;
    elseif($list_format == 7) $oneDropDown = true;
    elseif($list_format == 8) $oneShortDropDown = true;
    else { echo "Wrong Selection"; return false; }
    
    $list = "";
    if((bool)$exclude_guests === FALSE) {
        if(($user_power == 0 || $user_power == '')) {
            if($dropdown) $list .= '<option value="0" selected>Guests and above</option>';
            elseif($allText) return '<b>guests</b> and above';
            elseif($allShortText) return 'guest+';
            elseif($oneText) return 'Guest';
            elseif($oneShortText) return 'Guest';
            elseif($shortDropDown) $list .= '<option value="0" selected>Guests+</option>';
            elseif($oneDropDown) $list .= '<option value="0" selected>Guests</option>';
            elseif($oneShortDropDown) $list .= '<option value="0" selected>Guest</option>';
        } else {
            if($dropdown) $list .= '<option value="0">Guests and above</option>';
            elseif($shortDropDown) $list .= '<option value="0">Guests+</option>';
            elseif($oneDropDown) $list .= '<option value="0">Guests</option>';
            elseif($oneShortDropDown) $list .= '<option value="0">Guest</option>';
        }
    }
    if($user_power == 1) {
        if($dropdown) $list .= '<option value="1" selected>Members and above</option>';
        elseif($allText) return '<b>members</b> and above';
        elseif($allShortText) return 'members+';
        elseif($oneText) return 'Member';
        elseif($oneShortText) return 'Member';
        elseif($shortDropDown) $list .= '<option value="1" selected>Members+</option>';
        elseif($oneDropDown) $list .= '<option value="1" selected>Members</option>';
        elseif($oneShortDropDown) $list .= '<option value="1" selected>Member</option>';
    } else {
        if($dropdown) $list .= '<option value="1">Members and above</option>';
        elseif($shortDropDown) $list .= '<option value="1">Members+</option>';
        elseif($oneDropDown) $list .= '<option value="1">Member</option>';
        elseif($oneShortDropDown) $list .= '<option value="1">Member</option>';
    }
    if($user_power == 2) {
        if($dropdown) $list .= '<option value="2" selected>Moderators and Administrators</option>';
        elseif($allText) return '<b>mods & admins</b>';
        elseif($allShortText) return 'mods+';
        elseif($oneText) return 'Moderator';
        elseif($oneShortText) return 'Mod';
        elseif($shortDropDown) $list .= '<option value="2" selected>Mods+</option>';
        elseif($oneDropDown) $list .= '<option value="2" selected>Moderator</option>';
        elseif($oneShortDropDown) $list .= '<option value="2" selected>Mod</option>';
    } else {
        if($dropdown) $list .= '<option value="2">Moderators and Administrators</option>';
        elseif($shortDropDown) $list .= '<option value="2">Mods+</option>';
        elseif($oneDropDown) $list .= '<option value="2">Moderator</option>';
        elseif($oneShortDropDown) $list .= '<option value="2">Mod</option>';
    }
    if($user_power == 3) {
        if($dropdown) $list .= '<option value="3" selected>Administrators only</option>';
        elseif($allText) return '<b>admins</b> only';
        elseif($allShortText) return 'admins';
        elseif($oneText) return 'Administrator';
        elseif($oneShortText) return 'Admin';
        elseif($shortDropDown) $list .= '<option value="3" selected>Admins</option>';
        elseif($oneDropDown) $list .= '<option value="3" selected>Administrator</option>';
        elseif($oneShortDropDown) $list .= '<option value="3" selected>Admin</option>';
    } else {
        if($dropdown) $list .= '<option value="3">Administrators only</option>';
        elseif($shortDropDown) $list .= '<option value="3">Admins</option>';
        elseif($oneDropDown) $list .= '<option value="3">Administrator</option>';
        elseif($oneShortDropDown) $list .= '<option value="3">Admin</option>';
    }
    
    if($list != '') return $list;
    else { echo 'Error in createUserPowerMisc(): User\'s power unidentifiable ('.$user_power.')'; return false; }
}

function ok_cancel($action, $text) {
    //global $font_m, $font_face, $font_color_main;

    echo "<form action='$action' METHOD=POST>
    <font size='$font_m' face='$font_face' color='$font_color_main'>
    $text
    <input type=submit name='verify' value='Ok'> <input type=submit name='verify' value='Cancel'>
    </font>
    </form>";
}

//ADDED $qr parameter to check for quick reply so $pageStr can be configured correctly
function createPageNumbers($current_page, $total_number_of_pages, $url_string='',$qr = false) {
    global $font_face,$font_s;
    if($current_page == '') $current_page = '1';
    $num_pages = (int) $total_number_of_pages;
    $url_string = str_replace('page='.$_GET['page'], '', $url_string);
    if($url_string != '') $url_string = '?'.$url_string.'&';
    else $url_string = '?';
    $url_string = str_replace('&&', '&', $url_string);
        
    if($num_pages == 1) $pageStr = "<font face='$font_face' size='$font_s'><span class='pagenumstatic'>$num_pages</span></font>";
    else {
        //$pageStr = "<font face='$font_face' size='$font_s'><span class=pagenumstatic>";
        for($i=1;$i<=$num_pages;$i++) {
            if($current_page == $i) 
              $pageStr .= $i."</span> ";
            else 
            {
              $pageStr .= "<font face='$font_face' size='$font_s'><span class='pagenum'><a href='";
              if ($qr === false)
                $pageStr .= basename($_SERVER['PHP_SELF']);
              else
                $pageStr .= "viewtopic.php";
              $pageStr .= $url_string."page=".$i."'>".$i."</a></span> ";
            }
        }
        //$pageStr .= "</font></span>";
    }
    return $pageStr;
}

function addIdRefToArray(&$ArrRec, $field) {
    $ArrRec[$field] = array();
    for($i=0;$i<count($ArrRec);$i++) {
        $ArrRec[$field][$i] = $ArrRec[$i][$field];
    }
    return $ArrRec;
}

function generateUniqueKey() {
    return md5(uniqid(rand(), true));
/*    $key = '';
    for($i=0;$i<11;$i++) {
        $key .= chr(rand(33,126));
    }
    return $key; */
}

function getlastvisit($id)
{
  $file = file(DB_DIR."/lastvisit.dat");
 
  if ($file[0] != "")
  {
    foreach ($file as $value)
    {
      list($userid,$timestamp) = explode("|",trim($value));
      $lines[$userid] = $timestamp;
    }
  }
  
  if (array_key_exists($id,$lines))
    $lv = $lines[$id];
  return $lv;
}

function lastvisit($id='')
{ 
  if ($id == '')
    $id = $_COOKIE['id_env'];
  
  $now = mkdate();
  $lv = $string = '';
  $lines = array();
  $file = file(DB_DIR."/lastvisit.dat");
 
  //var_dump($file);
 
  if ($file[0] != "")
  {
    foreach ($file as $value)
    {
      list($userid,$timestamp) = explode("|",trim($value));
      $lines[$userid] = $timestamp;
    }
  }
  
  if (array_key_exists($id,$lines))
    $lv = $lines[$id];
  
  $lines[$id] = $now;
  
  ksort($lines);
  
  //var_dump($lines);
  
  foreach ($lines as $key => $value)
  {
    $string .= $key."|".$value."\n"; 
  }
  
  $f = fopen(DB_DIR.'/lastvisit.dat', 'w');
  fwrite($f, $string);
  fclose($f);
  return $lv;
}

function strstr_after($haystack, $needle, $case_insensitive = false) {
    $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
    $pos = $strpos($haystack, $needle);
    if (is_int($pos)) {
        return substr($haystack, $pos + strlen($needle));
    }
    // Most likely false or null
    return $pos;
}

function strmstr($haystack, $needle, $before_needle=FALSE) {
 //Find position of $needle or abort
 if(($pos=strpos($haystack,$needle))===FALSE) return FALSE;

 if($before_needle) return substr($haystack,0,($pos-1)+strlen($needle));
 else return substr($haystack,$pos);
}
?>
