<?php  
  require_once "config.php";
  require_once "db_connect.php";
  require_once "login.php";
  header('Content-type: text/html; charset=UTF-8');

  $action = $_REQUEST['action'];
  $id = (int)$_REQUEST['id'];  
  $title = $_REQUEST['title'];  
    
  switch ($action) {
    case 'edit':
    	$sql = "UPDATE mark SET title='$title' WHERE id=$id";
    	$db->execute($sql, false);
    	echo $id;
    	break;
    case 'add':
    	$sql = "INSERT INTO mark (title) VALUES('$title')";
   	  $db->execute($sql, false);  
    	break;    	
    case 'delete':
    	$sql = "DELETE FROM mark WHERE id=$id";
    	$db->execute($sql, false);    	
    	break;    	
  }
  
?>