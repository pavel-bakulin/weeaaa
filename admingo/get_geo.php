<?php  
  require_once $_SERVER['DOCUMENT_ROOT']."/sxgeo/sxgeo.php";
  $ip = $_SERVER['REMOTE_ADDR'];
  //$ip = '109.184.171.121';
  
  $SxGeo = new SxGeo($_SERVER['DOCUMENT_ROOT'].'/sxgeo/SxGeoCity.dat');
  $cityInfo = $SxGeo->getCity($ip);
  $lat = $cityInfo['city']['lat'];
  $lon = $cityInfo['city']['lon'];
  if ($lat && $lon) {
    $_SESSION['user_lat'] = $lat;
    $_SESSION['user_lon'] = $lon;
    $s = 'https://maps.googleapis.com/maps/api/timezone/json?location=' . $lat . ',' . $lon . '&timestamp=' . time() . '&key=AIzaSyBzmM9v10lHP5Kb-IjD3UjCOKGuGx8xXLA';
    $arr = json_decode(file_get_contents($s),true);
    $_SESSION['timezone'] = $arr['timeZoneId'];
    //$_SESSION['timezone'] = "Africa/Tunis";
    $_SESSION['offset'] = ($arr['dstOffset'] + $arr['rawOffset']) / 3600;
    if(($arr['dstOffset'] + $arr['rawOffset'])%3600/60 == 30){
      $_SESSION['offset'].=":30";
    } else {
      $_SESSION['offset'].=":00";
    }
    date_default_timezone_set($_SESSION['timezone']);    
  }
?>