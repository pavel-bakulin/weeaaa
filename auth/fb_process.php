<?php  
  error_reporting(E_ERROR);
  
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/config.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/db_connect.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/lib.php";
  require_once 'fb_init.php';
  require_once 'auth.php';
  global $config;
  header('Content-type: text/html; charset=UTF-8');  
  
  if(isset($_SESSION['facebook'])) {
    $data = $user;
    $data['authtype'] = 'fb';
    auth($data);
    $responce = json_decode(auth($data));
    $html = '<li><a href="/u'.$responce->userid.'/" class="profileLink"><img src="'.$responce->image.'"/>'.$responce->title.'</a></li>';
    echo "<script>opener.soc.auth('$html',$responce->userid,'$responce->image','$responce->result',$responce->status);window.close();</script>";    
  } else {
    echo 'Ошибка авторизации';
  } 
    
?>