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
    
    function d_delTopic() {
        if(!$this->check_user_info() || $this->user["power"] < 2) return false;
        echo "<font><a href='admin.php?action=del_t&id=".$this->fRec[0]["id"]."&t_id=".$this->tRec[0]["id"]."'>Delete Topic</a></font>";
        return true;
    }
    
    function d_topic($page_string) {
        if(!$this->check_user_info()) return false;
        echo "<table border='0' cellspacing='0' cellpadding='4' width='".TABLE_WIDTH_MAIN."' align='center'><tr>
        <td><font>".$page_string."</font></td>
        <td align='right'><p align=right>";
        if((int)$this->user["power"] >= (int)$this->fRec[0]["reply"]) echo "<a href='newpost.php?id=".$this->fRec[0]["id"]."&t=1&t_id='><img src='".SKIN_DIR."/icons/topic.gif' border='0'></a>";
        echo "</p></td></tr></table>";
        return true;
    }

    function d_posting($page_string) {
        if(!$this->check_topic() || !$this->check_forum() || !$this->check_user_info()) return false;
        
        echo "<table border='0' cellspacing='0' cellpadding='4' width='".TABLE_WIDTH_MAIN."' align='center'>
        <tr><td><font>$page_string</font></td>
        <td align='right'><p align=right>";
        if((int)$this->user["power"] > 0) echo "<a href='managetopic.php?action=watch&id=".$this->fRec[0]["id"]."&t_id=".$this->tRec[0]["id"]."&page=".$_GET["page"]."'><img src='".SKIN_DIR."/icons/monitor.gif' border='0'></a>";
        if((int)$this->user["power"] >= (int)$this->fRec[0]["post"]) echo "<a href='newpost.php?id=".$this->fRec[0]["id"]."&t=1&t_id='><img src='".SKIN_DIR."/icons/topic.gif' border='0'></a>";
        else echo"&nbsp;";
        if((int)$this->user["power"] >= (int)$this->fRec[0]["reply"]) {
            if(!(bool)$this->tRec[0]["locked"]) echo "<a href='newpost.php?id=".$this->fRec[0]["id"]."&t=0&t_id=".$this->tRec[0]["id"]."&page=$page'><img src='".SKIN_DIR."/icons/reply.gif' border='0'></a>";
            else echo "&nbsp;<img src='".SKIN_DIR."/icons/replylocked.gif' border='0'>";
        } else echo"&nbsp;";
        echo "</font></td></tr></table>";
        return true;
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