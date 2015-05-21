<?php
  session_start();
  date_default_timezone_set($_SESSION['timezone']);
	include('php_emoji/emoji.php'); 
  require_once "../lib.php";  
  global $socids, $userid;
  $socids = array();
  header('Content-type: text/html; charset=UTF-8');
    
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
		}
	}  
  
  function period_to_timestamp($period) {
    $t = explode(",",$period);
    $arr = array();
    if(count($t) > 1){
      $arr['t1'] = strtotime($t[0]);
      $arr['t2'] = strtotime($t[1]);
    } else {
      $arr['t1'] = time() - $period * 3600;
      $arr['t2'] = time();
    }
    return $arr;
  }
  
  function vkget($count_record, $lat, $lng, $radius, $period, $textQuery) {
    global $socids;
    $s = 'http://api.vk.com/method/photos.search?count=' . $count_record .'&lat=' . $lat . '&long=' . $lng . '&radius=' . $radius;
    if($period){
      $arr = period_to_timestamp($period);
      $s.= '&start_time=' . $arr['t1'] . '&end_time=' . $arr['t2'];
    }
    
    $obj = json_decode(file_get_contents($s), true);
    $res = $obj['response'];
    $count = 0;
    $all_owner_id = '';
    foreach($res as $key=>$arr_field) {
      if(strcmp($key,'0') == 0) {
        $count = $key;
      } else {
        foreach($arr_field as $key1=>$field) {  
          if (strcmp($key1,'owner_id') === 0) {
            $field =  str_replace('-','',$field);
            if($all_owner_id=='') {
              $all_owner_id = $field; 
            } else {
              $all_owner_id.= "," . $field; 
            }
          }
        }
      }
    }
    
    $postdata1 = http_build_query(
      array(
          'user_ids' => $all_owner_id,
          'fields' => 'photo_200,bdate,city,interests,occupation,activities,personal'
      )
    );
    
    $opts1 = array('http' =>
      array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $postdata1
      )
    );
    
    $context1  = stream_context_create($opts1);
    $obj = json_decode(file_get_contents('http://api.vk.com/method/users.get', false, $context1), true);
    $result = $obj['response'];
    $arr_new = array();
    foreach($result as $key=>$arr_field) {
      $arr_new[$arr_field['uid']] = $arr_field;
    }
    $arr = array();
    foreach($res as $key1=>&$arr_field) {
      if (is_array($arr_field)) {
        if (strlen($textQuery)) if (strpos($arr_field['text'],$textQuery)===false) {
          unset($arr_field);
          continue;
        }
        $lifetime = 0;
        if((int)$arr_field['created'] > 0) {
          $lifetime = diff_datetime($arr_field['created']);
        }
        $arr_field['lifetime'] = $lifetime;
        $arr_field['rate'] = 0;
        $arr_field['type'] = 'vk';
        $arr_field['date'] = date('Y-m-d H:i:s',$arr_field['created']);
        $arr_field['created'] = timeAgo($arr_field['created']);
        $arr_field['profile_url'] = 'http://vk.com/id'.$arr_field['owner_id'];
        $arr_field['socid'] = $arr_field['pid'];
        if (!isset($socids[$arr_field['pid']])) {
          if ((int)$arr_field['owner_id'] > 0) {
            $value = $arr_new[$arr_field['owner_id']];
            $arr_add = array('fullname' => $value['first_name'] . " " . $value['last_name'], 'bdate' => $value['bdate']);
            if (trim($value['photo_200'])!='') {
              $arr_add['profile_picture'] = $value['photo_200'];
            } else {
              $arr_add['profile_picture'] = "";
            }            
            $arr[] = array_merge($arr_add,$arr_field);
          }        
        } else {
          //echo $arr_field['pid']."    ";
        }        
      }
    }
    
    return $arr;
  }  
  
  function instget($lat, $lng, $radius, $period, $textQuery) { 
    global $socids; 
    $s = 'https://api.instagram.com/v1/media/search?lat=' . $lat . '&lng=' . $lng . '&distance=' . $radius . '&client_id=e6a91043126244df9fecf644efddd581';
    if($period){
      $arr = period_to_timestamp($period);
      $s.= '&min_timestamp=' . $arr['t1'] . '&max_timestamp=' . $arr['t2'];
    }
    $obj = file_get_contents($s);
    $obj= json_decode($obj);
    $res = $obj -> data;
    
    $array_data = array();
    $i = 0;
    foreach($res as $key=>$res1){  
      $fullname = ''; $profile_picture = ''; $bdate = ''; $socid = ''; $owner_id = ''; $img = ''; $width = '';
      $height = ''; $src_small = ''; $src_big = ''; $text = ''; $created = ''; $link = ''; $date = '';
      foreach($res1 as $key1=>$field1) {     
        if($key1=='user'){
          if(trim($field1->full_name)!=''){
            $fullname = removeEmoji(emoji_unified_to_html($field1->full_name));
          } else {
            $fullname = removeEmoji(emoji_unified_to_html($field1->username));        
          }  
          $profile_picture = $field1->profile_picture;
        }
        if($key1=='caption'){
          $text =  removeEmoji(emoji_unified_to_html($field1->text));
        }
        if($key1=='images'){
          $img =  $field1->low_resolution->url;
          $width = $field1->standard_resolution->width;
          $height = $field1->standard_resolution->height;
          $src_big = $field1->standard_resolution->url;
          $src_small = $field1->thumbnail->url;
        }
        if($key1=='created_time'){
          $date = date('Y-m-d H:i:s',$field1);
          $created = timeAgo($field1);
          $lifetime = 0;
          if((int)$field1 > 0) {
            $lifetime = diff_datetime($field1);
          }          
        }
        if($key1=='location'){
          $lat = $field1->latitude;
          $lon = $field1->longitude;
        }
        if($key1=='id'){
            $a = explode('_',$field1);
            $owner_id = $a[1];
            $socid =  $a[0];
        }
        if($key1=='user'){
            $link = 'http://instagram.com/'.$field1->username;
        }        
      }
      
      if (strlen($textQuery)) if (strpos($text,$textQuery)===false) {
        continue;
      }            
      if (!isset($socids[$socid])) {
        $array_data[$i] = array(  'fullname' => $fullname,        
                                  'bdate' => $bdate,
                                  'socid' => $socid, 
                                  'owner_id' => $owner_id,
                                  'src' => $img, 
                                  'src_big' => $src_big,
                                  'src_small' => $src_small,
                                  'width' => $width,
                                  'height' => $height,
                                  'text' => $text,
                                  'date' => $date,
                                  'created' => $created,
                                  'lat' => $lat,
                                  'long' => $lon,
                                  'rate' => 0,
                                  'lifetime' => $lifetime,
                                  'profile_url' => $link,
                                  'type' => 'inst'
                                  );
        if (trim($profile_picture)!='') {
          $array_data[$i]['profile_picture'] = $profile_picture;
        } else {
          $array_data[$i]['profile_picture'] = "";
        }
        $i++;
      }
    }

    return $array_data;
  } 
  
  function ownget($lat, $lng, $radius, $period, $my, $tag, $textQuery) {
    global $db, $config, $socids, $userid;
  	$ids = array();
  	$array_data = array();
    $votes = array();
    $where = '';
    if (strlen($period)) {
      if (strpos($period,',')) {
        $temp = preg_split('/,/',$period);
        $d1 = substr($temp[0],6,4).'-'.substr($temp[0],3,2).'-'.substr($temp[0],0,2);
        $d2 = substr($temp[1],6,4).'-'.substr($temp[1],3,2).'-'.substr($temp[1],0,2);
        $where .= " AND object.`date` BETWEEN '$d1' AND '$d2'";
      } else {
        $where .= " AND ADDDATE(object.`date`, INTERVAL $period HOUR) > NOW()";
      }
    }
    if (strlen($tag)) {
      $where .= " AND tag = '$tag'";
    }
    if (strlen($textQuery)) {
      $where .= " AND text LIKE '%$textQuery%'";
    }

    if ($my && $userid) {
    	$sql = "SELECT object.id, object.socid, object.userid, object.text, object.image, object.link, object.date, object.lat, object.lng, object.fcount, object.rate, object.tag,
                     object.author_name, object.author_link, object.author_image, 
                    (6371*acos(cos(radians($lat))*cos(radians(object.lat))*cos(radians(object.lng)-radians($lng))+sin(radians($lat))*sin(radians(object.lat)))) AS distance,
                     last_action.userid AS la_userid, last_action.username AS la_username, last_action.userimage AS la_userimage, last_action.action AS la_action, last_action.value AS la_value
              FROM object
              LEFT JOIN last_action ON object.id = last_action.oid
              WHERE object.deleted=0 AND object.userid = $userid";
    } else {
      $radius_km = $radius/1000*2;
    	$sql = "SELECT object.id, object.socid, object.userid, object.text, object.image, object.link, object.date, object.lat, object.lng, object.fcount, object.rate, object.tag,
                     object.author_name, object.author_link, object.author_image, 
                    (6371*acos(cos(radians($lat))*cos(radians(object.lat))*cos(radians(object.lng)-radians($lng))+sin(radians($lat))*sin(radians(object.lat)))) AS distance,
                     last_action.userid AS la_userid, last_action.username AS la_username, last_action.userimage AS la_userimage, last_action.action AS la_action, last_action.value AS la_value
              FROM object
              LEFT JOIN last_action ON object.id = last_action.oid
              WHERE object.deleted=0 $where              
              HAVING distance < $radius_km
              ORDER BY last_action.date DESC
              LIMIT 100";                          
    }
    //echo $sql;
  	$result = $db->execute($sql);
  	while ($myrow = mysql_fetch_object($result)) {
  	 $ids[] = $myrow->id;
  	}
  	$ids_s = implode(',', $ids);
  	    	    	    	  
  	if ($userid && strlen($ids_s)) {
      $sql2 = "SELECT oid,value FROM object_vote WHERE userid = $userid AND oid IN ($ids_s)";
      $result2 = $db->execute($sql2);
      while ($myrow2 = mysql_fetch_object($result2)) {
        $votes[$myrow2->oid] = $myrow2->value;
      }
  	}
  	    	
    $i = 0;	
    mysql_data_seek($result,0);
  	while ($myrow = mysql_fetch_object($result)) {  	  	  
  	  if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
  	  else $image = '/img/noimg.png';
      $temp = array(            'fullname' => span_to_html($myrow->author_name),                                
                                'userid' => $myrow->userid,
                                'bdate' => '',
                                'socid' => ($myrow->socid)?$myrow->socid:$myrow->id, //не дело, могут пересекаться!
                                'oid' => $myrow->id,
                                'owner_id' => 0,
                                'src' => $image, 
                                'src_big' => $image,
                                'src_small' => $image,
                                'width' => 0,
                                'height' => 0,
                                'text' => span_to_html($myrow->text),
                                'link' => $myrow->link,
                                'fcount' => $myrow->fcount,
                                'created' => timeAgo(strtotime($myrow->date)),
                                'lat' => $myrow->lat,
                                'long' => $myrow->lng,
                                'rate' => $myrow->rate,
                                'profile_url' => $myrow->author_link,
                                'la_userid' => $myrow->la_userid,
                                'la_username' => $myrow->la_username,
                                'la_userimage' =>(strpos($myrow->la_userimage,'http:')!==false||strpos($myrow->la_userimage,'https:')!==false)?$myrow->la_userimage:getImgLink($myrow->la_userimage,''),
                                'la_action' => $myrow->la_action,
                                'la_value' => $myrow->la_value,
                                'tag' => $myrow->tag,
                                'type' => 'own'
                                );      
      if (isset($votes[$myrow->id])) $temp['myvote'] = $votes[$myrow->id];                                
      $temp['profile_picture'] = (strpos($myrow->author_image,'http:')!==false||strpos($myrow->author_image,'https:')!==false)?$myrow->author_image:getImgLink($myrow->author_image,'');
      
      $array_data[$i] = $temp;                          
      if ($myrow->socid) $socids[$myrow->socid] = 1;
      $i++;
    }    
    return $array_data;    
  }  
  
  function cacheGet($lat, $lng, $radius, $period, $apioff, $textQuery) {
    global $db, $config, $socids;
  	$ids = array();
  	$array_data = array();
    $votes = array();
    $where = '';
    if (strlen($period)) {
      if (strpos($period,',')) {
        $temp = preg_split('/,/',$period);
        $d1 = substr($temp[0],6,4).'-'.substr($temp[0],3,2).'-'.substr($temp[0],0,2);
        $d2 = substr($temp[1],6,4).'-'.substr($temp[1],3,2).'-'.substr($temp[1],0,2);
        $where .= " AND `date` BETWEEN '$d1' AND '$d2'";
      } else {
        $where .= " AND ADDDATE(`date`, INTERVAL $period HOUR) > NOW()";
      }
    }
    if (strlen($apioff)) {
      if (strpos($apioff,'vk')>-1) {
        $where .= " AND soctype!='vk'";
      } 
      if (strpos($apioff,'inst')>-1) {
        $where .= " AND soctype!='inst'";
      }      
    }
    if (strlen($textQuery)) {
      $where .= " AND text LIKE '%$textQuery%'";
    }
    
    $radius_km = $radius/1000;
  	$sql = "SELECT *, (6371*acos(cos(radians($lat))*cos(radians(lat))*cos(radians(lng)-radians($lng))+sin(radians($lat))*sin(radians(lat)))) AS distance                   
            FROM cache
            WHERE 1=1 $where              
            HAVING distance < $radius_km
            ORDER BY distance
            LIMIT 100";                          
    
    //echo $sql;
    $i=0;
  	$result = $db->execute($sql);
  	while ($myrow = mysql_fetch_object($result)) {
      if (!isset($socids[$myrow->socid])) {  	  	  
    	  $image = (strlen($myrow->image))?$myrow->image:'/img/noimg.png';
        $temp = array(            'fullname' => span_to_html($myrow->author_name),                                
                                  'socid' => $myrow->socid,
                                  'type' => $myrow->soctype,
                                  'oid' => 0,
                                  'rate' => 0,
                                  'owner_id' => 0,
                                  'src' => $image, 
                                  'src_big' => $image,
                                  'src_small' => $image,
                                  'width' => 0,
                                  'height' => 0,
                                  'text' => span_to_html($myrow->text),
                                  'date' => $myrow->date,
                                  'created' => timeAgo(strtotime($myrow->date)),
                                  'lat' => $myrow->lat,
                                  'long' => $myrow->lng,
                                  'profile_url' => $myrow->author_link,
                                  'profile_picture' => $myrow->author_image,
                                  'cache' => 'true', 
                                  );
        $array_data[$i] = $temp;                          
        $socids[$myrow->socid] = 1;
        $i++;
      }
    }    
    return $array_data;    
  }    
  
  function clusters() {
    //before CALL kmeans(200);
    global $db, $config;
    $array_data = array();
  	$sql = "SELECT * FROM km_clusters";                          
  	$result = $db->execute($sql);
  	while ($myrow = mysql_fetch_object($result)) {      
  	  if ($myrow->count && $myrow->lat && $myrow->lng) {
        $temp = array(            'cluster_id' => $myrow->id,                                                                  
                                  'lat' => $myrow->lat,
                                  'lng' => $myrow->lng,
                                  'count' => $myrow->count 
                                  );
        $array_data[] = $temp;
      }
    }    
    return $array_data;         
  }
  
  function socnet_get($count_record, $lat, $lng, $radius, $period, $apioff, $my, $zoom, $tag, $text) {
    if ($zoom<=7) {
      $array_data = clusters();
      $arr = array('data'=>$array_data,'type'=>'cluster');
      return json_encode($arr);      
    }
    
    
    $obj_own = ownget($lat, $lng, $radius, $period, $my, $tag, $text);    
    
    if (!$my) {
      if (!$apioff!='vkinst') {
        $obj_cache = cacheGet($lat, $lng, $radius, $period, $apioff, $text);
      }
        
      if (strpos($apioff,'vk')===false) {
        $obj_vk = vkget($count_record, $lat, $lng, $radius, $period, $text);
      }
      if (strpos($apioff,'inst')===false) {
        $obj_inst = instget($lat, $lng, $radius, $period, $text);
      }
    }
    
    $array_data = array();
    
    $array_data = $obj_own;
    if (!$my) {
      if ($obj_cache) {
        $array_data = array_merge($array_data, $obj_cache);
      }            
      
      if (count($array_data)<30) { //если меньше 30 объектов своих+их кеша, то берем из социалок 
        $from_soc = array();
        if ($obj_inst) {
          $from_soc = array_merge($from_soc, $obj_inst);
        }
        if ($obj_vk) {
          $from_soc = array_merge($from_soc, $obj_vk);                 
        } 
        if (count($from_soc)) {
          $array_data = array_merge($array_data, $from_soc);
          putToCache($array_data);
          //var_dump($array_data);
        }          
      }
    }
    $arr = array('data'=>$array_data);
    return json_encode($arr);    
  }
  
  function putToCache($array_data) {
    global $db, $config;
    $sql = "";
    foreach($array_data as $key=>$field) {
      if ($field['type']=='vk' || $field['type']=='inst') {
        $sql .= "('$field[socid]','$field[type]','$field[src_big]','".clearField($field['text'])."','$field[lat]','$field[long]','$field[date]','".clearField($field['fullname'])."','$field[profile_url]','$field[profile_picture]'),";
      }
    }
    if (strlen($sql)) {
      $sql = "REPLACE INTO cache (`socid`,`soctype`,`image`,`text`,`lat`,`lng`,`date`,`author_name`,`author_link`,`author_image`) VALUES ".substr($sql,0,strlen($sql)-1);
      $db->execute($sql,false);    
    }      
  }
  
?>