<?php  
  //error_reporting(E_ERROR);
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/config.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/db_connect.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/admingo/lib.php";
  require_once 'google_init.php';
  require_once 'auth.php';
  global $config;
  
  header('Content-type: text/html; charset=UTF-8');  
  if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $oauth2token_url = "https://accounts.google.com/o/oauth2/token";
    $clienttoken_post = array(
      "code" => $code,
      "client_id" => $client_id,
      "client_secret" => $client_secret,
      "redirect_uri" => $redirect_uri,
      "grant_type" => "authorization_code"
    );
     
    $curl = curl_init($oauth2token_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $json_response = curl_exec($curl);
    curl_close($curl);
 
    $authObj = json_decode($json_response);
     
    if (isset($authObj->refresh_token)){
      //refresh token only granted on first authorization for offline access
      //save to db for future use (db saving not included in example)
      global $refreshToken;
      $refreshToken = $authObj->refresh_token;
    }
               
    $accessToken = $authObj->access_token;

    //calls api and gets the data
    function call_api($accessToken,$url){
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	
      $curlheader[0] = "Authorization: Bearer " . $accessToken;
      curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);
      $json_response = curl_exec($curl);
      curl_close($curl);
      $responseObj = json_decode($json_response);
      return $responseObj;
    }
  
    $result = false;
    if (isset($accessToken)){
      $accountObj = call_api($accessToken,"https://www.googleapis.com/oauth2/v1/userinfo");
      //$your_name =  $accountObj->name;
      if (isset($accountObj->id)) {
        $userInfo = $accountObj;
        $result = true;
      }
    }  
    
    if ($result) {
      //var_dump($userInfo);     
      $data = (array)$userInfo;
      $data['authtype'] = 'google';
      auth($data);
      $responce = json_decode(auth($data));
      $html = '<li><a href="/u'.$responce->userid.'/" class="profileLink"><img src="'.$responce->image.'"/>'.$responce->title.'</a></li>';
      echo "<script>opener.soc.auth('$html',$responce->userid,'$responce->image','$responce->result',$responce->status);window.close();</script>";      
    } else {
      echo 'Ошибка авторизации';
    }   
  }
  


?>