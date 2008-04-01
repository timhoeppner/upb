<?php
// Ultimate PHP Board
// Author: PHP Outburst
// Website: http://www.myupb.com
// Version: 2.0

// Ultimate PHP Board Functions

function exitPage($text, $include_header=false, $include_footer=true, $footer_simple=false) {
    $_CONFIG = &$GLOBALS['_CONFIG'];;
    $tdb = &$GLOBALS['tdb'];
    //$tdb->define_error_handler(array(&$errorHandler, 'add_error'));

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

function deleteWhiteIndex(&$array) {
    for($i=0;$i<count($array);$i++) {
        $array[$i] = trim($array[$i]);
        if($array[$i] == "") {
            unset($array[$i]);
        }
    }
}

//UnTested!!
function array_reset_keys(&$array) {
    $keys = array_keys($array);
    sort($keys, SORT_NUMERIC);
    $i = 0;
    foreach($keys as $key) {
        if(!ctype_digit($key)) continue;
        if($key != $i) {
            $array[$i] =& $array[$key];
            unset($array[$key]);
        }
        $i++;
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
    if($user_power >= 3) {
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
    echo "
<form action='$action' METHOD=POST>
<div class='alert'><div class='alert_text'>
<strong>$text</strong></div><div style='padding:4px;'><input type=submit name='verify' value='Ok'> <input type=submit name='verify' value='Cancel'>
</div></div>
</form>";
}

function createPageNumbers($current_page, $total_number_of_pages, $url_string='') {
    if($current_page == '') $current_page = '1';
    $num_pages = (int) $total_number_of_pages;
    $url_string = str_replace('page='.$current_page, '', $url_string);
    if($url_string != '') $url_string = '?'.$url_string.'&';
    else $url_string = '?';
    $url_string = str_replace('&&', '&', $url_string);

    $pageStr = '';
    if($num_pages == 1) $pageStr = "<span class='pagination_current'>$num_pages</span>";
    else {
        //$pageStr = "<font face='$font_face' size='$font_s'><span class=pagenumstatic>";
        for($i=1;$i<=$num_pages;$i++) {
            if($current_page == $i) $pageStr .= "<span class='pagination_current'>".$i."</span>";
            else $pageStr .= "<span class='pagination_link'><a href='".basename($_SERVER['PHP_SELF']).$url_string."page=".$i."'>".$i."</a></span> ";
        }
        //$pageStr .= "</font></span>";
    }
    return $pageStr;
}

function generateUniqueKey() {
    return md5(uniqid(rand(), true));
/*    $key = '';
    for($i=0;$i<11;$i++) {
        $key .= chr(rand(33,126));
    }
    return $key; */
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

function directory($dir,$filters="all") {
    $files = $filtered =array();
    if(FALSE !== ($handle = @opendir($dir))) {
        while(($file = readdir($handle))!==false)   {
            if ($file != "." and $file != "..") $files[] = $file;
        }
        closedir($handle);
    }

    if ($filters == "all") $filtered = $files;
    else {
		$filters=explode(",",$filters);
		if(!empty($files)) {
    		foreach ($files as $file) {
    			for ($f=0;$f<sizeof($filters);$f++) {
                    $system=explode(".",$file);
    				if (count($system) > 1) {
                        if (strtolower($system[1]) == $filters[$f])
                            $filtered[] = $file;
                    }
                }
    		}
		}
	}

	return $filtered;
}

function strmstr($haystack, $needle, $before_needle=FALSE) {
 //Find position of $needle or abort
 if(($pos=strpos($haystack,$needle))===FALSE) return FALSE;

 if($before_needle) return substr($haystack,0,($pos-1)+strlen($needle));
 else return substr($haystack,$pos);
 }

//for debugging
function dump($array)
{
  echo "<pre>";
  var_dump($array);
  echo "</pre>";
}

function echoTableHeading($display, $_CONFIG) {
		//set $display to 85
		echo "
	<div class='main_cat_wrapper'>
		<div class='cat_area_1'>".$display."</div>
		<table class='main_table' cellspacing='1'>
		<tbody>";
	}

//$skin_tablefooter = "</tbody></table><div class='footer'><img src='".$skin_dir."/images/spacer.gif' alt='' title='' /></div></div><br />";

function echoTableFooter($skin_dir)
{
echo "
		</tbody>
		</table>
		<div class='footer'><img src='".$skin_dir."/images/spacer.gif' alt='' title='' /></div>
	</div>
	<br />";
}

function timezonelist($current=0)
{
  $timezones = array();
  $timezones["-12"] = "(GMT -12:00) Eniwetok, Kwajalein";
  $timezones["-11"] = "(GMT -11:00) Midway Island, Samoa";
  $timezones["-10"] = "(GMT -10:00) Hawaii";
  $timezones["-9"] = "(GMT -9:00) Alaska";
  $timezones["-8"] = "(GMT -8:00) Pacific Time (US &amp; Canada)";
  $timezones["-7"] = "(GMT -7:00) Mountain Time (US &amp; Canada)";
  $timezones["-6"] = "(GMT -6:00) Central Time (US &amp; Canada), Mexico City";
  $timezones["-5"] = "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima";
  $timezones["-4"] = "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz";
  $timezones["-3"] = "(GMT -3:00) Brazil, Buenos Aires, Georgetown";
  $timezones["-2"] = "(GMT -2:00) Mid-Atlantic";
  $timezones["-1"] = "(GMT -1:00 hour) Azores, Cape Verde Islands";
  $timezones["0"] = "(GMT) Western Europe Time, London, Lisbon, Casablanca";
  $timezones["1"] = "(GMT +1:00) Brussels, Copenhagen, Madrid, Paris, Rome";
  $timezones["2"] = "(GMT +2:00) Kaliningrad, South Africa";
  $timezones["3"] = "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg";
  $timezones["3.5"] = "(GMT +3:30) Tehran";
  $timezones["4"] = "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi";
  $timezones["4.5"] = "(GMT +4:30) Kabul";
  $timezones["5"] = "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent";
  $timezones["5.5"] = "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi";
  $timezones["6"] = "(GMT +6:00) Almaty, Dhaka, Colombo";
  $timezones["7"] = "(GMT +7:00) Bangkok, Hanoi, Jakarta";
  $timezones["8"] = "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong";
  $timezones["9"] = "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk";
  $timezones["9.5"] = "(GMT +9:30) Adelaide, Darwin";
  $timezones["10"] = "(GMT +10:00) Eastern Australia, Guam, Vladivostok";
  $timezones["11"] = "(GMT +11:00) Magadan, Solomon Islands, New Caledonia";
  $timezones["12"] = "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka";

  $output = "\n<select name='u_timezone' id='u_timezone'>\n";
  $set = (float) $current; //convert to a float for comparison with keys
  foreach ($timezones as $key => $places)
  {
    $diff = (float) $key; //set type to float to convert some array keys which are strings.
    $output .= "<option value='".(float)$diff."'";
    if ($set == $diff)
      $output .= " selected='selected'";
    $output .= ">$places</option>\n";
  }
  $output .= "</select>\n";
  return $output;
}

//replaces characters in strings to make xml compatible
function xml_clean($string)
{
  $original = array("&","\"","\'",">","<");
  $replace = array("&amp;","&quot;","&apos;","&gt;","&lt;");
  $new = str_replace($original,$replace,$string);
  return $new;
}

	function returnimages($dirname = "images/avatars/") {
			$pattern = "\.(jpg|jpeg|png|gif|bmp)$";
			$files = array();
			$curimage = 0;
			if ($handle = opendir($dirname)) {
				while (false !== ($file = readdir($handle))) {
					if (eregi($pattern, $file)) {
						echo "<option value ='images/avatars/".$file."'>".$file."</option>";
						$curimage++;
					}
				}
				closedir($handle);
			}
			return($files);
		}
?>
