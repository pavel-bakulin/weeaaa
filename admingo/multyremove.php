<?php
  require_once "config.php";
  require_once "db_connect.php";
  require_once "login.php";
  require_once "lib.php";

  $ids = mysql_real_escape_string($_REQUEST['ids']);

  $doctypes = array ("Search"=>"Search","image"=>"images","simple"=>"simple","st"=>"st","material"=>"materials","banner"=>"banners","question"=>"question","goods"=>"goods","orders"=>"orders","siteuser"=>"siteuser");    
        
  $sql = "SELECT DISTINCT doctype FROM alldocs WHERE documentid IN($ids);";
  $result = $db->execute($sql);  
  while ($myrow = mysql_fetch_object($result)){
    $doctypetbl = $doctypes[$myrow->doctype];
		$sql = "DELETE FROM $doctypetbl WHERE documentid IN ($ids)";
		$db->execute($sql, false);    
  }

	$sql = "DELETE FROM alldocs WHERE documentid IN ($ids)";
	$db->execute($sql, false);
  $sql = "DELETE FROM linkeds WHERE docid IN ($ids) OR linkedid IN ($ids);";
  $db->execute($sql, false);    


  header("Location: ok.html");
?>