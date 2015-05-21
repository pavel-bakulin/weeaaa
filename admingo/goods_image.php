<?php
  require_once "config.php";
  require_once "db_connect.php";
  require_once "login.php";
  require_once "lib.php";
  global $config;
  
  header('Content-type: text/html; charset=UTF-8');
    
  $action = $_REQUEST['action'];

  if ($action=='image') {
    $imageid = md5(uniqid(rand(), true));  
    $result = imageUpload('uploadfile', 0, $imageid, '', -1, false);    
    echo $result;
  } else if ($action=='goodsimage') {
    $imageid = md5(uniqid(rand(), true));  
    $result = imageUpload('uploadfile', 248, $imageid, '', -1, false);
    /*imageUpload('uploadfile', 212, $imageid, 'multy_', -1, false);
    imageUpload('uploadfile', 800, $imageid, 'big_', -1, false);*/    
    echo $result;
  } else if ($action=='material') {
    $imageid = md5(uniqid(rand(), true));  
    $result = imageUpload('uploadfile', 134, $imageid, '', -1, false);
    imageUpload('uploadfile', 317, $imageid, 'big_', -1, false);    
    echo $result;        
  } else if ($action=='file') {
    $filename = md5(uniqid(rand(), true)).substr($_FILES['uploadfile']['name'], strrpos($_FILES['uploadfile']['name'],'.'));
    $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
    copy($_FILES['uploadfile']['tmp_name'], $uploaddir.$filename);
    echo $filename;
  } else if ($action=='title') {
    $imgid = (int)$_REQUEST['imgid'];
    $title = $_REQUEST['title'];
  	$sql = "UPDATE image_owner SET title='$title' WHERE id = $imgid";
  	$db->execute($sql, false);
  } else if ($action=='sort') {
    $imgid = (int)$_REQUEST['imgid'];
    $sort = (int)$_REQUEST['sort'];
  	$sql = "UPDATE image_owner SET sort=$sort WHERE id = $imgid";
  	$db->execute($sql, false);  	
  } else if ($action=='remove') {
    $imgid = (int)$_REQUEST['imgid'];
  	$result = $db->execute("SELECT * FROM image_owner WHERE id = $imgid");
  	if ($myrow = mysql_fetch_object($result)) {
      /*unlink($_SERVER['DOCUMENT_ROOT'].$config->upfolder.$myrow->image);        
      unlink($_SERVER['DOCUMENT_ROOT'].$config->upfolder.'small_'.$myrow->image);
      unlink($_SERVER['DOCUMENT_ROOT'].$config->upfolder.'big_'.$myrow->image);*/
    	$sql = "DELETE FROM image_owner WHERE id = $imgid";
    	$db->execute($sql, false);    
    }    
  } else if ($action=='gallery') {
    $docid = (int)$_REQUEST['docid'];
    $imageid = md5(uniqid(rand(), true));  
    $result = imageUpload('uploadfile', 419, $imageid, '', -1, false);
    imageUpload('uploadfile', 101, $imageid, 'small_', -1, false);
    
  	$sql = "INSERT INTO image_owner(docid, image) VALUES ($docid, '$result')";
  	$db->execute($sql, false);	  	
        
    echo $result;   
  }   
    
?>