<?php
  header('Content-type: text/html; charset=UTF-8');  
  global $config, $srNodeFix, $resultXML, $srNodeList;          
  $ids = array();
  
  $srNodeList = $srNodeFix -> appendChild($resultXML->CreateElement("LIST"));
  $sql = "SELECT count(1) as `count` FROM `object` WHERE deleted=0";          		
  $pages = new Paging();
  $limits = $pages->makePages(20, $sql);
  $limit = "LIMIT $limits[start], $limits[count]";
      
	$sql = "SELECT object.id, object.socid, object.userid, object.text, object.image, object.link, object.date, object.lat, object.lng, object.fcount, object.rate,
             object.author_name, object.author_link, object.author_image,
             last_action.userid AS la_userid, last_action.username AS la_username, last_action.userimage AS la_userimage, last_action.action AS la_action, last_action.value AS la_value 
          FROM `object` 
          LEFT JOIN last_action ON object.id = last_action.oid
          WHERE deleted=0
          ORDER BY last_action.date DESC
          $limit";    
	$result = $db->execute($sql);
	objectList($result, $srNodeList);
          
  /*while ($myrow = mysql_fetch_object($result)) {
    $srNodeItem = $srNodeList -> appendChild($resultXML->CreateElement("ITEM"));
		$srNodeItem -> setAttribute("iid", $myrow->id);
		$srNodeItem -> setAttribute("text", span_to_html($myrow->text));          
    $srNodeItem -> setAttribute("text_without_html", strip_tags(span_to_html($myrow->text)));
		$srNodeItem -> setAttribute("link", $myrow->link);    	
		$srNodeItem -> setAttribute("lat", $myrow->lat);              		
		$srNodeItem -> setAttribute("lng", $myrow->lng);
		$srNodeItem -> setAttribute("rate", $myrow->rate);
		$srNodeItem -> setAttribute("socid", $myrow->socid);
		if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
    else $image = '/img/noimg.png';
		$srNodeItem -> setAttribute("src", $image);
		
		$srNodeItem -> setAttribute("userid", $myrow->userid);
		$srNodeItem -> setAttribute("username", $myrow->username);
		if (strlen($myrow->userimage)) {
		  $srNodeItem -> setAttribute("userimage", getImgLink($myrow->image,''));
		} else {
		  $srNodeItem -> setAttribute("userimage", '/img/noava40.gif');
		}
		$srNodeItem -> setAttribute("created", timeAgo(strtotime($myrow->date)));
		$srNodeItem -> setAttribute("author_name", span_to_html($myrow->author_name));
		$srNodeItem -> setAttribute("author_link", $myrow->author_link);
		$srNodeItem -> setAttribute("author_image", (strpos($myrow->author_image,'http:')!==false||strpos($myrow->author_image,'https:')!==false)?$myrow->author_image:getImgLink($myrow->author_image,''));
    if ($myrow->la_userid) $srNodeItem -> setAttribute("la_userid", $myrow->la_userid);
    if ($myrow->la_username) $srNodeItem -> setAttribute("la_username", $myrow->la_username);
    if ($myrow->la_userimage) $srNodeItem -> setAttribute("la_userimage", (strpos($myrow->la_userimage,'http:')!==false||strpos($myrow->la_userimage,'https:')!==false)?$myrow->la_userimage:getImgLink($myrow->la_userimage,''));
    if ($myrow->la_action) $srNodeItem -> setAttribute("la_action", $myrow->la_action);
    if ($myrow->la_value) $srNodeItem -> setAttribute("la_value", $myrow->la_value);
    if (isset($votes[$myrow->id])) $srNodeItem -> setAttribute("myvote", $votes[$myrow->id]);
    		
		array_push($ids,$myrow->id);
  }
  
  if (count($ids)) {
    if ($user->userid) {
      $ids_s = implode(',',$ids);
      $sql = "SELECT oid,value FROM object_vote WHERE oid IN ($ids_s) AND userid = $user->userid";	
      $result = $db->execute($sql);
      $srNodeVotes = $srNodeList -> appendChild($resultXML->CreateElement("MYVOTES"));          
      while ($myrow = mysql_fetch_object($result)) {
        $srNodeV = $srNodeVotes -> appendChild($resultXML->CreateElement("VOTE"));
        $srNodeV -> setAttribute("oid", $myrow->oid);
        $srNodeV -> setAttribute("value", $myrow->value);
      }
    }
  }*/      

?>