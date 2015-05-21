<?php
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $action = $_POST['action'];
  
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid,action_read FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) { 
			$userid = $myrow->documentid;
			$action_read = $myrow->action_read;
		}
	}  
	if (!$userid) {
		$result = array ('result'=>false,'msg'=>'Необходимо авторизоваться');
	  echo json_encode($result);
	  return;
	}  
  
  if ($action=='read') {
    $id = (int)$_POST['id'];
    if ($id) {
      $sql = "UPDATE user SET action_read=$id WHERE documentid = $userid";
      $db->execute($sql,false);
    }
  } else if ($action=='readall') {
  
  } else if ($action=='getlist') {
    $resultXML = new DOMDocument('1.0');
    $resultXML->loadXML('<EVENT_LIST/>');
    $srNodeFix = $resultXML->firstChild;    
        
    $start = (int)$_POST['start'];
    $count = (int)$_POST['count'];
    if ($start && $count) {
  	  eventList($userid, $srNodeFix, $action_read, $start, $count);
  	} else {
  	  eventList($userid, $srNodeFix, $action_read);
  	}
    
  	$xsl = new DOMDocument;
    $xsl->substituteEntities = true;
    $xsl->resolveExternals = true;	    
  	$xsl->load("../xsl/site.xsl");
      			
  	$xslt = new XSLTProcessor();
  	$xslt->importStylesheet($xsl);	  		
    $result = $xslt->transformToXML($resultXML);     	    
    $result = html_entity_decode($result);
    
    echo $result;
  } else if ($action=='autoload') {
        
  }
?>