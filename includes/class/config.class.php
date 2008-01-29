<?php
// Configuration Class
// Author: J. Moore aka Rebles
// Website: http://www.myupb.com
// Version: 2.0
// Using textdb Version: 4.x

/* Notes
    $tdb->createTable("config", array(
      array("name", "memo"),
      array("value", "memo"),
      array("type", "string", 6),
      array("id", "id"),
      array("title", "memo"),
      array("description", "memo"),
      array("form_object", "string", 8),
      //form_name = "name"
        //for link "name" = text
      //form_value = "value"
        //for link "value" = URL
      //size predetermined (40)
      //rows predetermined (10)
      //cols predetermined (30)
      array("data_type", "string", 7)
      //Determines what kind of data is acceptable in the object.  Options: "number", "text" aka "string", "_blank", "_parent"
      //"data_type" is the target of links.
    ));
*//*

 - Title
 - Description
 - Form Object Type
   - input
     - text
       - name
       - size
       - INI val
     - password
       - name
       - size
     - hidden
       - name
       - INI val
     - checkbox
       - name
       - INI switch
         - FALSE === unchecked
         - TRUE === checked
   - textarea
     - rows
     - cols
     - INI VAL
     - name
   - link
     - URL
     - text
    - target
*/

class configSettings extends tdb {

    var $_cache = array();  //cache the vars
    var $_cache_ext = array();

    function configSettings() {
        $this->tdb(DB_DIR, "main.tdb");
        $this->setFp("config", "config");
        $this->setFp("ext_config", "ext_config");
    }

    function clearcache() {
    	$this->_cache = array();
    	$this->_cache_ext = array();
    }

    function getVars($type, $returnOptionalData=false) {
        $return = array();
        if($returnOptionalData) {
        	if(isset($this->_cache_ext[$type])) return $this->_cache_ext[$type];
        	$this->_cache_ext[$type] = $this->query("ext_config", "type='".$type."'");
        	return $this->_cache_ext[$type];
        }
        if(isset($this->_cache[$type])) return $this->_cache[$type];
        $rawVars = $this->query("config", "type='".$type."'");
        //print_r($rawVars);
        foreach($rawVars as $rawVar) {
            $return[$rawVar["name"]] = $rawVar["value"];
        }
        $this->_cache[$type] = $return;
        return $return;
    }

    //format for $varArr is array('var_name' => 'var_value', ...)
    //if($editOptionalData) format is how it is stored in the tdb, array(array("name" => $name, ...)...)
    function editVars($type, $varArr, $editOptionalData=false) {
        if(!is_array($varArr)) {
            echo "<b>Warning:</b> second argument of editVars(), must be an array.  (type: ".$type.")";
            return false;
        }
        $oriVars = $this->getVars($type, true);
        if($editOptionalData) {
            $nameRef = array();
            for($i=0;$i<count($varArr);$i++) {
               $nameRef[$varArr[$i]["name"]] = $varArr[$i];
            }
        }
        foreach($oriVars as $oriVar) {
            if($oriVar["form_object"] == "textarea") {
                if($editOptionalData) $nameRef[$oriVar["name"]]["value"] = stripslashes($nameRef[$oriVar["name"]]["value"]);
                else $varArr[$oriVar["name"]] = stripslashes($varArr[$oriVar["name"]]);
            } elseif($oriVar["form_object"] == "checkbox") {
                if($editOptionalData && $nameRef[$oriVar["name"]] != "1") $nameRef[$oriVar["name"]]["value"] = 0;
                elseif($varArr[$oriVar["name"]] != "1") $varArr[$oriVar["name"]] = "0";
                /*if($nameRef[$oriVar["name"]] != "1") {
                    if($editOptionalData)
                    else $varArr[$oriVar["name"]] = 0;
                } */
            }
            elseif ($oriVar["form_object"] == "list" and $varArr['type'] != "addcat" and $varArr['type'] != "delcat")
            {
              $varArr[$oriVar["name"]] = $this->sortCats($varArr['neworder']);
              $output = $varArr[$oriVar["name"]];
            }
            
            if($editOptionalData) {
                if(is_array($nameRef[$oriVar["name"]])) {
                    $this->edit("ext_config", $oriVar["id"], array_diff_assoc($nameRef[$oriVar["name"]], $oriVar), false);
                    if($nameRef[$oriVar["name"]]["value"] != $oriVar["value"]) $this->edit("config", $oriVar["id"], $nameRef[$oriVar["name"]], false);
                }
            } else {
                //if($varArr[$oriVar["name"]] != "" && $varArr[$oriVar["name"]] != $oriVar["value"]) {
                if($varArr[$oriVar["name"]] != $oriVar["value"]) { // Allow entries to be blank, otherwise how set blank announcement?
//echo "Changing Value of ".$oriVar["name"]." from \"<i>".htmlentities($oriVar["value"])."</i>\" to \"<i>".htmlentities($varArr[$oriVar["name"]])."</i>\"<br>";
                    $this->edit("config", $oriVar["id"], array("value" => $varArr[$oriVar["name"]]), false);
                    $this->edit("ext_config", $oriVar["id"], array("value" => $varArr[$oriVar["name"]]), false);
                }
            }
        }
        //$this->defragMemo("ext_config");
        //$this->defragMemo("config");
        return true;
        //return $output;
    }
    
    //changes the sort list into comma delimited string for entry into the database
    function sortCats($orderlist)
    {
      //use $array['neworder']
       //var_dump($array);
       $newlist = explode("&list",$orderlist);
      array_shift($newlist);
       $u_sort = "";
       foreach ($newlist as $key => $value)
       {
        list($id,$title) = explode("=",$value);
         list($catid,$name) = explode("::",$title);
         $u_sort .= $catid;
         if ($key < count($newlist)-1)
           $u_sort .= ",";
       }
       return $u_sort;
     }
}
?>
