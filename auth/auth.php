<?php  
  error_reporting(E_ERROR);
  
  function auth($data) {  
    global $db, $config;     
    
    if ($data['authtype']=='vk') {
      $fileld = 'vkid';
      $socid = clearField($data['uid']);
      $first_name = clearField($data['first_name']);
      $last_name = clearField($data['last_name']);
      $name = $first_name.' '.$last_name; 
      $email = clearField($data['email']);
      $birthday = clearField($data['bdate']);
      if (strlen($birthday)) {
        $temp = preg_split('/\./',$birthday);
        $d = (strlen($temp[0])<2)?'0'.$temp[0]:$temp[0];
        $m = (strlen($temp[1])<2)?'0'.$temp[1]:$temp[1];
        $y = $temp[2];
        $birthday = $y.'-'.$m.'-'.$d;
      }
      $image = clearField($data['photo']);      
      $image_big = clearField($data['photo_big']);
    } else if ($data['authtype']=='fb') {
      $fileld = 'fbid';
      $socid = clearField($data['id']);         
      $name = clearField($data['name']);
      $temp = preg_split('/ /',$name);
      $first_name = $temp[0];
      $last_name = $temp[1];      
      $email = clearField($data['email']);
      $birthday = clearField($data['birthday']);
      if (strlen($birthday)) {
        $temp = preg_split('/\//',$birthday);
        $d = $temp[1];
        $m = $temp[0];
        $y = $temp[2];
        $birthday = $y.'-'.$m.'-'.$d;
      }
      $image = $data['picture']->data->url;
      $image_big = "https://graph.facebook.com/$socid/picture?type=large";
    } else if ($data['authtype']=='google') {
      $fileld = 'googleid';
      $socid = clearField($data['id']);                     
      $first_name = clearField($data['given_name']);
      $last_name = clearField($data['family_name']);
      $name = $first_name.' '.$last_name;      
      $email = '';
      $birthday = '';
      $image = $data['picture'];
      $image_big = $data['picture'];
      $link = $data['link'];
    } else if ($data['authtype']=='ok') {
      $fileld = 'okid';
      $socid = clearField($data['uid']);                     
      $first_name = clearField($data['first_name']);
      $last_name = clearField($data['last_name']);
      $name = $first_name.' '.$last_name;      
      $email = '';
      $birthday = '';
      $image = $data['pic_1'];
      $image_big = $data['pic_2'];
      $link = $data['link'];        
    }
    if (!$socid) die('Не удалось получить данные');
		$stateid = md5($email.time());
  		
    $sql = "SELECT documentid,title,image,status FROM user WHERE $fileld = $socid";
  	$result = $db->execute($sql);		
  	if ($myrow = mysql_fetch_object($result)) {
  		$db->execute("UPDATE user SET stateid='$stateid', accesstime = NOW(), ip='".$_SERVER['REMOTE_ADDR']."' WHERE $fileld = $socid", false);			
  		setcookie("stateid", $stateid, 0, '/');
  	  $result = array ('result'=>'signin','authtype'=>$data['authtype'],'title'=>$myrow->title,'image'=>getImgLink($myrow->image),'userid'=>$myrow->documentid,'status'=>$myrow->status);
      return json_encode($result);      
  	}
  	
  	$sid = 105;
  	$userid = getID();
  	$stid = getSTID($sid);		
  	$base = $_SERVER['DOCUMENT_ROOT'].$config->upfolder;
  	mkdir($base.$userid, 0755);
  
    if (strlen($image_big)) {  		        
      $hash = md5(uniqid(rand(), true));
      $img = downloadImage($image,$hash,'');
      downloadImage($image_big,$hash,'big');      
    }
  	
  	$sql = "INSERT INTO user SET documentid=$userid, title='$name', image='$img', email='$email', birthday='$birthday', $fileld=$socid, stateid='$stateid', accesstime = NOW(), ip='".$_SERVER['REMOTE_ADDR']."'";
  	$db->execute($sql, false);
  	$sql2 = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $userid, $userid, '$name', 'user', $stid)";
  	$db->execute($sql2, false);
  	setcookie("stateid", $stateid, 0, '/');
  	
    $result = array ('result'=>'signup','authtype'=>$data['authtype'],'title'=>$name,'image'=>getImgLink($img),'userid'=>$userid);
    return json_encode($result);
  }
?>
