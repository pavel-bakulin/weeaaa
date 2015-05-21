<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";    
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
    
  $action = $_REQUEST['action'];
	
	switch ($action) {
    case 'registration':
      $size = getimagesize($_FILES['uploadfile']['tmp_name']);
      $width = $size[0];
      $height = $size[1];
      if ($width<160 || $height<160 || $width>4000 || $height>4000 || $width*2<$height || $height*2<$width) {
        echo 'error';
        return;
      } 
      $imageid = md5(uniqid(rand(), true));
      $result = imageUpload('uploadfile', 160, $imageid, '', $userid, false,'');
      imageSquare($result, $userid, 40, 'small_');
      imageSquare($result, $userid, 263, 'multy_');
      break;      
    case 'crop':
      $image = $_REQUEST['image'];
      $x1 = (int)$_REQUEST['x1'];
      $y1 = (int)$_REQUEST['y1']; 
      $w = (int)$_REQUEST['w'];
      $h = (int)$_REQUEST['h'];
      $crop_width_multy = (int)$_REQUEST['crop_width_multy']; 
      $crop_height_multy = (int)$_REQUEST['crop_height_multy'];
      $crop_width_small = (int)$_REQUEST['crop_width_small']; 
      $crop_height_small = (int)$_REQUEST['crop_height_small'];      
      
      imageCropp($image, $userid, $x1, $y1, $w, $h, $crop_width_small, $crop_height_small, 'small_');
      imageCropp($image, $userid, $x1, $y1, $w, $h, $crop_width_multy, $crop_height_multy, 'multy_');      
      break;
    case 'object':
      $imageid = md5(uniqid(rand(), true));  
      $result = imageUpload('uploadfile', 600, $imageid, '', false);
      break;            
	}
  
  echo $result;
?>