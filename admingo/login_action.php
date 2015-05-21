<?php
  error_reporting(E_ERROR);
  setlocale(LC_ALL,'ru_RU.UTF-8'); 
  require_once "config.php";
  require_once "db_connect.php";
  require_once "lib.php";
  
  $ip = $_SERVER['REMOTE_ADDR'];  
  $result = $db->execute("SELECT count(1) as count FROM `wronglogin` WHERE `ip`='$ip' AND date + INTERVAL 300 SECOND > now()");
  if ($myrow = mysql_fetch_object($result)) {
  	$wrongCount = $myrow->count;
  }
  if ($wrongCount > 3) {
    die("error='Превышено число неправильных попыток авторизации. Попробуйте повторить через 5 минут.';");
  }
  
  $login = clearField($_POST['login']);
  $password = clearField($_POST['password']);  
    
  if (pc_validate($login, $password)) {
  	$stateid = md5($phone.time());
		$sql = "UPDATE settings SET stateid='$stateid'";
		$db->execute($sql, false);		
		$expire = time()+60*60*24*366;
		setcookie("admingostateid", $stateid, $expire, '/');
  } else {
    die("error='Неправильная пара логин/пароль.';");  
  }  
  
  function pc_validate($user,$pass) {  
    global $config, $db, $ip;
    
    $result = $db->execute("SELECT password FROM settings");
    if ($myrow = mysql_fetch_object($result)) {
    	$password = $myrow->password;
    }     	
      
  	if ($user == $config->admingo_login && md5($pass) == $password) {return true;}
  	else {
  	  $db->execute("INSERT INTO `wronglogin` (ip,login,password) VALUES ('$ip','$user','$pass')");
      return false;
    }
  }

?>