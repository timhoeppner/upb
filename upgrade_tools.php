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

function successResponse($msg)
{
	return "<span style=\"color: green\">".$msg."</span>";
}

function failureResponse($msg)
{
	$outputMsg = "<span style=\"color: red\">".$msg."</span>";
	$outputMsg .= "<br /><br />\n\nPlease consult the <a href=\"http://forum.myupb.com\">MyUPB team</a> for further instruction. ";
	$outputMsg .= "Copy and paste the transcript above into another forum post so the team can aid you in the most efficient manner.";
	
	return $outputMsg;
}

function fixedResponse($msg)
{
	$outputMsg = "<span style=\"color: yellow\">".$msg."</span>";
	
	return $outputMsg;
}

/**
 * Uses the user API to perform a backup before proceeding
 */
function AJAX_backupDatabase($go = "no")
{
	$response = new xajaxResponse;

	if($go == "no")
	{
		$response->append("progress", "innerHTML", "Performing backup...");
		$response->call("xajax_AJAX_backupDatabase", "yes");
	}
	else 
	{
		$filename = "";
		$backup = new UPB_DatabaseBackup;
		
		if($backup->backup($filename) == true)
		{
			$downloadLink = "No Link";
			$backup->displayDownloadLink($filename, $downloadLink);
			$response->append("progress", "innerHTML", successResponse("Success")."&nbsp;&nbsp;&nbsp;[". $downloadLink ."]");
			$response->call("xajax_AJAX_validateRootConfig");
		}
		else 
		{
			$response->append("progress", "innerHTML", failureResponse("Failure", true));
		}
		
		$response->append("progress", "innerHTML", "<br />");
	}

	return $response;
}

/**
 * Checks over the config.php configuration file and verifies everything is in order
 */
function AJAX_validateRootConfig($go = "no")
{
	$response = new xajaxResponse;
	
	if($go == "no")
	{
		$response->append("progress", "innerHTML", "Validating root config.php...<br />");
		$response->call("xajax_AJAX_validateRootConfig", "yes");
	}
	else 
	{
		// UPB requires the root config.php contains 3 constants; UPB_VERSION,
		// DB_DIR, and ADMIN_EMAIL. Make sure they all exist and are valid.
		$failure = false;
		
		if(defined("UPB_VERSION"))
		{
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Found <span style=\"font-style: italic\">UPB_VERSION</span><br />");
		}
		else
		{
			// TODO: add constant to config.php
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Missing <span style=\"font-style: italic\">UPB_VERSION</span>".fixedResponse("Fixed")."<br />");
		}
		
		// No validation is needed here since the backup would have failed if this was invalid
		if(defined("DB_DIR"))
		{
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Found <span style=\"font-style: italic\">DB_DIR</span><br />");
		}
		else
		{
			// TODO: add constant to config.php
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Missing <span style=\"font-style: italic\">DB_DIR</span>".fixedResponse("Fixed")."<br />");
		}
		
		if(defined("ADMIN_EMAIL"))
		{
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Found <span style=\"font-style: italic\">ADMIN_EMAIL</span><br />");
		}
		else
		{
			// TODO: add constant to config.php
			$response->append("progress", "innerHTML", "&nbsp;&nbsp;&nbsp;Missing <span style=\"font-style: italic\">ADMIN_EMAIL</span>".fixedResponse("Fixed")."<br />");
		}
		
		$response->append("progress", "innerHTML", "config.php validation result: ");
		
		if($failure == false)
		{
			$response->append("progress", "innerHTML", successResponse("Success")."<br />");
		}
		else 
		{
			$response->append("progress", "innerHTML", failureResponse("Failure")."<br />");
		}
	}

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
