<?php
// TODO: insert proper header block

class UPB_User
{
	function UPB_User($userId)
	{
	}

	/**
	 * Returns the latest time at which the user visited a
	 * new topic on the previous session.
	 */
	function getDbLastVisit()
	{
	}

	/**
	 * Returns the time of users newest topic visited on this
	 * particular session. Cannot return anything less than
	 * getDbLastVisit().
	 */
	function getSessionLastVisit()
	{
	}

	/**
	 * Returns an array of topic id's that have been added to the
	 * session data using addSessionViewedTopic().
	 */
	function getSessionViewedTopics()
	{
	}

	/**
	 * Sets database last visit to the session lastvisit.
	 *
	 * I guess the big challenge here is how to determine when a
	 * user is finished a particular session...
	 */
	function setDbLastVisit()
	{
	}

	/**
	 * This function is called anytime a user visits a topic. The
	 * session last visit is only updated if the viewed topic is
	 * newer than the existing session last visit.
	 */
	function setSessionLastVisit()
	{
	}

	/**
	 * This function is also called anytime a user visits a topic.
	 * The purpose of this function is to keep track of all the
	 * topics viewed by the user in a single session.
	 */
	function addSessionViewedTopic($t_id)
	{
	}
}
?>
