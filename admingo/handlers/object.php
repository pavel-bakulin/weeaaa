<?php
  session_start();
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  require_once "php_emoji/emoji.php";  
  date_default_timezone_set($_SESSION['timezone']);
  header('Content-type: text/html; charset=UTF-8');
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid,title,image,status FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
			$username = $myrow->title;
			$userimage = $myrow->image;			
			$userstatus = (int)$myrow->status;
		}
	}	
	
	$action = $_REQUEST['action'];
	
	if ($action == 'add') {
	  if (!$userid) die();
	  $link = clearField($_REQUEST['link']);
	  $image = clearField($_REQUEST['image']);
	  $lat = clearField($_REQUEST['lat']);
	  $lng = clearField($_REQUEST['lng']);
	  $tag = clearField($_REQUEST['tag']);
    $text = autoURL(clearContent($_REQUEST['text']));
	  $author_name = $username;
	  $author_link = "/u$userid/";
	  $author_image = $userimage;    
    
    $sql = "INSERT INTO object (userid, image, link, text, lat, lng, author_name, author_link, author_image, tag) VALUES ($userid, '$image', '$link', '$text', '$lat', '$lng', '$author_name', '$author_link', '$author_image', '$tag')";
    $db->execute($sql, false);  		
        
    $res = array('type'=>'object_add');
    echo json_encode($res);
  } else if ($action == 'update') {
	  if (!$userid) die();
	  if (!$userstatus) $user_test = "AND userid = $userid";
	  $oid = (int)$_REQUEST['oid'];
	  $link = clearField($_REQUEST['link']);
	  $image = clearField($_REQUEST['image']);
	  $lat = clearField($_REQUEST['lat']);
	  $lng = clearField($_REQUEST['lng']);
	  $tag = clearField($_REQUEST['tag']);
    $text = autoURL(clearContent($_REQUEST['text']));
	  $author_name = $username;
	  $author_link = "/u$userid/";
	  $author_image = $userimage;    
    
    $sql = "UPDATE object SET image='$image', link='$link', text='$text', lat='$lat', lng='$lng', tag='$tag' WHERE id = $oid $user_test";
    $db->execute($sql, false);  		
        
    $res = array('type'=>'object_update');
    echo json_encode($res);
  } else if ($action == 'delete') {
    if (!$userid) die();
    if (!$userstatus) $user_test = "AND userid = $userid";
    $iid = (int)$_REQUEST['iid'];
    $sql = "UPDATE object SET deleted=1 WHERE id = $iid $user_test";
    $db->execute($sql, false);
  	/*if (mysql_affected_rows()) {
      $sql = "DELETE FROM forum WHERE oid = $iid";
    	$db->execute($sql, false);            	
      $sql = "DELETE FROM last_action WHERE oid = $iid";
    	$db->execute($sql, false);    	
      $sql = "DELETE FROM object_vote WHERE oid = $iid";
    	$db->execute($sql, false);    	
  	}*/
	} else if ($action == 'getitem') {
	  $iid = (int)$_REQUEST['id'];      	  	  	
    	  
		$sql = "SELECT * FROM object WHERE id = $iid";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {
		  if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
  	  else $image = '/img/noimg.png';
      $arr = array( 'type' => 'own', 
              'fullname' => span_to_html($myrow->author_name),                                
              'bdate' => '',
              'userid' => $myrow->userid,
              'socid' => ($myrow->socid)?$myrow->socid:$myrow->id, 
              'oid' => $myrow->id,
              'owner_id' => 0,
              'src' => $image, 
              'src_big' => $image,
              'src_small' => $image,
              'width' => 0,
              'height' => 0,
              'text' => span_to_html($myrow->text),
              'link' => $myrow->link,
              'fcount' => $myrow->fcount,
              'created' => timeAgo(strtotime($myrow->date)),
              'lat' => $myrow->lat,
              'long' => $myrow->lng,
              'rate' => $myrow->rate,
              'tag' => $myrow->tag,
              'profile_url' => $myrow->author_link,
              'profile_picture' => (strpos($myrow->author_image,'http:')!==false||strpos($myrow->author_image,'https:')!==false)?$myrow->author_image:getImgLink($myrow->author_image,'')
              );             
      if ($userid) { 
        $sql2 = "SELECT oid,value FROM object_vote WHERE userid = $userid AND oid = $iid";
        $result2 = $db->execute($sql2);
        if ($myrow2 = mysql_fetch_object($result2)) {
          $vote = $myrow2->value;
        }    
        if (isset($vote)) $arr['myvote'] = $vote;	  
      }
    }
    //$res = array('type'=>'object_item', 'data'=>$arr);
	  echo json_encode($arr);
  } else if ($action == 'getlink') {
    if (!$userid) die();
    $socid = clearField($_POST['socid']);
	  $lat = clearField($_REQUEST['lat']);
	  $lng = clearField($_REQUEST['lng']);
	  $image = clearField($_REQUEST['image']);
	  $text = clearField($_REQUEST['text']);
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
    
		$sql = "REPLACE INTO last_action (oid,action,value,userid,username,userimage) VALUES ($oid,'getlink','',$userid,'$username','$userimage')";
		$db->execute($sql,false);    
    
    echo json_encode(array('result'=>true,'link'=>'http://'.$_SERVER['SERVER_NAME'].'/#object'.$oid,'oid'=>$oid));        
	}
?>