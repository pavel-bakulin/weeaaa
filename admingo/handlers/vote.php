<?php
  error_reporting(E_ERROR);  
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $action = $_POST['action'];
  
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
	if (!$userid) {
		$result = array ('result'=>false,'msg'=>'Необходимо авторизоваться');
	  echo json_encode($result);
	  return;
	}  
  
  if ($action=='object') {
    $oid = (int)$_POST['oid'];
    $value = (int)$_POST['value'];    
    
    if (!$oid) {
      $socid = clearField($_POST['socid']);
    	$sql = "SELECT id FROM object WHERE socid=$socid LIMIT 1";
    	$result = $db->execute($sql);		
    	if ($myrow = mysql_fetch_object($result)) {
        $oid = $myrow->id;
      }  		
  		
  		if (!$oid) {
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
      }
    }

		$sql = "INSERT INTO object_vote (oid,userid,value) VALUES ($oid,$userid,$value)";
		$db->execute($sql,false);
    if (mysql_affected_rows()) {      
      if ($value==1) {
    		$sql = "UPDATE object SET rate=rate+1 WHERE id = $oid";
    		$str_value = '<i class="glyphicon glyphicon-thumbs-up"></i>';
    	} else {
    	  $sql = "UPDATE object SET rate=rate-1 WHERE id = $oid";
    	  $str_value = '<i class="glyphicon glyphicon-thumbs-down"></i>';
    	}
    	$db->execute($sql,false);    	
    	
    	$forum_msg = "голосую $str_value";
      $sql = "INSERT INTO forum (oid, userid, content) VALUES ($oid, $userid, '$forum_msg')";
      $db->execute($sql, false);    	
    	
  		$sql = "REPLACE INTO last_action (oid,action,value,userid,username,userimage) VALUES ($oid,'vote','$value',$userid,'$username','$userimage')";
  		$db->execute($sql,false);    	
    	
    	$result = array ('result'=>true,'oid'=>$oid,'forum'=>array('data'=>array(0=>array('content'=>$forum_msg,'date'=>dateFormat2(date('Y-m-d H:i:s')),'userid'=>$userid,'username'=>$username,'userimage'=>getImgLink($userimage)))));
    }    				
		        						  
    echo json_encode($result);
    return;
  }
?>