<?php
  error_reporting(E_ERROR);  
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  header('Content-type: text/html; charset=UTF-8');
  
	$email = clearField($_REQUEST['email']);
	$password = clearField($_REQUEST['password']);
	
	if (!strlen($email) || !strlen($password)) {
    $result = array ('result'=>false,'mess'=>'Введите e-mail и пароль');
    echo json_encode($result);	
    die();
	}
	
	if (validateUserPass($email, $password)) {
		$stateid = md5($email.time());
		$sql = "UPDATE user SET stateid='$stateid', accesstime = NOW(), ip='".$_SERVER['REMOTE_ADDR']."' WHERE email = '$email'";
		$db->execute($sql, false);		
		setcookie("stateid", $stateid, 0, '/');
	  $result = array ('result'=>true);
    echo json_encode($result);
	}
	else {
	  $result = array ('result'=>false,'mess'=>'Неверная пара логин/пароль');
    echo json_encode($result);
	}
?>