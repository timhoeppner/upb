<?php
/**
 * Upload class interacts with TDB to store and retrieve files in a table
 * 
 * Author: MyUPB Team
 * Version: 1.0
 */

class upload extends tdb {
    var $initialized=false;
    var $file=array();
    var $maxSize;
    
    /**
     * Class instantiation
     *
     * @param String $dir
     * @return bool, true on success
     */
    function upload($dir, $maxSize) {
        // Make sure we start fresh
        $this->initialized = false;
        
        // Initialize the TextDb object
        $this->tdb($dir."/", "main.tdb");
        
        // Check if the upload mod has been installed
        if(!file_exists($dir."/main_uploads.ta")) $this->sendError(E_USER_ERROR, "Uploads have not been installed", __LINE__);
        else {
            // Set this file pointer
            $this->setFp("uploads", "uploads");
            $this->maxSize = $maxSize * 1024; // Maxsize is given in KB we want bytes
            
            $this->initialized = true;
            return true;
        }
        
        return false;
    }
    
    /**
     * Stores an uploaded file in the database
     *
     * @param String[] $file - just send $_FILES["file_field"]
     * @return bool
     */
    function storeFile($file) {
        if(!$this->initialized) { $this->notInitialized(); return false; }
        
        if($file["error"] != UPLOAD_ERR_OK) return false;
        
        // Create a temporary location to store the data before putting it in the database
        //$tmpName = $this->uploadDir."/".md5(uniqid(rand(), true));
        
        if($this->maxSize < $file["size"]) return false;
        
        if(is_uploaded_file($file["tmp_name"])) {
            // Read the temp file into the database
            clearstatcache();
            $tmpName = $file["tmp_name"];
            
            $f = fopen($tmpName, "rb");
            $data = fread($f, filesize($tmpName));
            fclose($f);
            
            unlink($tmpName);
            
            $id = $this->add("uploads", array(
                    "name" => $file["name"],
                    "size" => $file["size"],
                    "downloads" => 0,
                    "data" => $data
                ));
            
            return $id;
        }
        
        return false;
    }
    
    function getFile($id) {
        if(!$this->initialized) { $this->notInitialized(); return false; }
        
        // Retrieve the file from the database
        $q = $this->get("uploads", $id);
        
        if($q !== FALSE) {
            $this->file = $q[0];
            return true;
        } else {
            $this->sendError(E_USER_ERROR, "Unable to retrieve UploadId: <b>{$id}</b>", __LINE__);
            return false;
        }
    }
    
    function dumpFile() {
        if(!$this->initialized) { $this->notInitialized(); return false; }
        
        // Pre-dump checks
        if(empty($this->file)) { $this->sendError(E_USER_NOTICE, "No file loaded, cannot dump", __LINE__); return false; }
        if(headers_sent()) { $this->sendError(E_USER_NOTICE, "Headers have already been sent, unable to dump file", __LINE__); return false; }
        
        // Dump the file to the browser
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=".$this->file["name"]);
        echo $this->file["data"];
    }
    
    function notInitialized() {
        $this->sendError(E_USER_NOTICE, "The upload class has not been initialized");
        return true;
    }
}
?>