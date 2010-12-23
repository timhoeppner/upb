<?php
/**
 * Upgrades/Fixes for UPB version 2.2.7
 * 
 * These functions are included and called from upgrade_tools.php. The function
 * naming convention update<Module>_v<major>_<minor>_<revision>() is used in case
 * the subsequent version updates the same module and has a naming conflict.
 * 
 * @author Tim Hoeppner <timhoeppner@gmail.com>
 * @author Chris Kent <online@chris-kent.co.uk>
 */

function updateMonitorTopics_v2_2_7()
{
	// TODO: add in Chris's monitor topic fix
}

function updateUploads_v2_2_7()
{
	/*
	echo "Installing file upload fix...";
	
	// Add the fileupload_types config variable if it doesn't already exist
	if( !isset($_CONFIG["fileupload_types"]) )
	{
		$result = $config_tdb->addVar('fileupload_types', '', 'config', 'text', 'text', '9', '4', 'File upload allowed types', 'List the allowable file extensions seperated by a comma');
		
		if( $result !== FALSE )
		{
			echo "<font color='green'>DONE</font>!<br />";
		}
		else
		{
			echo "<font color='red'>FAILED</font><br />";
		}
	}
	else
	{
		echo "Skipping<br />";
	}
	 */
	
	// Need to update upload database, add a new field.
}
?>
