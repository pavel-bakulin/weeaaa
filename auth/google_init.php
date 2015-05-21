<?php
  $client_id = "384688347328-3mmc99vmumd1q19dbk8bbl6dsmfbsupi.apps.googleusercontent.com"; //your client id
  $client_secret = "p11s76s_-6M5j6t0qnPG5AY0"; //your client secret
  $redirect_uri = 'http://'.$_SERVER['SERVER_NAME'].'/auth/google_process.php'; 
  $scope = "https://www.googleapis.com/auth/userinfo.profile"; //google scope to access
  $state = "profile"; //optional
  $access_type = "offline"; //optional - allows for retrieval of refresh_token for offline access

  $google_link = sprintf("https://accounts.google.com/o/oauth2/auth?scope=%s&state=%s&redirect_uri=%s&response_type=code&client_id=%s&access_type=%s", $scope, $state, $redirect_uri, $client_id, $access_type);
?>