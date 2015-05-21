<?php
  session_start();
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";  
  date_default_timezone_set($_SESSION['timezone']);
  header('Content-type: text/html; charset=UTF-8');
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid,title,image FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
			$username = $myrow->title;
			$userimage = $myrow->image;			
		}
	}	
	
	$action = $_REQUEST['action'];
	
	if ($action == 'add') {
	  if (!$userid) die();
    $content = clearContent($_REQUEST['content']);
    $oid = (int)$_REQUEST['oid'];
    $answer_userid = (int)$_REQUEST['answer_userid'];
    $answer_username = clearField($_REQUEST['answer_username']);
    
    if (!$oid) {
  		$text = clearContent($_REQUEST['text']);
  		$socid = clearField($_REQUEST['socid']);  		
      $image = clearField($_REQUEST['image']);  		
  		
    	$sql = "SELECT id FROM object WHERE socid=$socid LIMIT 1";
    	$result = $db->execute($sql);		
    	if ($myrow = mysql_fetch_object($result)) {
        $oid = $myrow->id;
      }  		
  		
  		if (!$oid) {
  		  $lat = clearField($_REQUEST['lat']);
  		  $lng = clearField($_REQUEST['lng']);
  		  $author_name = clearField($_REQUEST['author_name']);
  		  $author_link = clearField($_REQUEST['author_link']);
  		  $author_image = clearField($_REQUEST['author_image']);  		  
        $sql = "INSERT INTO object (userid, socid, image, text, lat, lng, author_name, author_link, author_image) VALUES ($userid, $socid, '$image', '$text', '$lat', '$lng', '$author_name', '$author_link', '$author_image')";
        $db->execute($sql, false);  		
        
      	$sql = "SELECT LAST_INSERT_ID() AS oid";
      	$result = $db->execute($sql);		
      	if ($myrow = mysql_fetch_object($result)) {
          $oid = $myrow->oid;
        }      
      }
    }

    $sql = "INSERT INTO forum (oid, userid, content, answer_userid, answer_username) VALUES ($oid, $userid, '$content', $answer_userid, '$answer_username')";
    $db->execute($sql, false);
    
		$sql = "REPLACE INTO last_action (oid,action,value,userid,username,userimage) VALUES ($oid,'comment','$content',$userid,'$username','$userimage')";
		$db->execute($sql,false);    
    
    $sql = "UPDATE object SET fcount = fcount+1 WHERE id = $oid";
    $db->execute($sql, false);    
    
    $arr = array('userid'=>$userid, 'username'=>$username, 'userimage'=>getImgLink($userimage), 'content'=>$content, 'date'=>date('d.m.Y H:i'));
    $res = array('type'=>'forum_add', 'oid'=>$oid, 'data'=>array($arr));
    echo json_encode($res);
  } else if ($action == 'rate') {
    if (!$userid) die();
    
	} else if ($action == 'remove') {
	  if (!$userid) die();
    $iid = (int)$_REQUEST['iid'];
    $sql = "DELETE FROM forum WHERE id = $iid AND userid = $userid";
    $db->execute($sql, false);    
	} else if ($action == 'getlist') { 
	  $oid = (int)$_REQUEST['oid'];
	  if (!$oid) {
      $socid = (int)$_REQUEST['socid'];
      if (!$socid) return;
  		$sql = "SELECT id FROM object WHERE socid = $socid";
  		$result = $db->execute($sql);		
  		if ($myrow = mysql_fetch_object($result)) {			
  			$oid = $myrow->id;
      }	   
	  }
	  if (!$oid) return;
  	$sql = "SELECT forum.id, forum.content, forum.date, user.documentid AS userid, user.title AS username, user.image AS userimage FROM forum
            INNER JOIN user ON forum.userid = user.documentid
            WHERE forum.oid = $oid
            ORDER BY forum.id";
  	$result = $db->execute($sql);		
  	$arr = array();
  	while ($myrow = mysql_fetch_object($result)) {
  	  $arr_add = array('id'=>$myrow->id,'content'=>$myrow->content,'date'=>dateFormat2($myrow->date),'userid'=>$myrow->userid,'username'=>$myrow->username,'userimage'=>getImgLink($myrow->userimage));
  	  $arr[] = $arr_add;
    }	  
    
    $res = array('type'=>'forum_list', 'data'=>$arr, 'oid'=>$oid);
	  echo json_encode($res);  
	}
?>