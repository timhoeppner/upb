<?php
// mod_avatar.class.php
// designed for Ultimate PHP Board
// Author: Jerroyd Moore, aka Rebles
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.4.1
function md5_file2($file) {
	$return = md5(file_get_contents($file));
	return $return;
    //version_compare not working correctly... PHP VERSION: 4.4.2-x86_64
	//if(version_compare(PHP_VERSION, "5.1.0", ">=")) $recent = md5(file_get_contents($file));
	//else $recent = md5_file($file);
}

if(!class_exists('tdb')) die('you must include the TextDB class file before including the mod_avatar class file');
class mod_avatar extends tdb {
	var $_userHeader = array();
	var $offset = array();
	var $length = array();
	var $_CONFIG;
	function mod_avatar($dir, $db, $_CONFIG) {
		if($this->tdb($dir, $db) === FALSE) {
			return false;
		}
		$this->_CONFIG = $_CONFIG;
		return true;
	}

	function all_users() {
		if(!isset($this->fp['users']) || $this->fp['users'] != DB_DIR.'/main_members') $this->setFp('users', 'members');

		if(FALSE === $this->readHeader('users', $this->_userHeader)) {
			$this->sendError(E_ERROR, 'Unable to retrieve the header information');
			return false;
		}

		//prevent corrupted tdb from corrupting more
		if(FALSE === is_int(((filesize($this->fp['users'].'.ta') - $this->_userHeader['recPos']) / $this->_userHeader['recLen']))) {
			$this->sendError(E_ERROR, 'Unable to parse '.$this->fp['users'].' Correctly');
			return false;
		}
		clearstatcache();
		$f = fopen($this->fp['users'].'.ta', 'r+');
		fseek($f, $this->_userHeader['recPos']);
		for($i=0,$max=$this->getNumberOfRecords('users');$i < $max;$i++) {
			foreach($this->_userHeader as $field) {
				if($field['fName'] == 'avatar_hash') {
					fwrite($f, str_repeat(' ', $field['fLength']));
				} else {
					fseek($f, $field['fLength'], SEEK_CUR);
				}
			}
		}
		fclose($f);
		return true;
	}

	function verify_avatar($file, $hash) {
		$recent = md5_file2($file);
		return ($recent == $hash);
	}

	function new_parameters($avatar, $max_width, $max_height) {
		$return = array('avatar' => $avatar, 'avatar_width' => '', 'avatar_height' => '', 'avatar_hash' => '');
		//if(!file_exists($avatar)) return array(/* 'avatar' => '', */ 'avatar_hash' => '', 'avatar_height' => '', 'avatar_width' => '');

		if($avatar == '') {
			$return['avatar_width'] = '';
			$return['avatar_height'] = '';
		} else {
			$return['avatar_width'] = $max_width;
			$return['avatar_height'] = $max_height;

			if (@fclose(@fopen($avatar, 'r')))  {
				list($width, $height, $type, $attr) = getimagesize($avatar);
				if($width <= $max_width && $height <= $max_height) {
					$return['avatar_width'] = $width;
					$return['avatar_height'] = $height;
				} elseif($width > $height) {
					$return['avatar_height'] = round(($max_width * $height) / $width);
				} elseif($width < $height) {
					$return['avatar_width'] = round(($max_height * $width) / $height);
				}
			}
		}
		
		$return['avatar_hash'] = md5_file2($avatar);
		return $return;
	}
}
?>