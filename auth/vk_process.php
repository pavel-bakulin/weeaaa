<?php  
  error_reporting(E_ERROR);
  
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/config.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/db_connect.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/lib.php";
  require_once 'vk_init.php';
  require_once 'auth.php';
  global $config;
  header('Content-type: text/html; charset=UTF-8');  
  
  if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id' => $config->vk_client_id,
        'client_secret' => $config->vk_client_secret,
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri
    );

    $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);

    if (isset($token['access_token'])) {
        $params = array(
            'uids'         => $token['user_id'],
            'fields'       => 'uid,first_name,last_name,screen_name,sex,birthdate,photo,photo_big,email',
            'access_token' => $token['access_token']
        );        
        
        $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
        if (isset($userInfo['response'][0]['uid'])) {
            $userInfo = $userInfo['response'][0];
            $result = true;
        }
        $userInfo['email'] = $token['email'];
    }

    if ($result) {
      $data = $userInfo;
      $data['authtype'] = 'vk';
      auth($data);
      $responce = json_decode(auth($data));
      $html = '<li><a href="/u'.$responce->userid.'/" class="profileLink"><img src="'.$responce->image.'"/>'.$responce->title.'</a></li>';
      echo "<script>opener.soc.auth('$html',$responce->userid,'$responce->image','$responce->result',$responce->status);window.close();</script>";            
    } else {
      echo 'Ошибка авторизации';
    } 
  }  
?>