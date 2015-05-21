<?php
  setlocale(LC_ALL,'ru_RU.UTF-8'); 
  require_once "config.php";
  require_once "db_connect.php";
  require_once "lib.php";
  require_once "login.php";
  
  $action = clearField($_POST['action']);
  
  if ($action=='add') {
    $login = clearField($_POST['login']);
    $password = clearField($_POST['password']);
    $sections = str_replace(" ","",clearField($_POST['sections']));       
    $db->execute("INSERT INTO `access` (login, password, sid) VALUES ('$login', '$password', '$sections')");
  } else if ($action=='login') {
    $iid = (int)$_POST['iid'];
    $val = clearField($_POST['val']);
    $db->execute("UPDATE `access` SET login = '$val' WHERE id = $iid");
  } else if ($action=='password') {
    $iid = (int)$_POST['iid'];
    $val = clearField($_POST['val']);
    $db->execute("UPDATE `access` SET password = '$val' WHERE id = $iid");
  } else if ($action=='sid') {
    $iid = (int)$_POST['iid'];
    $val = str_replace(" ","",clearField($_POST['val']));
    $db->execute("UPDATE `access` SET sid = '$val' WHERE id = $iid");
  } else if ($action=='remove') {
    $iid = (int)$_POST['iid'];
    $db->execute("DELETE FROM `access` WHERE id = $iid");
  } else if ($action=='getnames') {
    $sids = clearField($_POST['sids']);
		$sql = "SELECT id,name FROM sections WHERE id IN ($sids)";
		$result = $db->execute($sql);
    $return = '';		
		while ($myrow = mysql_fetch_object($result)) {
		  $return .= "$myrow->id: $myrow->name \n";  
		}
		echo $return; 
  }
    
?>