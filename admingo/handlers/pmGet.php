<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  global $config;
  header('Content-type: text/html; charset=UTF-8');
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT * FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
		}
	}
	if (!$userid) die();	    
  $touserid = (int)$_REQUEST['touserid'];
  $lastMessId = (int)$_REQUEST['lastMessId'];
  
  $resultXML = new DOMDocument('1.0');
  $resultXML->loadXML('<PM_GET/>');
  $srNodeFix = $resultXML->firstChild;  
                    
  $sql = "SELECT * FROM private_messages 
          WHERE removed=0 AND ((adresatid = $userid AND userid = $touserid) OR (adresatid = $touserid AND userid = $userid)) AND id>$lastMessId";
  $result = $db->execute($sql);
	while ($myrow = mysql_fetch_object($result)) {  
  	$srNodeN = $srNodeFix -> appendChild($resultXML->CreateElement("MESS"));
  	$srNodeN -> setAttribute("IID", $myrow->id);
  	$srNodeN -> setAttribute("USERID", $myrow->userid);
  	$srNodeN -> setAttribute("USERNAME", $myrow->username);
  	$srNodeN -> setAttribute("ADRESATID", $myrow->adresatid);
  	$srNodeN -> setAttribute("ADRESATNAME", $myrow->adresatname);
  	$srNodeN -> setAttribute("CONTENT", $myrow->content);
  	$srNodeN -> setAttribute("DATE", $myrow->date);
  	if (strlen($myrow->userimage)) {
  	  $srNodeN -> setAttribute("IMAGE", getImgLink($myrow->userimage,''));
  	} else {
  	  $srNodeN -> setAttribute("IMAGE", '/img/noava40.gif');
  	}          
  	if (strlen($myrow->file)) {
  	 $files = preg_split('/,/', $myrow->file);
  	 $file_names = preg_split('/,/', $myrow->filename);
  	 for ($i=0;$i<count($files);$i++) {
  	   $srNodeF = $srNodeN -> appendChild($resultXML->CreateElement("FILE"));
  	   $srNodeF -> setAttribute("NAME", htmlspecialchars(iconv(mb_detect_encoding($file_names[$i]),'utf-8',$file_names[$i])));
  	   $srNodeF -> setAttribute("URL", getImgLink(htmlentities($files[$i]),''));
  	   $ext = strtolower(substr(iconv('UTF-8','UTF-8',htmlentities($files[$i])),strlen(iconv('UTF-8','UTF-8',htmlentities($files[$i])))-4,4));
  	   if ($ext == '.png' || $ext == '.gif' || $ext == '.jpg') $srNodeF -> setAttribute("TYPE", "IMG");  	   
  	 }
  	}
  	$srNodeN -> setAttribute("MY", ($myrow->userid == $userid)?'TRUE':'FALSE');
  }      
  
  if ($touserid) {
    $sql = "UPDATE private_messages SET `read`=1 
            WHERE adresatid=$userid AND userid=$touserid AND `read`=0";
    $db->execute($sql, false);  
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
?>