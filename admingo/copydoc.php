<?php
require_once "config.php";
require_once "db_connect.php";
require_once "login.php";
require_once "lib.php";

function documentCopy($copydocid) {
  global $doctypes, $db;
  global $sid;
  $sid = $_REQUEST['sid'];
  if (!$sid) $sid = 1;

  $sql = "SELECT doctype FROM alldocs WHERE documentid=$copydocid";
  $result = $db->execute($sql);
  if ($myrow = mysql_fetch_object($result)){
    $doctype = $myrow->doctype;
  }
  else {
    die("<font color='red'>Документа с таким номером не существует.</font>");
  }
  
  $doctypes = array ("Search"=>"Search","image"=>"images","simple"=>"simple","st"=>"st","material"=>"materials","banner"=>"banners","question"=>"question","goods"=>"goods","orders"=>"orders","siteuser"=>"siteuser","forum"=>"forum","form"=>"form","record"=>"record","mailform"=>"mailform");    
  $doctypetbl = $doctypes[$doctype];
      
  $lastid = getID();
  $stid = getSTID();
  
  $copysql = "";
  $sql="DESC $doctypetbl";
  $result = $db->execute($sql);
  while ($myrow = mysql_fetch_object($result)){
    $fname = $myrow->Field;
    if ($fname!='documentid')
      $copysql .= ", $fname";
  }
  
  $copysql = "INSERT INTO $doctypetbl (documentid $copysql) SELECT $lastid $copysql FROM $doctypetbl WHERE documentid=$copydocid";
  $db->execute($copysql);
  $sql = "SELECT title FROM $doctypetbl WHERE documentid=$copydocid";
  $result = $db->execute($sql);
  if ($myrow = mysql_fetch_object($result)){
    $TITLE = mysql_real_escape_string($myrow->title);
  }
  
  $sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$TITLE', '$doctype', $stid)";
  $db->execute($sql, false);
  
  $sql = "INSERT INTO linkeds (docid, linkedid, doctype, title)
          SELECT $lastid as docid, linkedid, doctype, title 
          FROM linkeds
          WHERE docid=$copydocid";
  $db->execute($sql, false);
}

$copydocid = $_REQUEST['copydocid'];
if (strpos($copydocid,',')!==false) {
  $ids = array_reverse(preg_split('/,/',$copydocid));
  for ($i=0; $i<count($ids); $i++) {
    documentCopy((int)$ids[$i]);
  }
} else {
  documentCopy((int)$copydocid);
}

setcookie('copydocid','',time()-3600);
header("Location: ok.html");
?>