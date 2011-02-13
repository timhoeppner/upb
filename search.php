<?php
// Ultimate PHP Board
// Author: Tim Hoeppner aka RR_Pilot, FixITguy
// Reworked by Clark
// Website: http://www.myupb.com
// Version: 2.0

require_once('./includes/upb.initialize.php');
$where = "Search";
require_once('./includes/header.php');
$posts_tdb = new functions(DB_DIR.'/', "posts.tdb");
$sText = '';
	
if (!$tdb->is_logged_in()) 
{
  $_COOKIE["power_env"] = 0;
}
	
//build our forum list for selecting which forums to search from
$form_cats = $tdb->listRec("cats", 1);


//initialise the form values and keeps the options chosen when the page refreshes after submission
//also convert entered data from POST array into variables for using later
if (empty($_POST)) 
{
  $all_checked = 'checked';
}
	//form
	

if (count($_POST) > 0)
{
  $keytype = $_POST['keytype'];
  $usertype = $_POST['usertype'];
  $exact = $key_topics = $user_topics = $require_all = false;

  if ($keytype == 'topics') 
  {
    $key_topics = true;
  }

  $user = $_POST['user'];
    
  if ($usertype == 'topics') 
  {
    $user_topics = true;
  }
    
  if ($_POST['required'] == "all")
  {
    $require_all = true;
    $all_checked = "checked";
  }
  else
    $some_checked = "checked";

  if ($_POST['display'] == "threads" or empty($_POST))
  {
    $thread_display = "checked";
  }
  else 
  {
    $post_display = "checked";
  }
    
  $forums_req = $_POST['forums_req'];
  $exact = false;
    
  if (array_key_exists('exactuser',$_POST) and $_POST['exactuser'] == 'on') 
  {
    $exact = true;
  }
}
  
$form_select = "<option value='all' ";

if ($forums_req == "all")
{
  $form_select .= "selected='selected'";
}

$form_select .= ">All Forums</option>";
  
//BUILDING LIST FOR FORUM SELECTION BOX - FORUMS ARE INDENTED UNDER THEIR CATEGORY TITLES
//Category ids are prefixed by a c for the form values to differentiate from forum ids
//forums are filtered according to user permissions
//need to do the same for categories (can exclude forums automatically)
  
foreach($form_cats as $form_c) 
{
  if ($forums_req == 'c'.$form_c['id'])
  {
    $selected = "selected='selected'";
  }
  else
  {
    $selected = '';
  }
  
  $form_select .= "<option value='c".$form_c['id']."' $selected>".$form_c['name']."</option>";
		
  if (FALSE !== ($form_forums = $tdb->query("forums", "cat='".$form_c["id"]."'"))) 
  {
    foreach($form_forums as $form_f)
    {
      if ($forums_req == $form_f['id'])
      {
        $selected = "selected='selected'";
      }
      else
      {
        $selected = '';
      }
      
      if ($form_f["view"] <= $_COOKIE["power_env"]) 
      {
        $form_select .= "<option value='".$form_f["id"]."' $selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$form_f["forum"]."</option>\n";
			}
		}
	}
}

//create form using data from immediate previous search if available.
echo "<form action='search.php' method='post'>";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
echo "
		<tr>
			<td class='area_1' valign='top' width='50%'><fieldset><legend>Search By Keyword(s)</legend><input type='text' name='keyword' size='40' value='".$_POST['keyword']."' /><p><select name='keytype'><option value='posts'>Search Posts</option>
      <option value='topics' ";
      
if ($key_topics) 
{
  echo "selected='selected'"; 
}

echo ">Search Topic Titles Only</option></select><p> <input type='radio' name='required' value='all' $all_checked>All keywords<input type='radio' name='required' value='some' $some_checked>Some keywords</fieldset></td>
			<td class='area_2' valign='top'><fieldset><legend>Search By User</legend><input type='text' name='user' size='40' value='$user' /><p>
			<select name='usertype'><option value='posts'>Posts made by user</option>
      <option value='topics' "; 

if ($user_topics)
{
  echo "selected='selected'"; 
}  
echo ">Topics started by user</option></select> <input type='checkbox' name='exactuser' ";
  
if ($exact)
{
  echo "checked";
}
  
echo "> Exact username?</fieldset>
      </td>
    </tr>
    <tr><td class='footer_3' colspan='2'><img src='".SKIN_DIR."/images/spacer.gif' alt='' title='' /></td></tr>
    <tr>
			<td class='area_1'><fieldset><legend>Search Options</legend>
      Show results as <input type='radio' name='display' value='threads' $thread_display>Threads <input type='radio' name='display' value='posts' $post_display>Posts
      </fieldset></td>
			<td class='area_2'><fieldset><legend>Search Forum(s)</legend><select name='forums_req' size='5' multiple>$form_select
			</select></fieldset></td>
		</tr>
		<tr>
			<td class='footer_3a' style='text-align:center;' colspan='2'><input type='submit' name='search' value='Search' /><input type='reset' name='reset' value='Reset' /></td>
		</tr>";

//NOTE: REMOVED MULTIPLE AND SIZE FROM THE SELECT TAG IF NOT IMPLEMENTATION MULTIPLE USER SELECTION AT THIS JUNCTURE
echoTableFooter(SKIN_DIR);
echo "</form>";

//end form
	
//DISPLAY SEARCH SETTINGS IN ENGLISH - DEBUG ONLY
/*
if ($_POST['keyword'] != "")
{
  echo "Search for ";
  if ($require_all) 
  {
    echo "all "; 
  }
  else 
  {
    echo "some ";
  }
    
  echo "keyword(s): ".$_POST['keyword']."<p>";
  
  echo "Search in ";
  if (!$key_topics)
  {
    echo "posts<p>";
  }
  else
  {
    echo "topic titles only<p>";
  }
}

if ($_POST['user'] != "")
{
  echo "Search for ";
  if (!$user_topics)
  {
    echo "posts made by $user<p>";
  }
  else
  {
    echo "topics started by $user<p>";
  }
  
  if (array_key_exists('exactuser',$_POST) and $_POST['exactuser'] == 'on')
  {
    echo "The username match must be exact";
  }
}
*/
  
if ($_POST['user'] == "" and $_POST['keyword'] == "" and count($_POST) != 0)
{
	//ERROR MESSAGE IF FORM SUBMITTED WITH NO CRITERIA
  echo "<div class='alert'><div class='alert_text'>
<strong>Search failed!</strong></div><div style='padding:4px;'>No search criteria were entered</div></div>";
  exit;
}
else if (!empty($_POST))
{
  $forums = array();
	$fRecs = $tdb->listRec("forums", 1);
  $cRecs = $tdb->listRec('cats',1);
  		
	//this will need changing when using multiple selection
  if ($_POST["forums_req"] == "all") //create a forum list for searching for all forums
  {
    for($i = 0, $fmax = count($fRecs); $i < count($fRecs); $i++)
    {
      if ($fRecs[$i]["view"] <= $_COOKIE["power_env"]) 
      {
        $forums[] = $fRecs[$i]['id'];
      }
		}
	}
  else
  {
    //create a forum list for searching all forums in a category
    if (substr($_POST['forums_req'],0,1) == 'c') 
    {
      $catid = substr($_POST['forums_req'],1);
      $query = $tdb->basicQuery('cats','id',$catid);
        
      if ($query[0]["view"] <= $_COOKIE["power_env"])
      {
        $explode_ids = explode(',',$query[0]['sort']);
        foreach ($explode_ids as $expid) 
        {
          $forums[] = $expid;
        }
      }
    }
    else
    {
      //create single forum id
      $query = $tdb->basicQuery('forums','id',$_POST['forums_req']); 
      if ($query[0]["view"] <= $_COOKIE["power_env"]) 
      {
        $forums[] = $query[0]['id'];
      }
    }
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

//initialise blank queries
$query['topic'] = $query['post'] = "";

if ($user != "") //name entered in user box
{
  if ($user_topics)
  {
    //create query for searching for topics started by user (exact and non-exact)
    if ($exact)
    {
      $user_topic_query = "topic_starter='$user'";
    }
    else
    {  
      $user_topic_query = "topic_starter?'$user'";
    }
    
    $query['topic'][] = $user_topic_query;
  
  }
  else
  {
    //create query for searching for posts by user (exact and non-exact)
    if ($exact)
    {
      $user_post_query = "user_name='$user'";
    }
    else
    {
      $user_post_query = "user_name?'$user'";
    }
    
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
    {
      //create array of keyword search terms
      $keyword_topic_search[] = "subject?'$word'";
    }
      
    //create single query for all keywords based on AND or OR
    if ($require_all)
    {
      $keyword_topic_query = implode("&&",$keyword_topic_search);
    }
    else
    {
      $keyword_topic_query = implode("||",$keyword_topic_search);
    }
      
    $query['topic'][] = $keyword_topic_query; //add to topic query
  }
  else // OPTION 4 - KEYWORD SEARCH IN POSTS
  {
    foreach ($keywords as $word)
    {
      //create array of keyword search terms
      $keyword_post_search[] = "message?'$word'"; 
    }
  
    //create single query for all keywords based on AND or OR
    if ($require_all)
    {
      $keyword_post_query = implode("&&",$keyword_post_search);
    }
    else
    {
      $keyword_post_query = implode("||",$keyword_post_search);
    }
    
    $query['post'][] = $keyword_post_query; //add to post query;
    
  }
}

if ($query['topic'] != "") 
{
  $topic_query = implode("&&",$query['topic']); //combines queries for user and keywords for topics
}

if ($query['post'] != "")
{
  $post_query = implode("&&",$query['post']); //combines queries for user and keyword for posts
}
  
//DEBUGGING: Output queries
//echo "Post Query: $post_query<p>";
//echo "Topic Query: $topic_query<p>";
 
$forum_list = $tdb->query("forums","id>'0'");
  
$result = $post_result = array();

//create list of forums that are to be searched.
//THIS WILL NEED TO BE CHANGED WHEN IMPLEMENTING THE MULTIPLE SELECT OPTION
if (substr($forums_req,0,1) == "c" or $forums_req == 'all')
{
  foreach ($form_cats as $cat_member)
  {
    //GET FORUM DETAILS FOR ALL FORUMS
    if ($forums_req == "all")
    {
      $details = $tdb->query('forums',"cat='".$cat_member['id']."'");
      $res_forums[$cat_member['name']] = $details;
    }
    //GET DETAILS FOR ALL FORUM IN SELECTED CATEGORY
    elseif (substr($forums_req,0,1) == "c")
    {
      if (substr($forums_req,1) == $cat_member['id'])
      {
        $details = $tdb->query('forums',"cat='".$cat_member['id']."'");
        $res_forums[$cat_member['name']] = $details;
      }
      else
      {
        continue;
      }
    }
  }
}
else
//GET DETAILS FOR SELECTED FORUM
{
  $details = $tdb->query("forums", "id='$forums_req'");
  $cat = $tdb->basicQuery('cats',"id",$details[0]['cat']);
  $res_forums[$cat[0]['name']] = $details;
}

//TIME TO GET THE RESULTS  

foreach ($res_forums as $key => $fRec)
{
  foreach ($fRec as $forum_details)
  {
    $posts_tdb->setFp("topics", $forum_details["id"]."_topics");
    $posts_tdb->setFP("posts",$forum_details['id']);
  
    //GET THE RESULTS FOR TOPICS THAT MATCH THE TOPIC QUERY
    if ($topic_query != "")
    {
      if (FALSE !== ($r = $posts_tdb->query("topics", $topic_query)))
      {
        $r[0]['forum'] = $forum_details['forum'];
        $r[0]['category'] = $key;
        $result[$key][$forum_details['forum']] = $r;
      }
    
      //SEARCH FOR ANY TOPICS THAT ALSO MATCH THE POST QUERY
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
              $r[0]['forum'] = $forum_details['forum'];
              $r[0]['category'] = $key;
              
              if (!in_array($r,$post_result))
              {
                $post_result[$key][$forum_details['forum']] = $r;
              }
            }
          }
          
          $post_query_id = "";
          
        }
      }
    }
    elseif ($post_query != "" and $topic_query == "")
    {
      //SEARCHING FOR POSTS THAT MATCH THE POST QUERY IF THERE IS NO TOPIC QUERY
      if (FALSE !== ($r = $posts_tdb->query("posts", $post_query)))
      {
        $r[0]['forum'] = $forum_details['forum'];
        $r[0]['category'] = $key;
        $result[$key][$forum_details['forum']] = $r;
      }
      continue;
    }
  }
}

if (count($post_result) > 0)
{
  $result = $post_result[0];
}

//TIME TO DISPLAY RESULTS

$where = "Search Results";
echoTableHeading(str_replace($_CONFIG["where_sep"], $_CONFIG["table_sep"], $where), $_CONFIG);
if (count($result) == 0 or empty($result))
{
  echo "<tr><td class='area_1' style='text-align:center;font-weight:bold;'>No results found</td></tr>";
}
elseif ($_POST['display'] == 'threads')
{
  foreach ($result as $key => $thread)
  {
    foreach ($thread as $t_key => $t_details)
    {
      echo "<tr><td class='area_1'>Result in $key :: ".$t_key."</td></tr>";
      foreach ($t_details as $details)
      {
        echo "<tr><td class='area_2'><span class='link_1'> ".$details["subject"]."</span>";
        if (($user_topics and !$exact) or !$user_topics)
        {
          echo "<div class='description'>Started By:&nbsp;<span style='color:#".$statuscolor."'>".$t_details[0]["topic_starter"]."</span></div></td>";
        }
			}
    }
  }
}
else
{
  echo "This area displays posts";
}

echoTableFooter(SKIN_DIR);

require_once('./includes/footer.php');
?>