<?php
  session_start();
  define('FACEBOOK_SDK_V4_SRC_DIR',  $_SERVER['DOCUMENT_ROOT'].'/facebook-php-sdk-v4-4.0-dev/src/Facebook/');
  require  $_SERVER['DOCUMENT_ROOT'].'/facebook-php-sdk-v4-4.0-dev/autoload.php';
  use Facebook\FacebookSession;
  use Facebook\FacebookRequest;
  use Facebook\GraphUser;
  use Facebook\FacebookRequestException;
  FacebookSession::setDefaultApplication($config->fb_app_id,$config->fb_secret);
  $facebook = new Facebook\FacebookRedirectLoginHelper('http://'.$_SERVER['SERVER_NAME'].'/auth/fb_process.php');

  try {
   if($session = $facebook->getSessionFromRedirect()) {
    $_SESSION['facebook'] = $session->getToken();
    header('Location fburl.php');
   }

   if(isset($_SESSION['facebook'])) {
    $session = new Facebook\FacebookSession($_SESSION['facebook']);
    $request = new Facebook\FacebookRequest($session, 'GET', '/me?fields=id,location,gender,birthday,email,name,picture');
    $request = $request->execute();
    $user = $request->getGraphObject()->asArray();
   }

  } catch(Facebook\FacebookRequestException $e) {
   // если facebook вернул ошибку
  } catch(\Exception $e) {
   // Локальная ошибка
  }
?>