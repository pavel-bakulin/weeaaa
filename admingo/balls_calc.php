<?php
  setlocale(LC_ALL,'ru_RU.UTF-8'); 
  require_once "config.php";
  require_once "db_connect.php";
  require_once "lib.php";
  require_once "login.php";
  
  $orderid = (int)$_REQUEST["orderid"];

  $sql = "SELECT * FROM `orders` WHERE documentid = $orderid";
  $result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
	  $userid = (int)$myrow->userid;
	  $ballsadd = (int)$myrow->ballsadd;
	  $ballsuse = (int)$myrow->ballsuse;
    $sql = "UPDATE `orders` SET ballscalc = 1 WHERE documentid = $orderid";
    $db->execute($sql, false);    
    echo $sql;
     
    $sql = "UPDATE user SET balls = balls + $ballsadd - $ballsuse WHERE documentid = $userid";
    $db->execute($sql, false);
    echo $sql;          
  }
?>