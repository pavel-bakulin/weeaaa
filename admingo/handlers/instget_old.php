<?php
  header('Content-type: text/html; charset=UTF-8');
     
  function instget($lat, $lng, $radius) { 
    $obj = file_get_contents('https://api.instagram.com/v1/media/search?lat=' . $lat . '&lng=' . $lng . '&distance=' . $radius . '&client_id=e6a91043126244df9fecf644efddd581' );
    $obj= json_decode($obj);
    $res = $obj -> data;
    return json_encode($res);
  }
?>