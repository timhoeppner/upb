<?php
/**
 * Upgrade tools script contains a set of functions used by the upgrader. This
 * file is seperate from the upgrader in order to keep the code nice and clean.
 * By breaking up the upgrade process in small chunks we can give the end-user
 * very detailed status and progress information as we call all of the AJAX
 * functions.
 *
 * This file is meant to be included in from the upgrade.php script.
 *
 * @author Tim Hoeppner <timhoeppner@gmail.com>
 */

// TODO: need to figure out an effective way to validate the previous action has
//	 actually taken place. We can use the $response->call() to call the next
//	 function but how can we confirm forsure someone isn't faking the call.

/**
 * Uses the user API to perform a backup before proceeding
 */
function AJAX_backupDatabase($go = "no")
{
	$response = new xajaxResponse;

	//$backup = new UPB_DatabaseBackup;
	
	//$response->alert("AAAAAHHHHHH");
	
	if($go == "no")
	{
		$response->append("progress", "innerHTML", "Performing backup...");
		$response->call("xajax_AJAX_backupDatabase", "yes");
	}
	else 
	{
	
		for($i=0;$i<60000;$i++)
		{
			for($j=0;$j<200;$j++)
			{
				$c = $i+1;
			}
		}
		
		$response->append("progress", "innerHTML", "Done");
	
	}

	return $response;
}

/**
 * Checks over the config.php configuration file and verifies everything is in order
 */
function AJAX_validateRootConfig()
{
	$response = new xajaxResponse;

	return $response;
}

/**
 * Checks to make sure all the correct table fields are present and checks to make
 * sure all expected table rows exist.
 */
function AJAX_validateDbConfig()
{
	$response = new xajaxResponse;

	return $response;
}

/**
 * Checks to make sure all table fields are present in the categories table.
 */
function AJAX_validateDbCategories()
{
	$response = new xajaxResponse;

	return $response;
}

/**
 * Checks to make sure all table fields are present in the forums table.
 */
function AJAX_validateDbForums()
{
	$response = new xajaxResponse;

	return $response;
}

/**
 * Checks to make sure all table fields are present in the topics table.
 */
function AJAX_validateDbTopics()
{
	$response = new xajaxResponse;

	return $response;
}

/**
 * Checks to make sure all table fields are present in the posts table.
 */
function AJAX_validateDbPosts()
{
	$response = new xajaxResponse;

	return $response;
}
?>
