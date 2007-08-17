<?php
/**
 * Attachment Feature for UPB 2.x
 * Written by: MyUPB.com Team
 * 
 * Installs the new version of the upload system. The new system stores the files in a text database
 * and serves them as attachments to the browser. Technically any file can be served with no risk to
 * the server but people could upload harmful files which people could download and run on their local
 * machine.
 * 
 * The following table(s) are created in main.tdb
 * 
 *      uploads
 *          name        string[80]
 *          size        number[9]   allows up to around 1 Gig per file which is overkill
 *          downloads   number[10]  allows a count up to 9.9 billion
 *          data        memo        the data stored in 2Kb chunks
 *          id          id
 * 
 * The following field(s) are added to all posts_{id} tables in posts.tdb
 * 
 *      upload_id       references back to the upload table to gather information about the file
 */

// Since this might take some time to execute we should modify the timeout
set_time_limit(0);

// Get the nessassary includes
include "./includes/class/tdb.class.php";
include "./config.php";

// Create the tdb database objects
$main = new tdb(DB_DIR, "main.tdb");
$posts = new tdb(DB_DIR, "posts.tdb");

echo "Creating <b>uploads</b> table...";
flush();

// Add the new table to main.tdb
$main->createTable("uploads", array(
    array("name", "string", 80),
    array("size", "number", 9),
    array("downloads", "number", 10),
    array("data", "memo"),
    array("id", "id")
), 2048);

echo "<span style=\"color: green\">done</span><br/>\n";
flush();

// Get a list of tables in posts.tdb
$tableList = $posts->getTableList();

foreach($tableList as $table) {
    // Remove the database name from the tablename
    $table = str_replace("posts_", "", $table);
    
    // Make sure we don't get any topic tables
    if(substr($table, -6) != "topics" && is_numeric($table)) {
        echo "Adding <b>upload_id</b> to table <b>{$table}</b>...";
        flush();
        
        // Add the upload_id to the table
        $posts->setFp("posts", $table);
        
        $posts->addField("posts", array(
            "upload_id",
            "number",
            10
        ));
        
        echo "<span style=\"color: green\">done</span><br/>\n";
        flush();
    }
}

// Update the UPB_VERSION
$f = fopen("./config.php", "r+");
$data = fread($f, filesize("./config.php"));

$data = explode("\n", $data);

for($i=0;$i<count($data);$i++) {
    if(trim(strlen($data[$i])) == 0) {
        unset($data[$i]);
        continue;
    }
    
    if(strpos($data[$i], "UPB_VERSION") !== false) {
        $data[$i] = "define(\"UPB_VERSION\", \"2.1.1b\", true);";
    }
}

ftruncate($f, 0);
fwrite($f, implode("\n", $data));

fclose($f);

echo "<br/>\nPlease note any errors that occurred and report them to <a href=\"http://www.myupb.com\">MyUPB</a>. ";
echo "Otherwise enjoy the new attachment system!";
?>