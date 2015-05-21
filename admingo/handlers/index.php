<?php
  require_once $_SERVER['DOCUMENT_ROOT']."/sxgeo/sxgeo.php";
  header('Content-type: text/html; charset=UTF-8');
  global $config;
  
  $ip = $_SERVER['REMOTE_ADDR'];  
  $SxGeo = new SxGeo($_SERVER['DOCUMENT_ROOT'].'/sxgeo/SxGeoCity.dat');
  $cityInfo = $SxGeo->getCity($ip);
  $lat = $cityInfo['city']['lat'];
  $lon = $cityInfo['city']['lon'];
  if ($lat && $lon) {
    $srNodeFix -> setAttribute("lat", $lat);
    $srNodeFix -> setAttribute("lon", $lon);
  }
    
  
  $sql = "SELECT * FROM user WHERE `status`='FOLLOWER' ORDER BY documentid DESC";
  $result = $db->execute($sql);      
  while ($myrow = mysql_fetch_object($result)) {
		$srNodeU = $srNodeFix -> appendChild($resultXML->CreateElement("DOCUMENT"));
		$srNodeU -> setAttribute("TITLE", $myrow->title);
		$srNodeU -> setAttribute("IID", $myrow->documentid);
    $srNodeU -> setAttribute("URL", '/u'.$myrow->documentid.'/');
    $srNodeU -> setAttribute("FEEDBACKS", $myrow->feedbacks);
    if ((int)$myrow->approval) $srNodeU -> setAttribute("APPROVAL", "TRUE");
		if (strlen($myrow->image)) {
		  $srNodeU -> setAttribute("IMAGE", $config->upfolder.$myrow->documentid.'/multy_'.$myrow->image);
		} else {
		  $srNodeU -> setAttribute("IMAGE", '/img/noava160.gif');
		}    			 		  
  }
  
  if (isset($_COOKIE['hint']) && $_COOKIE['hint']=='hide') {
    $srNodeFix -> setAttribute("hint", 'hide');
  }
?>