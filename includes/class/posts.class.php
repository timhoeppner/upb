<?php
// posts.class.php
// designed for Ultimate PHP Board
// Author: Jerroyd Moore, aka Rebles
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.4.1

if(basename($_SERVER['PHP_SELF']) == 'posts.class.php') die('This is a wrapper script!');
require_once('./includes/inc/post.inc.php');
require_once('./includes/class/tdb.class.php');
class posts extends tdb {
	//declare vars
	var $tRec;
	var $fRec;
	var $user = array();

	function posts($dir, $db) {
		$this->tdb($dir, $db);
	}

	//Check Functions
	function set_topic($tRec) {
		$this->tRec = $tRec;
	}

	function set_forum($fRec) {
		$this->fRec = $fRec;

	}

	function set_user_info($username, $password, $power, $id) {
		if($power == 0) {
			$username = "guest";
			$password = "password";
			$id = "0";
		}
		$this->user = array("username" => $username, "password" => $password, "power" => $power, "id" => $id);
	}

	//Development Purposes
	function varDump() {
		echo '<pre><b>$tRec:</b><br>';
		var_dump($this->tRec);
		echo '<br><br><b>$fRec:</b><br>';
		var_dump($this->fRec);
		echo '<br><br><b>\$user:</b><br>';
		var_dump($this->user);
		echo '</pre>';
	}

	function check_user_info() {
		if($this->user["username"] == "" || !isset($this->user["username"])) return false;
		if($this->user["password"] == "" || !isset($this->user["password"])) return false;
		if($this->user["power"] == "" || !isset($this->user["power"])) return false;
		if($this->user["id"] == "" || !isset($this->user["id"])) return false;
		return true;
	}

	function check_forum() {
		if($this->fRec[0]["id"] == "" || !isset($this->fRec[0]["id"])) return false;
		return true;
	}

	function check_topic() {
		if($this->tRec[0]["id"] == "" || !isset($this->tRec[0]["id"])) return false;
		//if($this->tRec[0]["p_ids"] == "") return false;
		return true;
	}
	// end check functions

	function d_topic($page_string) {
		if(!$this->check_user_info()) return false;
		echo "
	<br />
    <div id='tabstyle_pagenum'>
<span class='pagination_current'>Page:</span>".$page_string."
</div>

    <div style='clear:both;'></div>
    <div id='tabstyle_1'>
        <ul>";
		if((int)$this->user["power"] >= (int)$this->fRec[0]["reply"]) echo "<li><a href='newpost.php?id=".$this->fRec[0]["id"]."&t=1&t_id=' title='Create a new topic?'><span>Create New Topic</span></a></li>";
		echo "
        </ul>
    </div>
    <div style='clear:both;'></div>";
		return true;
	}

	function d_posting($page_string, $position = "top")
  {
    if(!$this->check_topic() || !$this->check_forum() || !$this->check_user_info()) return false;
    $output = "<br />
      <div id='tabstyle_pagenum'>
  <span class='pagination_current'>Page:</span>$page_string
   </div>
      <div style='clear:both;'></div>";
      if ($position == "top")
      {
      $output .= "<div id='tabstyle_1'>
         <ul>";

  		if((int)$this->user["power"] >= (int)$this->fRec[0]["post"]) $output .= "<li><a href='newpost.php?id=".$this->fRec[0]["id"]."&t=1&t_id=' title='Create a new topic?'><span>Create New Topic</span></a></li>";
   		if((int)$this->user["power"] >= (int)$this->fRec[0]["reply"]) {
  			if(!(bool)$this->tRec[0]["locked"]) $output .= "<li><a href='newpost.php?id=".$this->fRec[0]["id"]."&t=0&t_id=".$this->tRec[0]["id"]."&page=".$_GET["page"]."' title='Add a reply?'><span>Add Reply</span></a></li>";
  			else $output .= "<li><a href='#' title='Topic Is Locked'><span>Topic Is Locked</span></a></li>";
  		}
  		if((int)$this->user["power"] > 0) {
  		    $output .= "<li><a href='managetopic.php?action=watch&id=".$this->fRec[0]["id"]."&t_id=".$this->tRec[0]["id"]."&page=".$_GET["page"]."' title='Watch This Topic?'><span>Watch Topic</span></a></li>";
	        $output .= "<li><a href='managetopic.php?action=favorite&id=".$this->fRec[0]["id"]."&t_id=".$this->tRec[0]["id"]."&page=".$_GET["page"]."' title='Favorite this Topic?'><span>Bookmark Topic</span></a></li>";
  		}
    	if ((int)$_COOKIE["power_env"] >= 2) {
/*    		$output .= "
				<li><a href='delete.php?action=delete&t=1&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Delete topic</span></a></li>";
		    if ($tRec[0]["locked"] == "0") $output .= "
				<li><a href='managetopic.php?action=CloseTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Close topic?</span></a></li>";
		    else $output .= "
				<li><a href='managetopic.php?action=OpenTopic&id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Open topic?</span></a></li>";
*/
		    $output .= "
				<li><a href='managetopic.php?id=".$_GET["id"]."&t_id=".$_GET["t_id"]."'><span>Options</span></a></li>";
	    }
   	$output .= "
        </ul>
      </div>";
      }
     $output .= "<div style='clear:both;'></div>";
  		return $output;
     }

	function getPosts($fp, $start=0, $howmany=-1) {
		if(!$this->check(__LINE__) || !$this->check_topic()) return false;

		$header = array();
		$this->readHeader($fp, $header);

		$f = fopen($this->fp[$fp].'.ta', 'r');

		$p_ids = explode(",", $this->tRec[0]["p_ids"]);
		$return = array();
		$tmp = array();

		foreach($p_ids as $p_id) {
			if($start > 0) {
				$start--;
				continue;
			}
			if($howmany == 0) {
				break;
				continue;
			}
			if(FALSE === ($fileId = $this->fileIdById($fp, $p_id))) {
				echo "<b><font color='red'>ERROR</font></b>: Unable to find the p_id $p_id(\$p_ids = <br />";
				print_r($p_ids);
				echo ") <br />";
				continue;
		}
		if(FALSE === ($seekto = $this->bytesToSeek($fp, $header, $fileId))) {
			echo 'tdb::bytestoseek() failed in posts::getPosts()...';
			continue;
		}
		fseek($f, $seekto);
		$return[] = $this->parseRecord($fp, fread($f, $header["recLen"]), $header);
		$howmany--;
		}
		fclose($f);
		return $return;
	}
}
?>
