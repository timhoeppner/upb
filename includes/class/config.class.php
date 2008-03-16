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
      //Determines what kind of data is acceptable in the object.  Options: "number", "text" aka "string", "_blank", "_parent", bool, or boolean
      //"data_type" is the target of links.
      //"data_type" is used to populate lists (serialized array($value => $text), empty values are NOT supported, if you wish to create an optgroup, $value should be set to "optgroup" followed by a number (ex: "optgroup1" => "categories").
                    Dynamically populated lists are NOT supported yet
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

    function editVars($type, $varArr, $editOptionalData=false) {
        //format for $varArr is array('var_name' => 'var_value', ...)
        //if($editOptionalData) format is how it is stored in the tdb, array(array("name" => $name, ...)...)
        if(!is_array($varArr)) {
            echo "<b>Warning:</b> second argument of editVars(), must be an array.  (type: ".$type.")";
            return false;
        }
        $oriVars = $this->getVars($type, true);
        if($editOptionalData) {
            $nameRef = array();
            for($i=0;$i<count($varArr);$i++) {
               $nameRef[$varArr[$i]["name"]] = $varArr[$i];  //element "value" is already in $varArr[$i]$varArr
            }
        }
        foreach($oriVars as $oriVar) {
            switch ($oriVar['data_type']) {
                case 'number':
                    if($editOptionalData) {  //$field = eregi_replace("[^0-9.-]", "", $field);
                        $nameRef[$oriVar["name"]]["value"] = eregi_replace("[^0-9.-]", "", $nameRef[$oriVar["name"]]["value"]);
                    } else $varArr[$oriVar["name"]] = eregi_replace("[^0-9.-]", "", $varArr[$oriVar["name"]]);
                    break;
                case 'bool':
                case 'boolean':
                    if($editOptionalData) {
                        $nameRef[$oriVar["name"]]["value"] = (($nameRef[$oriVar["name"]]["value"] != false) ? '1' : '0');
                    } else $varArr[$oriVar["name"]] = (($varArr[$oriVar["name"]] != false) ? '1' : '0');
                    break;
                case 'text':
                case 'string':
                default:
                    if($editOptionalData) $nameRef[$oriVar["name"]]["value"] = stripslashes($nameRef[$oriVar["name"]]["value"]);
                    else $varArr[$oriVar["name"]] = stripslashes($varArr[$oriVar["name"]]);
                    break;
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

    function delete($varName) {
        $query = $config_tdb->query('config', "name='$varName'", 1, 1);
        if(!empty($query[0])) {
            parent::delete('config', $query[0]['id']);
            return parent::delete('ext_config', $query[0]['id']);
        }
        return false;
    }

    function add($varName, $initialValue, $type, $dataOjbect, $formObject,  $category, $sort, $pageTitle, $pageDescription) {
        // Add checks here
        $query = $this->query('config', "name='$varName'", 1, 1);
        if(!empty($query[0])) return false;
        $query = $this->query('ext_config', "minicat='$category'&&sort>'$sort'");
        foreach($query as $r) {
            if(empty($r)) continue;
            $this->edit('ext_config', $r['id'], array('sort' => ($r['sort']+1)));
        }
        parent::add("ext_config", array("name" => $varName, "value" => $initialValue, "type" => $type, "title" => $pageTitle, "description" => $pageDescription, "form_object" => $formObject, "data_object" => $dataObject, "minicat" => $category, "sort" => $sort));
        return parent::add("config", array("name" => $varName, "value" => $initialValue, "type" => $type));
    }

    function rename($oldVarName, $newVarName) {
        $query = $config_tdb->query('config', "name='$oldVarName'", 1, 1);
        if(!empty($query[0])) {
            $this->edit('config', $query[0]['id'], array('name' => $newVarName));
            return $this->edit('ext_config', $query[0]['id'], array('name' => $newVarName));
        }
        return false;
    }
}
?>
