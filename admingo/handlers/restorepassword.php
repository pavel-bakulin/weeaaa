<?php
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  header('Content-type: text/html; charset=UTF-8');
    
  $error = '';
  $success = '';
  $email = clearField($_REQUEST['email']);  
  
  if (!strlen($error)) {
  	$sql = "SELECT password FROM user WHERE email = '$email'";
  	$result = $db->execute($sql);
  	if ($myrow = mysql_fetch_object($result)) {
  		$password = $myrow->password;      
      $subject = $_SERVER['PHP_SELF'].": восстановление пароля";
      $body = "Ваш пароль для доступа в личный кабинет: $password<br/>";
      $sucess = mail($email, $subject, $body);		      
  	} else {
  	 $error = "Указанного E-mail нет в базе.";
  	}	
  }
  
	if (strlen($error)) {
    $result = array ('result'=>false,'mess'=>$error);
    echo json_encode($result);		 
	} else {
    $result = array ('result'=>true,'mess'=>"Пароль выслан на указанный E-mail");
    echo json_encode($result);		 
	}
?>