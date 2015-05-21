<?php  
  //error_reporting(E_ERROR);
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/config.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/db_connect.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/lib.php";
  require_once 'ok_init.php';
  require_once 'auth.php';
  global $config;
  
  header('Content-type: text/html; charset=UTF-8');  
  if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret
    );
    $url = 'http://api.odnoklassniki.ru/oauth/token.do';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);

    $tokenInfo = json_decode($result, true);

    if (isset($tokenInfo['access_token']) && isset($public_key)) {
      $sign = md5("application_key={$public_key}format=jsonmethod=users.getCurrentUser" . md5("{$tokenInfo['access_token']}{$client_secret}"));
      $params = array(
          'method'          => 'users.getCurrentUser',
          'access_token'    => $tokenInfo['access_token'],
          'application_key' => $public_key,
          'format'          => 'json',
          'sig'             => $sign
      );
      $userInfo = json_decode(file_get_contents('http://api.odnoklassniki.ru/fb.do' . '?' . urldecode(http_build_query($params))), true);
      if (isset($userInfo['uid'])) {
        $result = true;
      }
      
      if ($result) {
        //var_dump($userInfo);     
        $data = (array)$userInfo;
        $data['authtype'] = 'ok';
        auth($data);
        $responce = json_decode(auth($data));
        $html = '<li><a href="/u'.$responce->userid.'/" class="profileLink"><img src="'.$responce->image.'"/>'.$responce->title.'</a></li>';
        echo "<script>opener.soc.auth('$html',$responce->userid,'$responce->image','$responce->result',$responce->status);window.close();</script>";      
      } else {
        echo 'Ошибка авторизации';
      }      
      
    }
    
  }
  


?>