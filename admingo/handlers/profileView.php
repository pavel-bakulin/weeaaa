<?php
  header('Content-type: text/html; charset=UTF-8');
  
  class Url {
    public $section = '';
    public $item = 0;

    public function __construct($rqpath) {
      $parts = preg_split('/\//',$rqpath);
      if (strlen($parts[1])) $this->section = clearField($parts[1]);
      if (strlen($parts[2])) $this->item = (int)$parts[2];         
    }
  }  
  
  class Profile {
    public function __construct() {
      global $db, $config, $srNodeFix, $resultXML, $url, $user;        
      
      if (!$user->userid) $srNodeFix -> setAttribute("logined", "false");
      else {
    		$srNodeFix -> setAttribute("iid", $user-userid);
    		$srNodeFix -> setAttribute("title", $user->title);
    		if (strlen($myrow->lat)) $srNodeFix -> setAttribute("lat", $user->lat);              		
    		if (strlen($myrow->lng)) $srNodeFix -> setAttribute("lng", $user->lng);
    		if (strlen($myrow->about)) {$srNodeFix -> setAttribute("about", $user->about);}        
    		if (strlen($user->birthday)) {
    			$srNodeFix -> setAttribute("birthday", substr($user->birthday,8,2).'/'.substr($user->birthday,5,2).'/'.substr($user->birthday,0,4));
          $srNodeFix -> setAttribute("age", $user->age);
        }
        if ($user->vkid) $srNodeFix -> setAttribute("vk", 'http://vk.com/id'.$user->vkid);
        if ($user->fbid) $srNodeFix -> setAttribute("fb", 'https://www.facebook.com/app_scoped_user_id/'.$user->fbid.'/');                
        if ($user->googleid) $srNodeFix -> setAttribute("google", 'https://plus.google.com/'.$user->googleid);        
        if ($user->okid) $srNodeFix -> setAttribute("ok", 'http://ok.ru/profile/'.$user->okid);
    		if (strlen($user->image)) {
    		  $srNodeFix -> setAttribute("image", getImgLink($user->image,'big'));
    		} else {
    		  $srNodeFix -> setAttribute("image", '/img/noava160.gif');
    		}    		
      }
    }  
    
    public function settings() {
      global $srNodeFix, $resultXML;
      $srNodeN = $srNodeFix -> appendChild($resultXML->createElement("SETTINGS"));
    }
        
    public function pm() {
      global $db, $config, $srNodeFix, $resultXML, $url, $user;
      require_once $_SERVER['DOCUMENT_ROOT'].'/parser/simple_html_dom.php';
      $srNodePM = $srNodeFix -> appendChild($resultXML->createElement("PM"));
      if ($url->item) {
        $srNodeFix -> setAttribute("tu", $touserid);
          
        $sql = "SELECT documentid, title, image, (accesstime + INTERVAL 5 MINUTE > now()) AS online FROM user WHERE documentid = $url->item";
        $result = $db->execute($sql);
        if ($myrow = mysql_fetch_object($result)) {
        	 $srNodeN = $srNodePM -> appendChild($resultXML->CreateElement("USER"));
        	 $srNodeN -> setAttribute("IID", $myrow->documentid);
        	 $srNodeN -> setAttribute("TITLE", $myrow->title);
        	 $srNodeN -> setAttribute("URL", '/u'.$myrow->documentid.'/');
        	 if ((int)$myrow->online) $srNodeN -> setAttribute("ONLINE", 'TRUE');
          	if (strlen($myrow->image)) {
          	  $srNodeN -> setAttribute("IMAGE_SMALL", getImgLink($myrow->image,''));
          	} else {
          	  $srNodeN -> setAttribute("IMAGE_SMALL", '/img/noava40.gif');
          	}
        }    
        
        $sql = "SELECT * FROM private_messages 
                WHERE removed=0 AND ((adresatid=$user->userid AND userid = $url->item) OR (userid=$user->userid AND adresatid = $url->item))
                ORDER BY id DESC
                LIMIT 30";
        $result = $db->execute($sql);
        if (mysql_num_rows($result)) {
        	while ($myrow = mysql_fetch_object($result)) {         		
          	$srNodeN = $srNodePM -> appendChild($resultXML->CreateElement("MESS"));
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
          	$srNodeN -> setAttribute("MY", ($myrow->userid == $user->userid)?'TRUE':'FALSE');
          } 
        } 
        
        $sql = "UPDATE private_messages SET `read`=1 
                WHERE adresatid=$user->userid AND userid=$url->item AND `read`=0";
        $db->execute($sql, false);              
      } else {
        $srNodeList = $srNodePM -> appendChild($resultXML->CreateElement("DIALOGS"));
        $sql = "SELECT userid, adresatid, pm.content, pm.date AS date, `read`, documentid, title, image, (accesstime + INTERVAL 5 MINUTE > now()) AS online
                FROM (SELECT * FROM private_messages
                      WHERE removed=0 AND (userid = $user->userid OR adresatid = $user->userid)
                      ORDER BY id DESC) pm
                INNER JOIN user
                        ON (user.documentid = adresatid OR user.documentid = userid) AND NOT(user.documentid = $user->userid)
                GROUP BY dialogid
                ORDER BY pm.date DESC";
        $result = $db->execute($sql);
      	while ($myrow = mysql_fetch_object($result)) {         		
        	$srNodeN = $srNodeList -> appendChild($resultXML->CreateElement("DIALOG"));
        	$srNodeN -> setAttribute("USERID", $myrow->documentid);
        	$srNodeN -> setAttribute("TITLE", $myrow->title);
      		if (strlen($myrow->image)) {
      			$srNodeN -> setAttribute("IMAGE", getImgLink($myrow->image,''));
      		} else {
      		  $srNodeN -> setAttribute("IMAGE", '/img/noava40.gif');
      		}
      		$srNodeN -> setAttribute("CONTENT", str_get_html($myrow->content)->plaintext);
      		$srNodeN -> setAttribute("DATE", $myrow->date);
      	  if ((int)$myrow->online) $srNodeN -> setAttribute("ONLINE", 'TRUE');      
      	  if (!(int)$myrow->read && $myrow->documentid != $myrow->adresatid) $srNodeN -> setAttribute("NEW", 'TRUE');
        	if ($myrow->documentid == $myrow->adresatid) {$srNodeN -> setAttribute("MYLAST", 'TRUE');}  	          	
        }              
      }  
    }
    
    public function index() {
      global $db, $config, $srNodeFix, $resultXML, $url, $currentUserId;
      $srNodeN = $srNodeFix -> appendChild($resultXML->createElement("INDEX"));
      userObjects($currentUserId, $srNodeN);  
    }                     
    
    public function events() {
      global $db, $config, $srNodeFix, $resultXML, $url, $user;
      $srNodeN = $srNodeFix -> appendChild($resultXML->createElement("EVENTS"));
      eventList($user->userid, $srNodeN, $user->action_read);
    }
  }  
  

  $currentUserId = $user->userid;

  $url = new Url($_REQUEST['rqpath']);
  
  if (!$currentUserId) {
    $srNodeFix -> appendChild($resultXML->createElement("NOAUTH"));
    return;
  }  
  
  $s = new Profile();
	switch ($url->section) {
    case 'pm':
      $s->pm();
      break;
    case 'events':
      $s->events();
      break;      
    default:
      $s->index();
      break;    
	}  
 
?>