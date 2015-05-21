<?php
  global $config, $currentRegion, $user;
  require_once "admingo/config.php";
  require_once "admingo/get_geo.php";       
	require_once "admingo/db_connect.php";
	require_once "admingo/processxml.php";
  if (!$user->logined) {
    require_once 'auth/fb_init.php';      
    require_once 'auth/vk_init.php';
    require_once 'auth/google_init.php';
    require_once 'auth/ok_init.php';
  }  
	$globals = array();  	
    
  if (isset($_REQUEST['rqpath']) && (preg_match("/^\/u[0-9]+\//i", '/'.$_REQUEST['rqpath'], $matches))) {
      $resultXML = new DOMDocument('1.0');
      $resultXML->loadXML('<USER/>');
      $srNodeFix = $resultXML->firstChild;      
  		  		  			           
      include_once "admingo/handlers/userView.php";        		

      $contentXML = $resultXML; 
  		$stid = 9;
  } else if (isset($_REQUEST['rqpath']) && (preg_match("/^\/object[0-9]+/i", '/'.$_REQUEST['rqpath'], $matches))) {
      $resultXML = new DOMDocument('1.0');
      $resultXML->loadXML('<OBJECT/>');
      $srNodeFix = $resultXML->firstChild;      
  		  		  			           
      include_once "admingo/handlers/objectView.php";        		

      $contentXML = $resultXML; 
  		$stid = 9;              
  } else if (isset($_REQUEST['rqpath']) && (preg_match("/^\/profile\/*/i", '/'.$_REQUEST['rqpath'], $matches))) {
      $resultXML = new DOMDocument('1.0');
      $resultXML->loadXML('<PROFILE/>');
      $srNodeFix = $resultXML->firstChild;      
  		  		  			           
      include_once "admingo/handlers/profileView.php";        		

      $contentXML = $resultXML; 
  		$stid = 9;
  } else if (isset($_REQUEST['rqpath']) && (preg_match("/^\/all\/*/i", '/'.$_REQUEST['rqpath'], $matches))) {
      $resultXML = new DOMDocument('1.0');
      $resultXML->loadXML('<ALL/>');
      $srNodeFix = $resultXML->firstChild;      
  		  		  			           
      include_once "admingo/handlers/allView.php";        		

      $contentXML = $resultXML; 
  		$stid = 9;  		
  } else {          	       	
  	if (isset($_REQUEST['sid']) && isset($_REQUEST['rqpath'])) {
      f404(0);
  	} 
  	else if (isset($_REQUEST['id'])) {
  		$id=(int)$_REQUEST['id'];
  		if (strlen($id.'') != strlen($_REQUEST['id'])) {f404(1);}
      
      $sql = "SELECT rqpath FROM alldocs WHERE documentid = $id";
      $result = $db->execute($sql);
      if ($myrow = mysql_fetch_object($result)) {
    		  if (strlen($myrow->rqpath)>0) {f404(9);}
    	}
      
  		if (isset($_REQUEST['sid'])) {
  		  $sid=intval($_REQUEST['sid']);
  		  if (strlen($sid.'')!=strlen($_REQUEST['sid'])) {f404(2);}		  
  		}				
  		else {
  		  if (isset($_REQUEST['rqpath'])) {		    
  		    $rqpath = '/'.mysql_real_escape_string($_REQUEST['rqpath']);
      		$sql = "SELECT * FROM sections WHERE rqpath = '$rqpath'";
      		$result = $db->execute($sql);
      		if ($myrow = mysql_fetch_object($result)) {
            $sid = $myrow->id; 
      		}
  		  }
  		}
  	}
  	else if (isset($_REQUEST['rqpath'])) {
      $rqpath = '/'.mysql_real_escape_string($_REQUEST['rqpath']);
      $sql = "SELECT * FROM alldocs WHERE rqpath = '$rqpath'";
      $result = $db->execute($sql);
      if (mysql_num_rows($result) > 0) {
    		if ($myrow = mysql_fetch_object($result)) {
    		  if (isset($_REQUEST['id'])) {f404(6);} //неправильно, если у документа есть и rqpath и id передан
    		  $id = $myrow->documentid;
          $sid = $myrow->sid; 
    		}
      }
      else {
    		$sql = "SELECT * FROM sections WHERE rqpath = '$rqpath'";
    		$result = $db->execute($sql);
    		if (mysql_num_rows($result) == 0) {f404(4);}
    		if ($myrow = mysql_fetch_object($result)) {
    		  $id = $myrow->rootdocid;
          $sid = $myrow->id; 
          if (!($id > 0)) {f404(7);}
    		}
    	}
  	}
  	
  	if (!($id > 0)) {
  		$id = (int)$config->default_id;
  		$sid = (int)$config->default_sid;
      if (!$id) {
    		$sql = "SELECT rootdocid FROM sections WHERE id = $sid";
    		$result = $db->execute($sql);
    		if ($myrow = mysql_fetch_object($result)) {
    		  $id = $myrow->rootdocid; 
          if (!($id > 0)) {f404(11);}
    		}    
      }		
  	}
  	$closed_sid = (int)$config->closed_sid;
  	if ($closed_sid == $sid && $sid) {
      f404(10);
    }
  
  	$globals['id'] = $id;	
  	$doctype=getDocType($id); //определяем тип документа
  
    switch ($doctype) {
        case 'image':
      		$sql = "SELECT `image`, `preview`, `bigimage` FROM `images` WHERE `documentid`=$id";
      		$result = $db->execute($sql);
      		
      		if ($myrow = mysql_fetch_row($result))
      		{
      			if (isset($_REQUEST['bigimage']) && $_REQUEST['bigimage']=='true') {
      				$imagepath = $myrow[2];
      			} else if (isset($_REQUEST['preview']) && $_REQUEST['preview']=='true') {
      				$imagepath = $myrow[1];
      			} else {
      				$imagepath = $myrow[0];
      			}
      			$imagepath = "uploads/".$imagepath;
      			$f=fopen($imagepath,"rb");	
      			$image=fread($f,filesize($imagepath));
      			header("Content-type: image/jpeg");
      			echo $image;
      			exit;
      		}
          break;
        case 'file':
      		$sql = "SELECT `link` FROM `file` WHERE `documentid`=$id";
      		$result = $db->execute($sql);
      		
      		if ($myrow = mysql_fetch_object($result))
      		{			
      			$link = $config->upfolder.$myrow->link;	
      			header("Location: $link");
      			exit;
      		}
          break;
        default:
           $contentXML = processDoc($globals['id']);
    }	
  
  	//получаем структурник
  	if (isset($_REQUEST['stid'])) {
  	 $stid = (int)$_REQUEST['stid'];
  	}
  	else if (isset($_COOKIE['stid'])) {
  	 $stid = (int)$_COOKIE['stid'];
  	}
  	else if (isset($_SESSION['stid']) && (int)$_SESSION['stid'] > 0 ) {
  	 $stid = (int)$_SESSION['stid'];
  	}
  	else {
    	$sql = "SELECT STID FROM alldocs WHERE documentid=$id";
    	$result = $db->execute($sql);
    	if ($myrow = mysql_fetch_row($result))
    	{
    		$stid = $myrow[0];
    	}
    }
  }
    
	if (isset($stid))
	{
	  if ($stid>0) {
  		$sql = "SELECT xsl, content FROM st WHERE documentid=$stid";
  		$result = $db->execute($sql);
  		if ($myrow = mysql_fetch_row($result))
  		{
  			$xslpath = $myrow[0];
  			$inputstxml = $myrow[1];
  		}
  		$stxml = new DOMDocument('1.0');
  		$stxml->loadXML($inputstxml);  		
  	}
  	else {
  		$stxml = new DOMDocument('1.0');
  		$stxml->loadXML('<CONTENT/>');
  	}
		
		//обрабатываем структурник
		$resultXML = new DOMDocument('1.0');
		$root = $stxml->firstChild;
		$resultRoot = $resultXML;
		processXML($root, $resultRoot);
		
		$root = $resultXML->firstChild;		
		$root -> setAttribute("IID", $id);
		$root -> setAttribute("DOCTYPE", $doctype);
		$root -> setAttribute("USERAGENT", $_SERVER['HTTP_USER_AGENT']);
		$root -> setAttribute("SERVERNAME", $_SERVER['SERVER_NAME']);
		$root -> setAttribute("URL", $_SERVER['REQUEST_URI']);
		if ($sid) {$root -> setAttribute("SECTIONID", $sid);}
		$root -> setAttribute("YEAR", date(o));
			
		//вставляем контент в xml стуктурника
		$xpath = new DOMXPath($resultXML);
		$CONTENT = $xpath->query('//CONTENT')->item(0);
		$cont = $resultXML->importNode($contentXML->firstChild,true);
		$CONTENT -> appendChild($cont);
	}
	
	if ((isset($_GET['for']) && $_GET['for']=="xml") || $stid==0)
	{
		header("Content-type: text/xml");
		$resultXML -> formatOutput = true;
		print $resultXML->saveXML();
	}
	else
	{  
	  header('Content-type: text/html; charset=UTF-8');
		$xsl = new DOMDocument;
	    $xsl->substituteEntities = true;
	    $xsl->resolveExternals = true;	    
	  	$xsl->load($config->cms."/xsl/".$xslpath);
			
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xsl);	
		
	    $result = $xslt->transformToXML($resultXML);   
	    
	    $result = html_entity_decode($result);	    
	    echo $result;
      //echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
      //echo 'offset: ' . $_SESSION['offset'] . '<br />';
	}
	
  function Clear_array_empty($array) {
    $ret_arr = array();
    foreach($array as $val){
        if (!empty($val)) {$ret_arr[] = trim($val);}
    }
    return $ret_arr;
  } 
?>