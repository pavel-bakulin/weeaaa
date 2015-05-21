<?php
$fixResultNode = "";

class Paging {
  public function makePages($countonpage, $sql) { 
    global $db, $resultXML, $srNodeList;
    
		if(isset($_REQUEST['page']))  {$currentpage = (int)$_REQUEST['page'];}
		else {$currentpage = 1;}                		
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {$TotalMsg = $myrow->count;}
		unset($result);
		
		if ($TotalMsg <= $countonpage) $TotalPages = 1;
		elseif ($TotalMsg % $countonpage == 0) $TotalPages = $TotalMsg / $countonpage;
		else $TotalPages = ceil ($TotalMsg / $countonpage);
		if($TotalMsg == 0) $MsgStart = 0;
		else $MsgStart = $countonpage * $currentpage - $countonpage + 1;
		if ($currentpage == $TotalPages) $msgEnd = $TotalMsg;
		else $msgEnd = $countonpage * $currentpage;
		$InitialMsg = $countonpage * $currentpage - $countonpage;		
		$PrevPage = -1;
		$NextPage = -1;
		if ($currentpage > 1) {$PrevPage=$currentpage-1;}
		if ($currentpage < $TotalPages) {$NextPage=$currentpage+1;}
		
		$count = $countonpage;
		$start = $InitialMsg;

		$argv = $_SERVER['QUERY_STRING'];      				    		
		$argv = RemoveParamParam ("page", $argv);		
		$argv = RemoveParamParam ("rqpath", $argv);		

		$LINK = $_SERVER['PHP_SELF'];
		if (isset($_REQUEST['rqpath'])) {
		  $LINK = 'http://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['rqpath'];
		}
		
		$srNodeN = $srNodeList -> appendChild($resultXML->createElement("PARTS"));
		$srNodeN -> setAttribute("ACTIVE", $currentpage);
		$srNodeN -> setAttribute("LINK", $LINK.$argv);
		$srNodeN -> setAttribute("TOTAL", $TotalPages);
		$srNodeN -> setAttribute("DOCUMENTS", $TotalMsg);
		if ($PrevPage>0) {
			$srNodeN -> setAttribute("PREV", $PrevPage);
			$srNodePrev = $srNodeN -> appendChild($resultXML->createElement("PREVPART"));
			$srNodePrev -> setAttribute("CURRENTPAGE", $PrevPage);    			
		}
		if ($NextPage>0) {
			$srNodeN -> setAttribute("NEXT", $NextPage);
			$srNodeNext = $srNodeN -> appendChild($resultXML->createElement("NEXTPART"));
			$srNodeNext -> setAttribute("CURRENTPAGE", $NextPage);    			
		}
		
    $ddd = false;		
  	for ($p=1; $p<=$TotalPages; $p++) {
  	  if (abs($p-$currentpage)<6 || $p==1 || $p==$TotalPages) {
    		$srNodePart = $srNodeN -> appendChild($resultXML->createElement("PART"));
    		$srNodePart -> setAttribute("CURRENTPAGE", $p);
    		if ($p==$currentpage) {
    			$srNodePart -> setAttribute("ACTIVE", "TRUE");
    			$ddd = false;
    		}
    	}
    	if (abs($p-$currentpage)>=6 && !$ddd) {
    	  $ddd = true;
    		$srNodePart = $srNodeN -> appendChild($resultXML->createElement("PART"));
    		$srNodePart -> setAttribute("TYPE", 'ddd');  	
    	}
  	}  				
    
    $result = array();
    $result['start'] = $start;
    $result['count'] = $count;
    return $result;         
  }
}

function peopleCount($count) {
  if ($count % 10 == 1 && ($count<10 || $count>20)) return 'человеку';
  else return 'людям'; 
}

function eventList($userid, $node, $action_read, $start=0, $count=0) {
  global $db, $config, $srNodeFix, $resultXML;
  $limits = ($start&&$count)?"LIMIT $start, $count":"LIMIT 20";
  $sql = "SELECT last_action.id,oid,lat,lng,object.image,last_action.username,last_action.userid,last_action.userimage,action,value 
          FROM last_action
          INNER JOIN `object` ON object.id = last_action.oid
          WHERE (last_action.userid=$userid OR object.userid=$userid) 
          ORDER BY last_action.id DESC
          $limits;";    	// AND last_action.id>$user->action_read
	$result = $db->execute($sql); 
	$newevents = 0;
	while ($myrow = mysql_fetch_object($result)) {
		$srNodeFixE = $node -> appendChild($resultXML->CreateElement("EVENT"));
		$srNodeFixE -> setAttribute("id", $myrow->id);
		$srNodeFixE -> setAttribute("oid", $myrow->oid);
		$srNodeFixE -> setAttribute("lat", $myrow->lat);
		$srNodeFixE -> setAttribute("lng", $myrow->lng);
		  if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
       else $image = '/img/noimg.png';
		$srNodeFixE -> setAttribute("image", $image);
    $srNodeFixE -> setAttribute("username", $myrow->username);
    $srNodeFixE -> setAttribute("userid", $myrow->userid);
    $srNodeFixE -> setAttribute("userimage", getImgLink($myrow->userimage,''));
    $srNodeFixE -> setAttribute("action", $myrow->action);
    $srNodeFixE -> setAttribute("value", $myrow->value);
    if ($myrow->id>$action_read) $newevents++;
	}
  return $newevents;  
}

function userObjects($userid, $node) {
  global $db, $config, $srNodeFix, $resultXML;
	$sql = "SELECT object.id, object.socid, object.userid, object.text, object.image, object.link, object.date, object.lat, object.lng, object.fcount, object.rate,
             object.author_name, object.author_link, object.author_image,
             last_action.userid AS la_userid, last_action.username AS la_username, last_action.userimage AS la_userimage, last_action.action AS la_action, last_action.value AS la_value 
          FROM `object` 
          LEFT JOIN last_action ON object.id = last_action.oid           
          WHERE object.userid = $userid
          ORDER BY last_action.date DESC
          LIMIT 100";    
	$result = $db->execute($sql);
	objectList($result, $node);  
}

function objectList($result, $node) {
  global $db, $config, $srNodeFix, $resultXML, $user;
	while ($myrow = mysql_fetch_object($result)) {
	 $ids[] = $myrow->id;
	}
	if (count($ids)) {
  	$ids_s = implode(',', $ids);
            
  	if ($currentUserId && strlen($ids_s)) {
      $sql2 = "SELECT oid,value FROM object_vote WHERE userid = $user->userid AND oid IN ($ids_s)";
      $result2 = $db->execute($sql2);
      while ($myrow2 = mysql_fetch_object($result2)) {
        $votes[$myrow2->oid] = $myrow2->value;
      }
  	}
  
    mysql_data_seek($result,0);                
    $srNodeList = $node -> appendChild($resultXML->createElement("OBJECTS"));      
    while ($myrow = mysql_fetch_object($result)) {
      $srNodeN = $srNodeList -> appendChild($resultXML->createElement("ITEM"));
      $srNodeN -> setAttribute("iid", $myrow->id);
      $srNodeN -> setAttribute("userid", $myrow->userid);
  		$srNodeN -> setAttribute("lat", $myrow->lat);
      $srNodeN -> setAttribute("long", $myrow->lng);
      if (strlen($myrow->image)) $image = (strpos($myrow->image,'http:')!==false||strpos($myrow->image,'https:')!==false)?$myrow->image:getImgLink($myrow->image,'');
      else $image = '/img/noimg.png';
      $srNodeN -> setAttribute("src", $image);
      $srNodeN -> setAttribute("src_big", $image);
      $srNodeN -> setAttribute("created", $myrow->date);
      $srNodeN -> setAttribute("rate", $myrow->rate);
      $srNodeN -> setAttribute("fcount", $myrow->fcount);          
      if ($myrow->la_userid) $srNodeN -> setAttribute("la_userid", $myrow->la_userid);
      if ($myrow->la_username) $srNodeN -> setAttribute("la_username", $myrow->la_username);
      if ($myrow->la_userimage) $srNodeN -> setAttribute("la_userimage", (strpos($myrow->la_userimage,'http:')!==false||strpos($myrow->la_userimage,'https:')!==false)?$myrow->la_userimage:getImgLink($myrow->la_userimage,''));
      if ($myrow->la_action) $srNodeN -> setAttribute("la_action", $myrow->la_action);
      if ($myrow->la_value) $srNodeN -> setAttribute("la_value", $myrow->la_value);
      if (isset($votes[$myrow->id])) $srNodeN -> setAttribute("myvote", $votes[$myrow->id]);           
    }
  }    
}

function dateFormat($date) {
  $month = array('01'=>'января','02'=>'февраля','03'=>'марта','04'=>'апреля','05'=>'мая','06'=>'июня','07'=>'июля','08'=>'августа','09'=>'сентября','10'=>'октября','11'=>'ноября','12'=>'декабря');
  return substr($date,8,2).' '.$month[substr($date,5,2)].' '.substr($date,11,2).':'.substr($date,14,2);
}

function dateFormat2($date) {
  return substr($date,8,2).'.'.substr($date,5,2).'.'.substr($date,0,4).' '.substr($date,11,5);
}

function downloadImage($image, $hash, $pre) {
  global $config;
  
  $ext = '.jpg';
  $fn = $hash.$ext;
  $dir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder.substr($hash,0,2).'/';
  if (strlen($pre)) $dir = $dir.$pre.'/';
  mkdir($dir, 0755);
  
  copy($image, $dir.$fn);      
  
  return $fn;     
}

function getImgLink($img,$pre) {
  global $config;
  if($img == 'noimg.png'){
    return '/img/noimg.png';
  }
  $path = $config->upfolder.substr($img,0,2).'/';
  if (strlen($pre)) $path = $path.$pre.'/';
  return $path.$img;
}

function insertToAllDocs($sid, $lastid, $lastid, $title, $doctype, $stid) {
  global $db;
  
  $sql = "SELECT path FROM sections WHERE id=$sid";
  $result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
    $path = $myrow->path;
  }  
  
  $path = $path.$lastid.'_';
  
	$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid, path) VALUES ($sid, $lastid, $lastid, '$title', '$doctype', $stid, '$path')";
	$result = $db->execute($sql, false);  
}

function teamRights($teamid, $userid) {
  global $db;
  $sql = "SELECT * FROM team_user WHERE userid=$userid AND teamid=$teamid AND active=1";
  $result = $db->execute($sql);
  $moderator = false;
	if ($myrow = mysql_fetch_object($result)) {
    $moderator = (int)$myrow->moderator;
  }
  return $moderator;                 
}

function imageProcess ($url, $userid, $image) {  
  global $config; 
  $url = str_replace('https:','http:',$url);
  $url = str_replace('&amp;','&',$url);
  $content = file_get_contents($url);
  if (!$content) return false;

  $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder."$userid/";
  $byteCount = file_put_contents($uploaddir.$image, $content);
  if (!$byteCount) return false;
        
  imageSquare($image, $userid, 40, 'small_');
  imageSquare($image, $userid, 160, '');
  imageSquare($image, $userid, 263, 'multy_');
}

function imageCropp($image, $userid, $x1, $y1, $w, $h, $crop_width, $crop_height, $prefix) {
  global $config;
  if ((int)$userid == -1) $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
  elseif ((int)$userid) $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder."$userid/";
  else $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder.'temp/';
  
  $fext = NULL;
  if (preg_match("/(.*\\.jpg|.*\\.jpeg)$/i",$image)) $fext = '.jpg';
  else if (preg_match("/.*\\.gif$/i",$image)) $fext = '.gif';
  else if (preg_match("/.*\\.png$/i",$image)) $fext = '.png';  
  if ($fext == NULL) {return '';}
                                        
	$fileName = $prefix.$image;           
		
  switch ($fext) {
    case '.jpg':
      $src = @imagecreatefromjpeg($uploaddir.$image);
      break;
    case '.gif':
      $src = @imagecreatefromgif($uploaddir.$image);
      break;
    case '.png':
      $src = @imagecreatefrompng($uploaddir.$image);
      break;
  }  
  if (!isset($src)) return '';
  
	$dist = imagecreatetruecolor($crop_width,$crop_height);
	if ($fext=='.png') {
		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
    imagefill($dist, 0, 0, $transparent);  		
    imagesavealpha($dist, true); 
  }		  

	imagecopyresampled($dist, $src, 0, 0, $x1, $y1, $crop_width, $crop_height, $w, $h);  		  
    
  switch ($fext) {
    case '.jpg':
      imagejpeg ($dist, $uploaddir.$fileName, 100);
      break;
    case '.gif':
      imagegif ($dist, $uploaddir.$fileName);
      break;
    case '.png':
      imagepng ($dist, $uploaddir.$fileName, 0);
      break;
  }
  imagedestroy($dist);
  
  return $fileName;  
}

function imageSquare($image, $userid, $size, $prefix) {
  global $config;
  if ((int)$userid == -1) $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
  elseif ((int)$userid) $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder."$userid/";
  else $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder.'temp/';
  
  $fext = NULL;
  if (preg_match("/(.*\\.jpg|.*\\.jpeg)$/i",$image)) $fext = '.jpg';
  else if (preg_match("/.*\\.gif$/i",$image)) $fext = '.gif';
  else if (preg_match("/.*\\.png$/i",$image)) $fext = '.png';  
  if ($fext == NULL) {return '';}
                                        
	$fileName = $prefix.$image;           
		
  switch ($fext) {
    case '.jpg':
      $src = @imagecreatefromjpeg($uploaddir.$image);
      break;
    case '.gif':
      $src = @imagecreatefromgif($uploaddir.$image);
      break;
    case '.png':
      $src = @imagecreatefrompng($uploaddir.$image);
      break;
  }
  if (!isset($src)) return '';
    
  //уменьшаем
	$sw = imagesx($src);
	$sh = imagesy($src);
  if ($sw<$sh) {
    $nw = $size;
    $nh = round($nw/$sw*$sh);
  } else {
    $nh = $size;
    $nw = round($nh/$sh*$sw);  
  }
	$dist = imagecreatetruecolor($nw,$nh);
	if ($fext=='.png') {
		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
    imagefill($dist, 0, 0, $transparent);  		
    imagesavealpha($dist, true); 
  }
  imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);
                              
  //центрируем в квадрат
  if ($nw<$nh) {
    $x = 0;
    $y = ($nh-$size)/2;
  } else {
    $x = ($nw-$size)/2;    
    $y = 0;  
  }
	$dist2 = imagecreatetruecolor($size,$size);
	if ($fext=='.png') {
		$transparent = imagecolorallocatealpha($dist2, 0, 0, 0, 127);
    imagefill($dist2, 0, 0, $transparent);  		
    imagesavealpha($dist2, true); 
  }		  

	imagecopyresampled($dist2, $dist, 0, 0, $x, $y, $nw, $nh, $nw, $nh);
                                   //       $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )      
  switch ($fext) {
    case '.jpg':
      imagejpeg ($dist2, $uploaddir.$fileName, 100);
      break;
    case '.gif':
      imagegif ($dist2, $uploaddir.$fileName);
      break;
    case '.png':
      imagepng ($dist2, $uploaddir.$fileName, 0);
      break;
  }
  imagedestroy($dist);
  imagedestroy($dist2);
  
  return $fileName;  
}

function imageUpload($paramName, $imgWidth, $imageid, $prefix, $watermark) {
  global $config;
  
	if (strlen($_FILES[$paramName]['tmp_name'])>0) {
    $type = $_FILES[$paramName]['type'];
    $ext = array (
      'image/gif' => '.gif',
      'image/jpeg' => '.jpg',
      'image/png' => '.png',
      'image/pjpeg' => '.jpg',
      'image/x-png' => '.png');
    $fext = $ext[$type];
    if ($fext == NULL) {return '';}

    $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder.substr($imageid,0,2).'/';
    if (strlen($prefix)) $uploaddir = $uploaddir.$prefix.'/';
    mkdir($uploaddir, 0755);
    
                                          
	  $fileName = $imageid.substr($_FILES[$paramName]['name'], strrpos($_FILES[$paramName]['name'],'.') );           
		
    switch ($fext) {
      case '.jpg':
        $src = @imagecreatefromjpeg($_FILES[$paramName]['tmp_name']);
        break;
      case '.gif':
        $src = @imagecreatefromgif($_FILES[$paramName]['tmp_name']);
        break;
      case '.png':
        $src = @imagecreatefrompng($_FILES[$paramName]['tmp_name']);
        break;
    }
    
    if (!isset($src)) return '';

		$sw = imagesx($src);
		$sh = imagesy($src);
		if ((int)$imgWidth && $imgWidth<$sw) {
		  $nw = $imgWidth;
  		$nh = round($sh/$sw*$nw);  		       	
  		
  		$dist = imagecreatetruecolor($nw,$nh);
  		if ($fext=='.png') {
    		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
        imagefill($dist, 0, 0, $transparent);  		
        imagesavealpha($dist, true); 
      }		
  		imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);  		  
      
      if ($watermark) $dist = watermark($dist);		  		
  		
      switch ($fext) {
        case '.jpg':
          imagejpeg ($dist, $uploaddir.$fileName, 100);
          break;
        case '.gif':
          imagegif ($dist, $uploaddir.$fileName);
          break;
        case '.png':
          imagepng ($dist, $uploaddir.$fileName, 0);
          break;
      }
      imagedestroy($dist);
		} else {
		  if ($watermark) {
    		$w = imagesx($src);
    		$h = imagesy($src);
    		$dist = imagecreatetruecolor($w,$h);
    		if ($fext=='.png') {
      		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
          imagefill($dist, 0, 0, $transparent);  		
          imagesavealpha($dist, true); 
        }		
    		imagecopyresampled($dist,$src,0,0,0,0,$w,$h,$w,$h);  		  
        
        if ($watermark) $dist = watermark($dist);		  		
    		
        switch ($fext) {
          case '.jpg':
            imagejpeg ($dist, $uploaddir.$fileName, 100);
            break;
          case '.gif':
            imagegif ($dist, $uploaddir.$fileName);
            break;
          case '.png':
            imagepng ($dist, $uploaddir.$fileName, 0);
            break;
        }
        imagedestroy($dist);    		
        		    
		  } else {
        copy($_FILES[$paramName]['tmp_name'], $uploaddir.$fileName);
      }
    }     
    
    return $fileName;					      				
  } else return '';
}

function watermark($image) {
  global $config;
  $watermarkImage = @imageCreateFromString(file_get_contents($_SERVER['DOCUMENT_ROOT'].$config->upfolder.'watermark_for_site.png'));
  $wm_w = imagesx($watermarkImage);
  $wm_h = imagesy($watermarkImage);
  $d_w = imagesx($image);
  $d_h = imagesy($image);  
  imageCopy($image, $watermarkImage, $d_w-$wm_w-20, $d_h-$wm_h-20, 0, 0, $wm_w, $wm_h);
  return $image; 
}

function imageMultyUpload($paramName, $i, $imgWidth, $imageid, $prefix, $userid) {
  global $config;
  if ($userid) $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder."$userid/";
  else         $uploaddir = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
	if (strlen($_FILES[$paramName]['tmp_name'][$i])>0) {
    $type = $_FILES[$paramName]['type'][$i];
    $ext = array (
      'image/gif' => '.gif',
      'image/jpeg' => '.jpg',
      'image/png' => '.png',
      'image/pjpeg' => '.jpg',
      'image/x-png' => '.png');
    $fext = $ext[$type];
    if ($fext == NULL) {return '';}
                                        
	  $fileName = $prefix.$imageid.substr($_FILES[$paramName]['name'][$i], strrpos($_FILES[$paramName]['name'][$i],'.') );           
    		
    switch ($fext) {
      case '.jpg':
        $src = @imagecreatefromjpeg($_FILES[$paramName]['tmp_name'][$i]);
        break;
      case '.gif':
        $src = @imagecreatefromgif($_FILES[$paramName]['tmp_name'][$i]);
        break;
      case '.png':
        $src = @imagecreatefrompng($_FILES[$paramName]['tmp_name'][$i]);
        break;
    }
    
    if (!isset($src)) return '';
    
		$sw=imagesx($src);
		$sh=imagesy($src);
		if ((int)$imgWidth && $imgWidth<$sw) {
		  $nw = $imgWidth;
		} else {
      $nw = $sw;
    }
		$nh = round($sh/$sw*$nw);  		       	
		
		$dist = imagecreatetruecolor($nw,$nh);		
		if ($fext=='.png') {
  		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
      imagefill($dist, 0, 0, $transparent);  		
      imagesavealpha($dist, true); 
    }    
		imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);  		  		  		
		
    switch ($fext) {
      case '.jpg':
        imagejpeg ($dist, $uploaddir.$fileName, 100);
        break;
      case '.gif':
        imagegif ($dist, $uploaddir.$fileName);
        break;
      case '.png':
        imagepng ($dist, $uploaddir.$fileName, 0);
        break;
    }
    imagedestroy($dist); 

    return $fileName;					      				
  } else return '';
}

function validateUserPass($email,$pass) {
  global $db;
	$sql = "SELECT documentid, password FROM user WHERE email = '$email' AND active=1";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
		$password = $myrow->password;
		$userid = (int)$myrow->documentid;
	}
	if (isset($password) && ($password==$pass)) {return $userid;}
	else {return false;}
}

function clearContent($content) {
  $content = trim(mysql_real_escape_string(rn2br(htmlspecialchars($content, ENT_QUOTES))));
  if (get_magic_quotes_gpc())  {$content = stripslashes($content);}
  return $content; 
}

function clearField($content) {
  $content = trim(mysql_real_escape_string(htmlspecialchars($content, ENT_QUOTES)));
  if (get_magic_quotes_gpc())  {$content = stripslashes($content);}
  return $content; 
}

function clearAdmingoField($content) {
	if (get_magic_quotes_gpc()) {$content=stripslashes(quote2code($content,ENT_QUOTES));}
	else {$content=quote2code($content);}
	return $content;
  //return htmlcleaner::cleanup($content); 
}

function getID()
{	
	/*Формирование нового id-шника*/
	global $lastid,$db;
	
	$sql = "SELECT `id` FROM `idgenerator` LIMIT 0 , 1";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_row($result)) {
		$lastid = $myrow[0];
	}
	$lastid++;
	$sql = "UPDATE idgenerator set `id` = $lastid";
	$db->execute($sql, false);
	return $lastid;
}

function getSTID()
{
	/*определяем структурник*/
	global $stid,$db;
	global $sid;
	
	$numargs = func_num_args();
	if ($numargs>0) {$sid=func_get_arg(0);}
	
	$q=0; //это чтобы бесконечного цикла не было, на всякий случай
	$stid = 0;
	$sectionid_st = $sid;
	$end_sectionid_st = 0; // до какой папки подниматься
	while ($stid == 0 && $sectionid_st != $end_sectionid_st && $q<32)
	{
		$q++;
		$sql = "SELECT ancestor, stid FROM `sections` WHERE id=$sectionid_st";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_row($result))
		{
			$sectionid_st = $myrow[0];
			$stid = $myrow[1];
		}
	}
	return $stid;
}

function RemoveParamParam ($ParamName, $paramlist)
{
  $newParamlist = '?';
  $params = preg_split('/\&/',$paramlist);  
  foreach ($params as $value) {
    $param = preg_split('/=/',str_replace('?','',$value)); 
    $key = $param[0];
    $val = $param[1];
    if ($key != $ParamName) {
      $newParamlist .= $key.'='.$val.'&'; 
    }
  }    
  $newParamlist = substr($newParamlist, 0, strlen($newParamlist)-1);
	//echo $newParamlist.'<br/>';
  return $newParamlist;
}

function RemoveParamParam2 ($ParamName, $paramlist)
{
  $newParamlist = '';
  if (substr($paramlist, 0, 1) != '?')
    $newParamlist = '?';
  $params = preg_split('/\&/',$paramlist);
  foreach ($params as $param) {
    $key = substr($param, 0, strpos($param,'='));
    $value = substr($param, strpos($param,'='));    
    if (strlen($key) && $key != $ParamName) {
      $newParamlist .= $key.$value.'&'; 
    }
  }    
  $newParamlist = substr($newParamlist, 0, strlen($newParamlist)-1);
	
  return $newParamlist;
}

function getParamFromUrl($s)
{
	$patern = "{##.+##}";
	preg_match_all($patern,$s,$match);
	
	$param = substr($match[0][0],2);
	$param = substr($param,0,strlen($param)-2);
  if (!get_magic_quotes_gpc()) {
      $param = mysql_real_escape_string($_REQUEST[$param]);
  } else {
      $param = $_REQUEST[$param];
  }
	$param = $param;
	return preg_replace($patern, $param, $s);
}

function quote2code($s)
{
  return str_replace("'", "&#039;", $s);
  
}

function linked_save($docid) {
  global $db;
  $linked_id = $_REQUEST['linked_id'];

  $linked_name = $_REQUEST['linked_name'];
  $linked_doctype = $_REQUEST['linked_doctype'];
  $linkeds = array();
  $linkeds_n = array();
  for ($i=0; $i < count($linked_id); $i++) {
    $linkeds[$linked_id[$i]] =  $linked_doctype[$i];
    $linkeds_n[$linked_id[$i]] =  $linked_name[$i];
  }
  $notin = '';
  foreach ($linkeds as $key=>$value) {
    $notin .= $key.',';
  }
  $notin = substr($notin,0,strlen($notin)-1);
  $sql = "DELETE FROM linkeds WHERE linkedid not in ($notin) AND docid = $docid";
  $db->execute($sql, false);
  
  $sql = "SELECT * FROM linkeds WHERE docid = $docid";
  $result = $db->execute($sql);
  $exists_linkeds = array();
  while ($myrow = mysql_fetch_object($result)) {
    $exists_linkeds[$myrow->linkedid] = $myrow->doctype; 
  }       
  foreach ($linkeds as $key=>$value) {
    if (!array_key_exists($key,$exists_linkeds)) {            
      $sql = "INSERT INTO linkeds (docid, linkedid, doctype, title) VALUES ($docid, $key, '$value', '".$linkeds_n[$key]."')"; 
      $db->execute($sql, false);                      
    }
  }    
}

function linked_del($docid) {
  global $db;
  $sql = "DELETE FROM linkeds WHERE docid = $docid";
  $db->execute($sql, false);
}

function domainSecurity() {
  echo "<script>document.domain='".$_SERVER['SERVER_NAME']."';</script>";
}

function getAttribute($name, $att)
{
    foreach($att as $i)
    {
        if($i->name==$name)
            return $i->value;
    }
}

function rn2br($str)
{
  $endline = chr(13).chr(10);
  return str_replace($endline,'<br/>',$str); 
}

function br2rn($str)
{
  $endline = chr(13).chr(10);
  return str_replace('<br/>',$endline, $str); 
}

function checkBadSimbols($str) {
  $bads = array( '<', '>', '\'', '"' );
  $result = false;
  for ($i=0; $i<count($bads); $i++) {
    if (strpos($str,$bads[$i])!==false) $result = true;
  }
  return $result; 
}

function updateSubSectionRQPath($sid, $rqpath_old, $rqpath) {
  global $db;
  $sql = "SELECT id FROM sections WHERE ancestor = $sid";
  $result = $db->execute($sql);
  if (mysql_num_rows($result)>0) {
    $sql = "UPDATE sections SET rqpath = REPLACE(rqpath, '$rqpath_old', '$rqpath') WHERE ancestor = $sid";
    $db->execute($sql, false);
    while ($myrow = mysql_fetch_object($result)) {  
      updateSubSectionRQPath($myrow->id, $rqpath_old, $rqpath);
      updateDocumentsRQPath($myrow->id, $rqpath_old, $rqpath);
    } 
  }
}

function updateDocumentsRQPath($sid, $rqpath_old, $rqpath) {
  global $db;
  $sql = "UPDATE alldocs SET rqpath = REPLACE(rqpath, '$rqpath_old', '$rqpath') WHERE sid = $sid";
  $db->execute($sql, false);  
}

function cleantext($input) {
    $input = trim($input);
    $input = $input . ' ';
    $input = htmlentities($input, ENT_QUOTES, 'UTF-8');
    if (get_magic_quotes_gpc ()) {
        $input = stripslashes ($input);
    }
    $input = mysql_escape_string ($input);
    $input = strip_tags($input);
    $input = str_replace ("\\","\\\\", $input);
    return $input;
}

function checkDateTime($date)
{
  if (preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs)) {  
      return $date.' 00:00:00';
  }
  else if (preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) {    
      return $date;    
  } else {  
      return false;  
  } 
}

class ExchangeRatesCBRF
{
	var $rates;
	var $success = true;
	function __construct($date = null) {
	  try {
  		$client = new SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL"); 
  		if (!isset($date)) $date = date("Y-m-d"); 
  		$curs = $client->GetCursOnDate(array("On_date" => $date));
  		$this->rates = new SimpleXMLElement($curs->GetCursOnDateResult->any);
    } catch (Exception $e) {
      $this->success = false;
      return;
    }    
	}
	
	function GetRate ($code)
	{
	//Этот метод получает в качестве параметра цифровой или буквенный код валюты и возвращает ее курс
	  if (!$this->success) {
   	  return false;
    }
		$code1 = (int)$code;
		if ($code1!=0) 
		{
			$result = $this->rates->xpath('ValuteData/ValuteCursOnDate/Vcode[.='.$code.']/parent::*');
		}
		else
		{
			$result = $this->rates->xpath('ValuteData/ValuteCursOnDate/VchCode[.="'.$code.'"]/parent::*');
		}
		if (!$result)
		{
			return false; 
		}
		else 
		{
			$vc = (float)$result[0]->Vcurs;
			$vn = (int)$result[0]->Vnom;
			return ($vc/$vn);
		}

	}
}

class Valute
{
  var $dollar;
  var $euro;
  var $rub;
  
  function __construct() {
    $filename = $_SERVER['DOCUMENT_ROOT'].'/admingo/valute.txt';
    $data = array();
    if (file_exists($filename)) {
      $fd = @fopen($filename, "r");        
      if ($fd) {  
        while (!feof ($fd)) array_push($data, fgets($fd, 4096));
        fclose($fd);                
      } 
      if (trim($data[0]) != date("Y/m/d")) {
        $this->loadAndSave($filename);
      } else {
        $this->dollar = $data[1]; 	
        $this->euro = $data[2];      
      }         
    } else {
      $this->loadAndSave($filename);
    }

    $this->rub = 1;    
  }
  
  private function loadAndSave($filename) {
    $this->dollar = $this->get_rbc_content(840); 	
    $this->euro = $this->get_rbc_content(978);
    
    if ((int)$this->dollar <= 0 || (int)$this->euro <= 0) {
      $cbr = new ExchangeRatesCBRF();
      $this->dollar = $cbr->GetRate(840);
      $this->euro = $cbr->GetRate(978);
    } 
    
    if ((int)$this->dollar > 0 && (int)$this->euro > 0) {
      if (file_exists($filename)) {    
        unlink($filename);
      }
      if ((int)$this->dollar > 0 && (int)$this->euro > 0) {
        $fd = fopen($filename, 'a');
        fwrite($fd, date("Y/m/d").chr(13).chr(10)); 
        fwrite($fd, $this->dollar.chr(13).chr(10));
        fwrite($fd, $this->euro);
        fclose($fd);  
      }    
    } else {//если всё равно не получилось достать значение, берем из файла, то что есть.
      $fd = @fopen($filename, "r");        
      if ($fd) {  
        while (!feof ($fd)) array_push($data, fgets($fd, 4096));
        fclose($fd);                
      } 
      $this->dollar = $data[1]; 	
      $this->euro = $data[2];                
    }  
  }                      
  
  private function get_rbc_content($number) {
    $date = date("Y/m/d");
    $link = "http://cbrates.rbc.ru/tsv/".$number."/$date.tsv";
    
    $fd = @fopen($link, "r"); 
    $text=""; 
    if ($fd) {  
      while (!feof ($fd)) $text .= fgets($fd, 4096);
      fclose ($fd); 
    }           
    return (float)substr($text, 2, strlen($text)-2);
  }
  
  public function getCourse($param10) {
    switch ($param10) {
        case 'dollar':
            return $this->dollar;
            break;
        case 'euro':
            return $this->euro;
            break;
        case 'rub':
            return $this->rub;
            break;
    }
    return 100;//$this->rub;          
  }
}

function mime_header_encode($str, $data_charset, $send_charset) { 
  if($data_charset != $send_charset) { 
    $str = iconv($data_charset, $send_charset, $str); 
  } 
  return '=?' . $send_charset . '?B?' . base64_encode($str) . '?='; 
}

function send_mime_mail($name_from, // имя отправителя 
                        $email_from, // email отправителя 
                        $name_to, // имя получателя 
                        $email_to, // email получателя 
                        $data_charset, // кодировка переданных данных 
                        $send_charset, // кодировка письма 
                        $subject, // тема письма 
                        $body // текст письма 
                        ) { 
  $to = $name_to . ' <' . $email_to . '>'; 
  $subject = mime_header_encode($subject, $data_charset, $send_charset); 
  $from =  mime_header_encode($name_from, $data_charset, $send_charset) 
                     .' <' . $email_from . '>'; 
  if($data_charset != $send_charset) { 
    $body = iconv($data_charset, $send_charset, $body); 
  } 
  $headers = "From: $from\r\n"; 
  $headers .= "Content-type: text/html; charset=$send_charset\r\n"; 

  return mail($to, $subject, $body, $headers); 
  //return mail($to, $subject, $body);
}

function toLower($str) 
{
    $tr = array(
        "А"=>"а","Б"=>"б","В"=>"в","Г"=>"г",
        "Д"=>"д","Е"=>"е","Ж"=>"ж","З"=>"з","И"=>"и",
        "Й"=>"й","К"=>"к","Л"=>"л","М"=>"м","Н"=>"н",
        "О"=>"о","П"=>"п","Р"=>"р","С"=>"с","Т"=>"т",
        "У"=>"у","Ф"=>"ф","Х"=>"х","Ц"=>"ц","Ч"=>"ч",
        "Ш"=>"ш","Щ"=>"щ","Ъ"=>"ъ","Ы"=>"ы","Ь"=>"ь",
        "Э"=>"э","Ю"=>"ю","Я"=>"я"
    );
    return strtr($str,$tr);
}

function translitIt($str) 
{
    $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        " "=> "_", "."=> "", "/"=> "_"
    );
    return strtr($str,$tr);
}

function translitIt_fn($str) 
{
    $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        " "=> "_", "/"=> "_"
    );
    return strtr($str,$tr);
}

function makeURL($urlstr) {
  if (preg_match('/[^A-Za-z0-9_\-]/', $urlstr)) {
      $urlstr = translitIt($urlstr);
      $urlstr = preg_replace('/[^A-Za-z0-9_\-]/', '', $urlstr);
  }  
  return $urlstr; 
}

function makeFileName($urlstr) {
  if (preg_match('/[^A-Za-z0-9_\-\.]/', $urlstr)) {
      $urlstr = translitIt_fn($urlstr);
      $urlstr = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $urlstr);
  }  
  return $urlstr; 
}

function checkEmail($email) {
  if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",trim($email))) {
    return false;
  } else return true;
}

function checkURL($url) {
  if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
    return true;
  } else {
    return false;
  }
}

function autoURL($text) {
  $pattern = "/(((http|https):\/\/)?(www.)?([a-z0-9-]+\.)+[a-z]{2,6}([^\s]+)?)/i";
  preg_match($pattern, $text,$matches);
  $http = (strpos($matches[0], 'http://')=== false)?'http://':'';
  return preg_replace($pattern, '<a target="_new" href="'.$http.'\1">\1</a>', $text);
}

if (!function_exists('mb_ucfirst') && extension_loaded('mbstring')) { /** * mb_ucfirst - преобразует первый символ в верхний регистр * @param string $str - строка * @param string $encoding - кодировка, по-умолчанию UTF-8 * @return string */ 
  function mb_ucfirst($str, $encoding='UTF-8') { 
    $str = mb_ereg_replace('^[\ ]+', '', $str); 
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding). mb_substr($str, 1, mb_strlen($str), $encoding); 
    return $str; }
}

function fixBadUnicodeForJson($str) {
    $str = preg_replace("/\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})/e", 'chr(hexdec("$1")).chr(hexdec("$2")).chr(hexdec("$3")).chr(hexdec("$4"))', $str);
    $str = preg_replace("/\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})/e", 'chr(hexdec("$1")).chr(hexdec("$2")).chr(hexdec("$3"))', $str);
    $str = preg_replace("/\\\\u00([0-9a-f]{2})\\\\u00([0-9a-f]{2})/e", 'chr(hexdec("$1")).chr(hexdec("$2"))', $str);
    $str = preg_replace("/\\\\u00([0-9a-f]{2})/e", 'chr(hexdec("$1"))', $str);
    return $str;
}

function diff_datetime($d_load){
  $d1 = new DateTime("@$d_load");
  $d2 = new DateTime();
  $interval = date_diff($d2,$d1);
  $a = $interval->format('%Y, %M, %H, %I, %S');
  $arr = explode(",",$a);
  $s = '';
  if((int)$arr[0] != 0) $s.= $arr[0] . ' г. ';    
  if((int)$arr[1] != 0) $s.= $arr[1] . ' мес. ';    
  if((int)$arr[2] != 0) $s.= $arr[2] . ' ч. ';
  if((int)$arr[3] != 0) $s.= $arr[3] . ' мин. ';
  if((int)$arr[4] != 0) $s.= $arr[4] . ' сек. ';
  return $s;
}

function removeEmoji($text) {
  $clean_text = "";

  // Match Emoticons
  $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
  $clean_text = preg_replace($regexEmoticons, '', $text);

  // Match Miscellaneous Symbols and Pictographs
  $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
  $clean_text = preg_replace($regexSymbols, '', $clean_text);

  // Match Transport And Map Symbols
  $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
  $clean_text = preg_replace($regexTransport, '', $clean_text);

  // Match Miscellaneous Symbols
  $regexMisc = '/[\x{2600}-\x{26FF}]/u';
  $clean_text = preg_replace($regexMisc, '', $clean_text);

  // Match Dingbats
  $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
  $clean_text = preg_replace($regexDingbats, '', $clean_text);

  return $clean_text;
}

function span_to_html($s){
  $pattern = '/&lt;span class=&quot;emoji/i';
  $replacement = '<span class="emoji';
  $s1 = preg_replace($pattern, $replacement, $s);
  $pattern = '/&quot;&gt;&lt;\/span&gt;/i';
  $replacement = '"></span>';
  $s2 = preg_replace($pattern, $replacement, $s1);     
  return $s2;
}

function timeAgo($time_ago){
  $cur_time   = time();
  $time_elapsed   = $cur_time - $time_ago;
  $seconds    = $time_elapsed ;
  $minutes    = round($time_elapsed / 60 );
  $hours      = round($time_elapsed / 3600);
  // Seconds
  if($seconds <= 60){
    return "несколько секунд назад";
  }
  //Hours
  if($hours >=1 && $hours <=24){
    if($hours==1){
      return "час назад";
    } 
    $last_symbol =  substr($hours , -1);
    if($last_symbol == '1'){
      return "$hours час назад";
    }
    if($hours >=10 && $hours <=20){
      return "$hours часов назад";     
    }
    if(in_array($last_symbol,array('2','3','4'))){
      return "$hours часа назад";
    }
    return "$hours часов назад";    
  }  
  //Minutes
  if($minutes<60){
    if($minutes==1){
      return "минута назад";
    }
    if($minutes>=10 && $minutes<=20){
      return "$minutes минут назад";
    }
    $last_symbol =  substr($minutes, -1);
    if($last_symbol == '1'){
      return "$minutes минута назад";
    }
    if(in_array($last_symbol,array('2','3','4'))){
      return "$minutes минуты назад";
    }
    return "$minutes минут назад";
  }
  return date("d.m.Y H:i",$time_ago);
}


?>