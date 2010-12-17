<?php

require_once('./includes/upb.initialize.php');
require_once('./includes/class/posts.class.php');
require_once('./includes/class/upload.class.php');
$posts_tdb = new posts(DB_DIR."/", "posts.tdb");
$cRecs = $tdb->listRec("cats", 1);
//dump($fRecs);
$uploads = $tdb->listRec("uploads",1);
//dump($cRecs);
//$tdb->addField('uploads',array('forum_id', 'number', 5));
//$tdb->addField('uploads',array('topic_id', 'number', 5));

foreach ($cRecs as $cRec)
{
  $tdb->setFP("forums","forums");
  if (FALSE === ($fRecs = $tdb->get("forums", $cRec["id"])))
    continue; 
  $posts_tdb->setFp("topics", $cRec["id"]."_topics");
  $posts_tdb->setFp("posts", $cRec["id"]);
  echo "FORUM LISTING";
  dump($fRecs);

  foreach ($fRecs as $fRec)
  {
    if (FALSE === ($tRecs = $posts_tdb->get("topics", $fRec["id"])))
      continue;
    echo "TOPIC LISTING";
    dump($tRecs);

    foreach ($tRecs as $tRec)
    {
      $posts_tdb->set_topic($tRec);
      $posts_tdb->set_forum($fRec);

      $pRecs = $posts_tdb->listRec("posts",1);
      //dump($pRecs);
      
      foreach ($pRecs as $key => $pRec)
      {
        
        if ($pRec['upload_id'] > 0)
        {
          echo "POST LISTING $key";
          dump($pRec);
        }
      }
    }
  }
  $tdb->cleanUp();
  $posts_tdb->cleanUP();
}


function getUploads($fid,$tid,$pid,$upload_ids,$location)
{
		if ($upload_ids == "" or $upload_ids == "0" or $upload_ids == false)
		return;
		
		$output =  "";
		$downloads = "";
		$ids = explode(",",$upload_ids);


		foreach ($ids as $id)
		{
			if($id > 0)
			{
				//check information is in the upload database
				$q = $this->get("uploads", $id, array("name", "downloads","file_loca"));

				if(!empty($q[0]) && file_exists($location."/".$q[0]['file_loca']))
				{
					$attachName = $q[0]["name"];
					$attachDownloads = $q[0]["downloads"];

					$filesize= filesize($location."/".$q[0]['file_loca']);
					if ($filesize < 1024)
					$attachSize = $filesize." bytes";
					else if ($filesize > 1048576)
					$attachSize = round(filesize($location."/".$q[0]['file_loca'])/1048576,2)."MB";
					else
					$attachSize = floor(filesize($location."/".$q[0]['file_loca'])/1024)."KB";

					//echo $attachSize;
					$downloads .= "<a href='downloadattachment.php?upload_id=$id&id=$fid&tid=$tid'>{$attachName}</a> ({$attachSize} / $attachDownloads Downloads)";
					if ((int)$_COOKIE['power_env'] >= 3 or $userid == (int)$_COOKIE['id_env'])
					$downloads .= " <a href=\"javascript:deleteFile($fid,$tid,$pid,$id,'$attachName',".(int)$_COOKIE['id_env'].",'$fid-$tid-$pid-attach')\" onMouseOver=\"window.status='Delete $attachName';\">Delete</a>";
					$downloads .= "<p>";
				}
			}
		}



		if ($downloads != "")
		{
			$output .= "<p><fieldset><legend>Attached File(s)</legend>";
			$output .= $downloads;
			$output .= "</fieldset>";
		}

		return $output;
}
?>