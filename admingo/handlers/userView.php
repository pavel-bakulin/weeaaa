<?php
  header('Content-type: text/html; charset=UTF-8');
  
  class Url {
    public $section = '';
    public $userid = '';

    public function __construct($rqpath) {
      $parts = preg_split('/\//',$rqpath);
      $this->userid = (int)substr($parts[0],1,strlen($parts[0])-1);
      if (strlen($parts[1])) $this->section = clearField($parts[1]);         
    }
  }  
  
  class User {
    public function __construct() {
      global $db, $config, $srNodeFix, $resultXML, $url, $currentUserId;        
      $sql = "SELECT *, floor(datediff(now(),birthday)/365.25) AS age FROM `user` WHERE documentid = $url->userid";  		
      $result = $db->execute($sql);            
      if ($myrow = mysql_fetch_object($result)) {
    		$id = $myrow->documentid;
    		$srNodeFix -> setAttribute("iid", $id);
    		$srNodeFix -> setAttribute("title", $myrow->title);
    		if (strlen($myrow->lat)) $srNodeFix -> setAttribute("lat", $myrow->lat);              		
    		if (strlen($myrow->lng)) $srNodeFix -> setAttribute("lng", $myrow->lng);
    		if (strlen($myrow->about)) {$srNodeFix -> setAttribute("about", $myrow->about);}        
    		if (strlen($myrow->birthday)) {
    			$srNodeFix -> setAttribute("birthday", substr($myrow->birthday,8,2).'/'.substr($myrow->birthday,5,2).'/'.substr($myrow->birthday,0,4));
          $srNodeFix -> setAttribute("age", $myrow->age);
        }
        if ($myrow->vkid) $srNodeFix -> setAttribute("vk", 'http://vk.com/id'.$myrow->vkid);
        if ($myrow->fbid) $srNodeFix -> setAttribute("fb", 'https://www.facebook.com/app_scoped_user_id/'.$myrow->fbid.'/');                
        if ($myrow->googleid) $srNodeFix -> setAttribute("google", 'https://plus.google.com/'.$myrow->googleid);        
        if ($myrow->okid) $srNodeFix -> setAttribute("ok", 'http://ok.ru/profile/'.$myrow->okid);
    		if (strlen($myrow->image)) {
    		  $srNodeFix -> setAttribute("image", getImgLink($myrow->image,'big'));
    		} else {
    		  $srNodeFix -> setAttribute("image", '/img/noava160.gif');
    		}    			      
      }   
    }  
    
    public function index() {
      global $db, $config, $srNodeFix, $resultXML, $url, $currentUserId;
      $srNodeN = $srNodeFix -> appendChild($resultXML->createElement("INDEX"));
      
      userObjects($url->userid, $srNodeN);  
    }
  }  

  $currentUserId = $user->userid;
  $url = new Url($_REQUEST['rqpath']);    
  $u = new User();
  if ($url->userid == $currentUserId) {
    $srNodeFix -> setAttribute("my", "TRUE");
  }
  
	switch ($url->section) {     
    default:
      $u->index();
      break;    
	}  
 
?>