<?php
  header('Content-type: text/html; charset=UTF-8');
  
  class Url {
    public $section = '';
    public $id = '';

    public function __construct($rqpath) {
      $parts = preg_split('/\//',$rqpath);
      $this->id = (int)substr($parts[0],6,strlen($parts[0])-1);
      if (strlen($parts[1])) $this->section = clearField($parts[1]);         
    }
  }  
  
  class Obj {
  
    public function __construct() {
      global $db, $config, $srNodeFix, $resultXML, $url, $user;        
      $sql = "SELECT `object`.id, `object`.text, `object`.link, `object`.lat, `object`.lng, `object`.image, `object`.rate, `object`.socid, `object`.tag,
                      user.documentid AS userid, user.title AS username, user.image AS userimage,
                      `object`.date, author_name, author_link, author_image
              FROM `object`
              INNER JOIN user ON `object`.userid = user.documentid 
              WHERE `object`.id = $url->id AND `object`.deleted=0";  		
      $result = $db->execute($sql);      
      if ($myrow = mysql_fetch_object($result)) {
    		$srNodeFix -> setAttribute("id", $myrow->id);
    		$srNodeFix -> setAttribute("text", $this->span_to_html($myrow->text));          
        $srNodeFix -> setAttribute("text_without_html", strip_tags($this->span_to_html($myrow->text)));
    		$srNodeFix -> setAttribute("link", $myrow->link);    	
    		$srNodeFix -> setAttribute("lat", $myrow->lat);              		
    		$srNodeFix -> setAttribute("lng", $myrow->lng);
    		$srNodeFix -> setAttribute("rate", $myrow->rate);
    		if (strlen($myrow->socid)) $srNodeFix -> setAttribute("socid", $myrow->socid);
    		if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
  	    else $image = '/img/noimg.png';
    		$srNodeFix -> setAttribute("image", $image);
    		
    		$srNodeFix -> setAttribute("userid", $myrow->userid);
    		$srNodeFix -> setAttribute("username", $myrow->username);
    		if (strlen($myrow->userimage)) {
    		  $srNodeFix -> setAttribute("userimage", getImgLink($myrow->image,''));
    		} else {
    		  $srNodeFix -> setAttribute("userimage", '/img/noava40.gif');
    		}
    		$srNodeFix -> setAttribute("date", timeAgo(strtotime($myrow->date)));
    		if (strlen($myrow->tag)) $srNodeFix -> setAttribute("tag", $myrow->tag);
    		$srNodeFix -> setAttribute("author_name", span_to_html($myrow->author_name));
    		$srNodeFix -> setAttribute("author_link", $myrow->author_link);
    		$srNodeFix -> setAttribute("author_image", (strpos($myrow->author_image,'http:')!==false||strpos($myrow->author_image,'https:')!==false)?$myrow->author_image:getImgLink($myrow->author_image,''));
      } else {
        $srNodeFix -> setAttribute("error", "Такого объекта не существует");
        return;
      }
      
  		if ($user->userid) {
        $sql = "SELECT value FROM object_vote WHERE oid = $url->id AND userid = $user->userid";	
        $result = $db->execute($sql);          
        if ($myrow = mysql_fetch_object($result)) {
          $srNodeFix -> setAttribute("myvote", $myrow->value);
        }
  		}      

      $sql = "SELECT `forum`.id, `forum`.content, `forum`.date, `forum`.answer_userid, `forum`.answer_username, 
                      user.documentid AS userid, user.title AS username, user.image AS userimage 
              FROM `forum`
              INNER JOIN user ON forum.userid = user.documentid 
              WHERE forum.oid = $url->id
              ORDER BY forum.id";  		
      $result = $db->execute($sql);      
      while ($myrow = mysql_fetch_object($result)) {
        $srNodeF = $srNodeFix -> appendChild($resultXML->createElement("FMESS"));
    		$srNodeF -> setAttribute("iid", $myrow->id);
    		$srNodeF -> setAttribute("content", $myrow->content);
    		$srNodeF -> setAttribute("date", $myrow->date);
    		
    		$srNodeF -> setAttribute("userid", $myrow->userid);
    		$srNodeF -> setAttribute("username", $myrow->username);
    		if (strlen($myrow->userimage)) {
    		  $srNodeF -> setAttribute("userimage", getImgLink($myrow->userimage,''));
    		} else {
    		  $srNodeF -> setAttribute("userimage", '/img/noava40.gif');
    		}
      }      
    }  
    
    public function index() {
      global $db, $config, $srNodeFix, $resultXML, $url, $currentUserId, $cacheSQL;
      $srNodeN = $srNodeFix -> appendChild($resultXML->createElement("INDEX"));  
    }
    
    private function span_to_html($s){
      $pattern = '/&lt;span class=&quot;emoji/i';
      $replacement = '<span class="emoji';
      $s1 = preg_replace($pattern, $replacement, $s);
      $pattern = '/&quot;&gt;&lt;\/span&gt;/i';
      $replacement = '"></span>';
      $s2 = preg_replace($pattern, $replacement, $s1);     
      return $s2;
    }    
  }  

  $url = new Url($_REQUEST['rqpath']);
  if ((int)$useridView) {
    $url->userid = $useridView;
  }    
  $o = new Obj();
  
	/*switch ($url->section) {     
    default:
      $o->index();
      break;    
	} */ 
 
?>