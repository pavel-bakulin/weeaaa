<?php
  require_once "lib_handlers.php";
  global $config, $user, $facebook, $vk_link, $google_link, $ok_link;    
		
	if (isset($_GET["stateid"]) && $_GET["stateid"]=="exit") {
		setcookie('stateid','',time()-3600, '/');
		if ($user->logined) {
  		$sql = "UPDATE user SET stateid = '' WHERE documentid = $user->userid";
  		$db->execute($sql, false); 
		}
		header("Location: /"); 
	}
	else {	  
	  if ($user->logined) { 
  		$srNodeFix2 = $srNodeFix -> appendChild($resultXML->CreateElement("DOCUMENT"));
			$srNodeFix2 -> setAttribute("EMAIL", $user->email);
			$srNodeFix2 -> setAttribute("IID", $user->userid);		 
			$srNodeFix2 -> setAttribute("TITLE", $user->title);
			if (strlen($user->image)) {
			 $srNodeFix2 -> setAttribute("IMAGE_SMALL", getImgLink($user->image,''));
			} else {	 
			 $srNodeFix2 -> setAttribute("IMAGE_SMALL", '/img/noava40.gif');
			}			
			$srNodeFix2 -> setAttribute("URL", '/lk');		
      $srNodeFix2 -> setAttribute("STATUS", $user->status);
      //есть ли личные сообщения	 
      $sql = "SELECT userid FROM private_messages WHERE adresatid = $user->userid AND `read`= 0 AND removed=0";
    	$result = $db->execute($sql); 
    	while ($myrow = mysql_fetch_object($result)) {
    		$srNodeFix2 = $srNodeFix -> appendChild($resultXML->CreateElement("NEWMESS"));
    		$srNodeFix2 -> setAttribute("USERID", $myrow->userid); 
    	}      		
    	
    	//непрочитанные события
    	$srNodeFix2 -> setAttribute("NEWEVENTS", eventList($user->userid, $srNodeFix, $user->action_read));
  	}
    if (isset($facebook)) $srNodeFix -> setAttribute("FB_AUTH", $facebook->getLoginUrl());
    if (isset($vk_link)) $srNodeFix -> setAttribute("VK_AUTH", $vk_link);
    if (isset($google_link)) $srNodeFix -> setAttribute("G_AUTH", $google_link);
    if (isset($ok_link)) $srNodeFix -> setAttribute("OK_AUTH", $ok_link);        
	}
?>