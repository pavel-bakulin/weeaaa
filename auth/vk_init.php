<?php
  /*$client_id = '4665302'; 
  $client_secret = 'wELUDT5tG98EKtqXSerO';*/ 
  
  $redirect_uri = 'http://'.$_SERVER['SERVER_NAME'].'/auth/vk_process.php'; 
  $url = 'http://oauth.vk.com/authorize';

  $params = array(
      'client_id'     => $config->vk_client_id,
      'redirect_uri'  => $redirect_uri,
      'response_type' => 'code',
      'scope'         => 'email'
  );

  $vk_link = $url.'?'.urldecode(http_build_query($params));
?>