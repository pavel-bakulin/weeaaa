<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  require_once "socnet_get.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $query = '';
  $count_record = 40;
  $lat = clearField($_REQUEST['lat']);
  $lng = clearField($_REQUEST['lng']);
  $tag = clearField($_REQUEST['tag']);
  $radius = (int)$_REQUEST['radius'];
  $period = clearField($_REQUEST['period']);  
  $apioff = clearField($_REQUEST['apioff']);
  $my = (int)$_REQUEST['my'];  
  $zoom = (int)$_REQUEST['zoom'];
  $text = clearField($_REQUEST['text']);
  
  echo socnet_get($count_record, $lat, $lng, $radius, $period, $apioff, $my, $zoom, $tag, $text);
      
?>
 