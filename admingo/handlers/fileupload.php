<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');  
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
		}
	}
	if (!$userid) die();	  

  $action = $_REQUEST['action'];   	

  $id = md5(uniqid(rand(), true));      
  $filename = $_FILES['uploadfile']['name'];
  $fext = substr($filename, strrpos($filename,'.'));
  $result = $id.$fext;  
  $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder.substr($id,0,2).'/';
  mkdir($uploaddir, 0755);    

  if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploaddir.$result)) die('Ошибка при загрузке файла');  
  
  echo $result;
?>