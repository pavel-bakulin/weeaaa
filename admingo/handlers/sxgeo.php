<?php
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/sxgeo/sxgeo.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $ip = $_SERVER['REMOTE_ADDR'];
  //$ip = '109.184.171.121';
  $SxGeo = new SxGeo($_SERVER['DOCUMENT_ROOT'].'/sxgeo/SxGeoCity.dat');
  $cityInfo = $SxGeo->getCity($ip);
  $lat = $cityInfo['city']['lat'];
  $lon = $cityInfo['city']['lon'];
  
  echo $lat.','.$lon; 
?>
 