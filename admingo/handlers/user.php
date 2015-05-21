<?php
  error_reporting(E_ERROR);  
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');

  $action = $_REQUEST['action'];
  $userid = 0;
  $sid = 105;

	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if (mysql_num_rows($result)) { 
			$myrow = mysql_fetch_object($result);
			$userid = $myrow->documentid;
		}
	}
  
  $title = clearField($_REQUEST['title']);			
	$email = strtolower(clearField($_REQUEST['email']));
	$password = clearField($_REQUEST['password']);		
						
	$error = "";
	
	if (!strlen($email)) {$error .= "Заполните поле E-mail; ";}
	if (!strlen($password) && ($action != 'update')) {$error .= "Заполните поле Пароль; ";}
  				   	
	if ($userid) { $useridWhere = " AND documentid!=$userid";}	 
	$sql = "SELECT * FROM user WHERE email = '$email' $useridWhere";
	$result = $db->execute($sql);
	$isemail = mysql_num_rows($result);			 
	if ($isemail>0) { 
		$myrow = mysql_fetch_object($result);
		$equal = '';
		if ($email == $myrow->email) $equal = 'E-mail';			  			  			
	  $error .= "Пользователь с таким $equal уже зарегистрирован; ";
	}
  
	if (strlen($error)) {
	  $result = array ('result'=>false, 'mess'=>$error);
    echo json_encode($result);
	  die();
	}    
 
	if ($action=='follower' || $action=='client') {
	  if (!$userid) {
  	  $result = array ('result'=>false, 'mess'=>'Вы не авторизованы');
      echo json_encode($result);
  	  die();
    }	  
    $skype = clearField($_REQUEST['skype']);
  	$phone = clearField($_REQUEST['phone']);
  	$birthday = clearField($_REQUEST['birthday']);
  	if (strlen($birthday)) $birthday = substr($birthday,6,4).'-'.substr($birthday,3,2).'-'.substr($birthday,0,2);
  	$image = clearField($_REQUEST['image']);
  	$passport = clearField($_REQUEST['passport']);
  	$latlng = clearField($_REQUEST['latlng']);  	
  	if (strlen($latlng)) {
  	   $temp = preg_split('/,/',$latlng);
  	   $lat = $temp[0];
  	   $lng = $temp[1];   	    
  	}  	 
  	$about = clearContent($_REQUEST['about']);
  	$agree = (int)$_REQUEST['agree'];
      		        
    $sql = "UPDATE user SET email='$email', password='$password', title='$title', `phone`='$phone', `skype`='$skype', `birthday`='$birthday', `image`='$image', `passport`='$passport', `lat`='$lat', `lng`='$lng', `about`='$about', `agree`='$agree' WHERE documentid = $userid";
    				
		$db->execute($sql, false);
  
	  $result = array ('result'=>true,'mess'=>'Данные успешно обновлены');
    echo json_encode($result);
	  die();
	} else if ($action=='new') {                     
		$lastid = getID();
		$stid = getSTID($sid);
    $stateid = md5($email.time());						
		        
    $sql1 = "INSERT INTO user SET documentid=$lastid, title='$title', email='$email', password='$password', stateid='$stateid', accesstime = NOW(), ip='".$_SERVER['REMOTE_ADDR']."'";
		$db->execute($sql1, false);
    						
		$sql2 = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$title', 'user', $stid)";
		$db->execute($sql2, false);
		      			
    /* Автологин */
		setcookie("stateid", $stateid, 0, '/');
    /* ///Автологин */ 
      
  	$userid = $lastid;
  	$base = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
  	mkdir($base.$userid, 0755);    

	  $result = array ('result'=>true);
    echo json_encode($result);
    die();
	}
	
?>
