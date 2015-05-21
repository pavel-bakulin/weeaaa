<?php
$client_id = '1121473280'; // Application ID
$public_key = 'CBAEFJNDEBABABABA'; // your public key
$client_secret = '86AA45AB2F260FF165291BAB'; // your client secret
$redirect_uri = 'http://weeaaa.ru/auth/ok_process.php'; // redirect

$url = 'http://www.odnoklassniki.ru/oauth/authorize';
$params = array(
    'client_id'     => $client_id,
    'response_type' => 'code',
    'redirect_uri'  => $redirect_uri,
    
);
$ok_link = $url . '?' . urldecode(http_build_query($params));
?>