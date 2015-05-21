<?php
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $oid = (int)$_REQUEST['oid'];
  $value = (int)$_REQUEST['value'];  
  $count = 0;
  $data = array();
  
	$sql = "SELECT count(1) AS count FROM object_vote
	        INNER JOIN user ON object_vote.userid = user.documentid
          WHERE object_vote.oid = $oid AND object_vote.value = $value";
	$result = $db->execute($sql);
	
	while ($myrow = mysql_fetch_object($result)) {
	  $count = $myrow->count;
	}
  
  if ($count) {
  	$sql = "SELECT documentid, title, image FROM object_vote
  	        INNER JOIN user ON object_vote.userid = user.documentid
            WHERE object_vote.oid = $oid AND object_vote.value = $value
            LIMIT 25";
  	$result = $db->execute($sql);  		
  	while ($myrow = mysql_fetch_object($result)) {
      $temp = array('userid'=>$myrow->documentid,'title'=>$myrow->title,'image'=>getImgLink($myrow->image,''));			
  		$data[] = $temp;
  	}
  }
  
  $text = '';
  if ($count) {
	 if ($value==1) $text .= 'Понравилось <span class="count">'.$count.'</span> '.peopleCount($count);
	 else $text .= 'Не понравилось '.$count.' '.peopleCount($count);
	} else $text .= 'Нет голосов '.(($value==1)?'За':'Против');
	$arr = array('data'=>$data, 'text'=>$text, 'count'=>$count);
	echo json_encode($arr);
?>