<?php
	// Ultimate PHP Board
	// Author: Tim Hoeppner aka RR_Pilot, FixITguy
	// Reworked by Clark
	// Website: http://www.myupb.com
	// Version: 2.0
	// Using textdb Version: 4.3.2


  /*
	forum list - main.tdb :: forums (forum, cat, view, des, topics, posts, mod, id)
	topic lists - posts.tdb :: [FORUM_ID]_topics (icon, subject, topic_starter, sticky, replies, locked, last_post, user_name, user_id, p_ids, id)
	posts - posts.tdb :: [FORUM_ID] (icon, user_name, date, message, user_id, t_id, id)
	*/
	require_once('./includes/upb.initialize.php');
	$where = "Search";
	require_once('./includes/header.php');
	$posts_tdb = new functions(DB_DIR.'/', "posts.tdb");
	$sText = '';
	
	if (!$tdb->is_logged_in()) $_COOKIE["power_env"] = 0;
	//build our forum list for selecting which forums to search from
	$form_cats = $tdb->listRec("cats", 1);
	
	if (empty($_POST))
    $all_checked = 'checked';
	//form
	if (count($_POST) > 0)
  {
    $keytype = $_POST['keytype'];
    $usertype = $_POST['usertype'];
    $exact = $key_topics = $user_topics = $require_all = false;

    if ($keytype == 'topics')
      $key_topics = true;

    $user = $_POST['user'];
    if ($usertype == 'topics')
      $user_topics = true;
    
    if ($_POST['required'] == "all")
    {
      $require_all = true;
      $all_checked = "checked";
    }
    else
      $some_checked = "checked";


    $forums_req = $_POST['forums_req'];
    $exact = false;
    if (array_key_exists('exactuser',$_POST) and $_POST['exactuser'] == 'on')
      $exact = true;
  }
  
  $form_select = "<option value='all' ";
  if ($forums_req == "all")
  {
    $form_select .= "selected='selected'";
  }
  $form_select .= ">All Forums</option>";
  foreach($form_cats as $form_c) {
  if ($forums_req == 'c'.$form_c['id'])
    $selected = "selected='selected'";
  else
    $selected = '';
  $form_select .= "<option value='c".$form_c['id']."' $selected>".$form_c['name']."</option>";
		if (FALSE !== ($form_forums = $tdb->query("forums", "cat='".$form_c["id"]."'"))) {
			foreach($form_forums as $form_f)
      {
        if ($forums_req == $form_f['id'])
          $selected = "selected='selected'";
        else
          $selected = '';
        if ($form_f["view"] <= $_COOKIE["power_env"]) $form_select .= "<option value='".$form_f["id"]."' $selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$form_f["forum"]."</option>\n";
			}
		}
	}

  echo "<div id='searchbox' style='display:inline;'>";
	echo "<form action='search.php' method='post'>";
	echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
  echo "
		<tr>
			<td class='area_1' valign='top' width='50%'><fieldset><legend>Search By Keyword(s)</legend><input type='text' name='keyword' size='40' value='".$_POST['keyword']."' /><p><select name='keytype'><option value='posts'>Search Posts</option>
      <option value='topics' ";if ($key_topics) echo "selected='selected'"; echo ">Search Topic Titles Only</option></select><p> <input type='radio' name='required' value='all' $all_checked>All keywords<input type='radio' name='required' value='some' $some_checked>Some keywords</fieldset></td>
			<td class='area_2' valign='top'><fieldset><legend>Search By User</legend><input type='text' name='user' size='40' value='$user' /><p>
			<select name='usertype'><option value='posts'>Posts made by user</option>
      <option value='topics' "; if ($user_topics) echo "selected='selected'"; echo">Topics started by user</option></select> <input type='checkbox' name='exactuser' ";
      if ($exact)
        echo "checked";
    echo "> Exact username?</fieldset>
      </td>
    </tr>
    <tr><td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td></tr>
    <tr>
			<td class='area_1'><fieldset><legend>Search Options</legend></fieldset></td>
			<td class='area_2'><fieldset><legend>Search Forum(s)</legend><select name='forums_req'>$form_select
			</select></fieldset></td>
		</tr>
		<tr>
			<td class='footer_3a' style='text-align:center;' colspan='2'><input type='submit' name='search' value='Search' /><input type='reset' name='reset' value='Reset' /></td>
		</tr>";
	echoTableFooter(SKIN_DIR);
	echo "</div></form>";
	//end form
	
  if (count($_POST) != 0)
    echo "<div class='showhide' id='showhidebuttons' ><img src='images/up.gif' alt='Hide Search Box' title='Hide Search Box' onClick=\"showhide('searchbox','showhidebuttons');\"></div>";
  
  //dump($_POST);
	
	//DISPLAY SEARCH SETTINGS - DEBUG ONLY
  /*if ($_POST['keyword'] != "")
  {
    echo "Search for ";
    if ($require_all) echo "all "; else echo "some ";
    echo "keyword(s): ".$_POST['keyword']."<p>";
    echo "Search in ";
    if (!$key_topics)
      echo "posts<p>";
    else
      echo "topic titles only<p>";
  }
  
  if ($_POST['user'] != "")
  {
    echo "Search for ";
    if (!$user_topics)
      echo "posts made by $user<p>";
    else
      echo "topics started by $user<p>";

    if (array_key_exists('exactuser',$_POST) and $_POST['exactuser'] == 'on')
      echo "The username match must be exact";
  }
	*/
  if ($_POST['user'] == "" and $_POST['keyword'] == "" and count($_POST) != 0)
	{
	echo "<div class='alert'><div class='alert_text'>
<strong>Search failed!</strong></div><div style='padding:4px;'>No search criteria were entered</div></div>";
    exit;
  }
  else if (!empty($_POST))
	{
  $forums = array();
		$fRecs = $tdb->listRec("forums", 1);
    $cRecs = $tdb->listRec('cats',1);
    //dump($cRecs);
    //dump($fRecs);
		if ($_POST["forums_req"] == "all")
    {
      for($i = 0, $fmax = count($fRecs); $i < count($fRecs); $i++)
      {
        if ($fRecs[$i]["view"] <= $_COOKIE["power_env"])
          $forums[] = $fRecs[$i]['id'];
			}
		}
    else
    {
      if (substr($_POST['forums_req'],0,1) == 'c')
      {
        $catid = substr($_POST['forums_req'],1);
        $query = $tdb->basicQuery('cats','id',$catid);
        if ($query[0]["view"] <= $_COOKIE["power_env"])
        {
          $explode_ids = explode(',',$query[0]['sort']);
          foreach ($explode_ids as $expid)
            $forums[] = $expid;
        }
      }
      else
      {
          $query = $tdb->basicQuery('forums','id',$_POST['forums_req']);
          if ($query[0]["view"] <= $_COOKIE["power_env"])
            $forums[] = $query[0]['id'];
      }
    }

//  TIME TO BUILD QUERY

/*
8 possibilities for searches

1) Username search for topics started by user
2) Username search for posts by user
3) Keyword search in topic titles
4) Keyword search in posts
5) Search for topic started by user with keyword in topic title
6) Search for post by user with keyword in post
7) Search for post by user with keyword in topic title
8) Search for topic by user with keyword in post
*/

// CODING FOR FIRST FOUR OPTIONS - BASIC QUERY FORMULATION
// CODING WILL BE DONE SEPARATELY FOR EACH OPTION AND THEN COMBINED IF POSSIBLE

// OPTION 1 - USER NAME SEARCH FOR TOPICS STARTED BY USER
// OPTION 2 - USER NAME SEARCH FOR POSTS BY USER

//create sub-queries for combining later.
$query['topic'] = $query['post'] = "";

  if ($user != "")
  {
    //create sub-queries for combining later.
    if ($user_topics)
    {
      if ($exact)
        $user_topic_query = "topic_starter='$user'";
      else
        $user_topic_query = "topic_starter?'$user'";
      $query['topic'][] = $user_topic_query;
    }
    else
    {
      if ($exact)
        $user_post_query = "user_name='$user'";
      else
        $user_post_query = "user_name?'$user'";
      $query['post'][] = $user_post_query;
    }
  }

// BREAK KEYWORDS INTO ARRAY
  if ($_POST['keyword'] != "")
  {
    $keywords = explode(" ",$_POST['keyword']);

    //create sub-queries for combining later.
    if ($key_topics) // OPTION 3 - KEYWORD SEARCH IN TOPIC TITLES
    {
      foreach ($keywords as $word)
        $keyword_topic_search[] = "subject?'$word'";

      if ($require_all)
        $keyword_topic_query = implode("&&",$keyword_topic_search);
      else
        $keyword_topic_query = implode("||",$keyword_topic_search);

        $query['topic'][] = $keyword_topic_query;
    }
    else // OPTION 4 - KEYWORD SEARCH IN POSTS
    {
      foreach ($keywords as $word)
        $keyword_post_search[] = "message?'$word'";

      if ($require_all)
        $keyword_post_query = implode("&&",$keyword_post_search);
      else
        $keyword_post_query = implode("||",$keyword_post_search);
      
        $query['post'][] = $keyword_post_query;
    }
  }

  //dump($query);
  if ($query['topic'] != "")
    $topic_query = implode("&&",$query['topic']);
  if ($query['post'] != "")
    $post_query = implode("&&",$query['post']);
  //echo "PQ: $post_query<p>";
  //echo "TQ: $topic_query<p>";

		//query time...
  //dump($form_cats);
  
  $forum_list = $tdb->query("forums","id>'0'");
  
  
  if ($forums_req == "all")
    $forums = $tdb->query("forums","id>'0'");
  elseif (substr($forums_req,0,1) == "c")
    $forums = $tdb->query("forums", "cat='".substr($forums_req,1)."'");
  else
    $forums = $tdb->query("forums", "id='$forums_req'"); //working

$result = $post_result = array();

foreach ($forums as $fRec)
{
  $posts_tdb->setFp("topics", $fRec["id"]."_topics");
  $posts_tdb->setFP("posts",$fRec['id']);
  //echo "Entering loop for forum ".$fRec['id']."<p>";
  if ($topic_query != "")
  {
    if (FALSE !== ($r = $posts_tdb->query("topics", $topic_query)))
      $result[] = $r;

    if ($post_query != "")
    {
      foreach ($result as $result_array)
      {
        $post_ids = $result_array[0]['p_ids'];
        $pids = explode(",",$post_ids);
        foreach ($pids as $pid)
        {
          $post_query_id = $post_query."&&id='$pid'";
          if (FALSE !== ($r = $posts_tdb->query("posts", $post_query)))
          {
            if (!in_array($r,$post_result))
              $post_result[] = $r;
          }
        }
        $post_query_id = "";
      }
    }
  }
  elseif ($post_query != "" and $topic_query == "")
  {
    if (FALSE !== ($r = $posts_tdb->query("posts", $post_query)))
      $result[] = $r;
    continue;
  }
}

if (count($post_result) > 0)
  $result = $post_result[0];


dump($result);

//for post and user search, search for threads first
//then when threads found check post details on each post.

/*
    $result = array();
		foreach($forums as $fRec) {
			//run on each forum
			$posts_tdb->setFp("topics", $fRec["id"]."_topics");
			if (FALSE !== ($r = $posts_tdb->query("topics", $sTopics, 1, $MAX_TOPIC_RESULTS))) {
				$MAX_TOPIC_RESULTS -= count($r);
				$resultTopics[$fRec["id"]]["forumName"] = $fRec["forum"];
				$resultTopics[$fRec["id"]]["catID"] = $fRec["cat"];
				//first 10 results...
				foreach($r as $sRec) {
					$resultTopics[$fRec["id"]]["records"][] = array("topicID" => $sRec["id"], "topicName" => $sRec["subject"]);
				}
			}
			unset($r);
			if ($intopic) {
				$posts_tdb->setFp("posts", $fRec["id"]);
				if (FALSE !== ($r = $posts_tdb->query("posts", $sPosts, 1, $MAX_POSTS_RESULTS))) {
					$MAX_POSTS_RESULTS -= count($r);
					$resultPosts[$fRec["id"]]["forumName"] = $fRec["forum"];
					$resultPosts[$fRec["id"]]["catID"] = $fRec["cat"];
					//first 10 results...
					foreach($r as $sRec) {
						//need to get the topic name...
						$topic_query = $posts_tdb->get("topics", $sRec["t_id"]);
						$sRec["topicName"] = $topic_query[0]["subject"];
						$resultPosts[$fRec["id"]]["records"][] = $sRec;
					}
				}
			}
		}

	//Lets query this
	/*
	$resultTopics {
	forumID {
	forumName
	catID
	records {
	index {
	topicID
	topicName [the search text should be bolded, maybe not...]
	}
	}
	}
	}
	$resultPosts {
	forumID {
	forumName
	catID
	records {
	index {
	COMPLETE RESULT
	}
	}
	}
	}
	*/
	//results here
	if (!empty($resultTopics)) {
		echo "<br /><br />";
		echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], "First 10 Results..."), $_CONFIG);
		echo "";
		//while(list($fId, $result) = each($results)) {
		foreach($resultTopics as $fID => $result) {
			if (empty($result)) continue;
			$cRec = $tdb->get('cats', $result["catID"]);
			echo "
			<tr>
				<td class='area_1' style='padding:8px;'><strong>Results in ".$cRec[0]['name']." ".$_CONFIG['table_sep']." <a href=\"viewforum.php?id={$fID}\" target=_blank>{$result['forumName']}</a></strong>:</td>
			</tr>";
			foreach($result["records"] as $topic) {
				echo "
			<tr>
				<td class='area_2' style='padding:8px;'><span class='link_1'><a href='viewtopic.php?id={$fID}&t_id={$topic['topicID']}' target=_blank>{$topic['topicName']}</a></span></td>
			</tr>";
			}
		}
		echoTableFooter(SKIN_DIR);
		flush();
	}
	if (!empty($resultPosts)) {
		echo "<div style='padding:8px;'>Showing the first 10 posts in topic results...</div>";
		$table_color = $table1;
		
		foreach($resultPosts as $fID => $result) {
			foreach($result["records"] as $post) {
				$msg = format_text(filterLanguage(UPBcoding($post["message"]), $_CONFIG));
				$msg = removeRedirect($msg);
				echo "";
				echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], "Result from: <a href='viewforum.php?id=".$fID."'>".$result["forumName"]."</a> ".$_CONFIG["where_sep"]." <a href='viewtopic.php?id=".$fID."&t_id=".$post["t_id"]."'>".$post["topicName"]."</a>"), $_CONFIG);
				echo "
					<tr>
						<th>Created by: ".$post["user_name"]."</th>
					</tr>";
				echo "
					<tr>
						<td class='area_2'><div style='padding:12px;margin-bottom:20px;'>$msg</div></td>
					</tr>";
				echoTableFooter(SKIN_DIR);
			}
		}
	}
	if (empty($resultTopics) && empty($resultPosts) && isset($_GET["q"]) && strlen(trim($_GET["q"])) > 0) {
		echo "<div class='alert'><div class='alert_text'>
<strong>Search failed!</strong></div><div style='padding:4px;'>......No results found......</div></div>";
	}
	}
	require_once('./includes/footer.php');
?>