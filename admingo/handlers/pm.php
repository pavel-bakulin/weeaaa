<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  header('Content-type: text/html; charset=UTF-8');
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid,title,image FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
			$username = $myrow->title;
			$userimage = $myrow->image;			
		}
	}
	if (!$userid) die();	  
	
	$action = $_REQUEST['action'];
	
	if ($action == 'add') {
    $content = autoURL(clearContent($_REQUEST['content']));
    $adresatid = (int)$_REQUEST['adresatid'];
    $file_a = $_REQUEST['file'];
    $filename_a = $_REQUEST['filename'];
    if (count($file_a) && count($filename_a)) {
      for ($i=0;$i<count($file_a);$i++) {
        $file_a[$i] = str_replace(',','',$file_a[$i]); 
        $filename_s[$i] = str_replace(',','',$filename_s[$i]);
      }
      $file_s = clearField(implode(',',$file_a));
      $filename_s = clearField(implode(',',$filename_a));
    }
    
    //если это рассылка всем юзерам
	  if ((int)$_REQUEST['all']) {
      if (!$admin) die();
      $sql = "SELECT documentid, title FROM user";
      $result = $db->execute($sql);
      while ($myrow = mysql_fetch_object($result)) {
        if ($myrow->documentid!=$userid) {
          $dialogid = min(array($userid,$myrow->documentid)).max(array($userid,$myrow->documentid));
          array_push($rows, "($userid, '$username', $myrow->documentid, '$myrow->title', '$content', 1, '$file_s', '$filename_s', '$dialogid')");
        }            
      }
      $sql = "INSERT INTO private_messages (userid, username, adresatid, adresatname, content, `all`, file, filename, dialogid) VALUES ".implode(',', $rows);      
      $db->execute($sql, false);	     
      return;
	  }
    
    $sql = "SELECT documentid, title, pmnotice, email FROM user WHERE documentid = $adresatid";
    $result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
      $adresatname = $myrow->title;   
      $pmnotice = (int)$myrow->pmnotice;  
      $adresatemail = $myrow->email;      
		}
    $dialogid = min(array($userid,$adresatid)).max(array($userid,$adresatid));
    $sql = "INSERT INTO private_messages (userid, username, userimage, adresatid, adresatname, content, file, filename, dialogid) VALUES ($userid, '$username', '$userimage', $adresatid, '$adresatname', '$content', '$file_s', '$filename_s', '$dialogid')";      
    $db->execute($sql, false);
    if ($pmnotice && strlen($adresatemail)) {
      $subject = 'Личное сообщение на '.$_SERVER['SERVER_NAME'];
      $body = 'Здравствуйте! <br/>Вы получили личное сообщение от пользователя '.$username.'. Чтобы прочитать сообщение, перейдите по ссылке <a href="http://'.$_SERVER['SERVER_NAME'].'/profile/pm/">http://'.$_SERVER['SERVER_NAME'].'/profile/pm/</a><br/>Если Вы больше не желаете получать подобные уведомления - отключите их в своих настройках здесь: <a href="http://'.$_SERVER['SERVER_NAME'].'/profile/settings/">http://'.$_SERVER['SERVER_NAME'].'/profile/settings/</a>';
      send_mime_mail($_SERVER['SERVER_NAME'], 
               $adresatemail, 
               $_SERVER['SERVER_NAME'], 
               $adresatemail, 
               'UTF8',  // кодировка, в которой находятся передаваемые строки 
               'windows-1251', // кодировка, в которой будет отправлено письмо 
               $subject, 
               $body);         
    }      
	} else if ($action == 'remove') {
    $iid = (int)$_REQUEST['iid'];
    $sql = "UPDATE private_messages SET removed=1 WHERE id = $iid AND (userid=$userid OR adresatid=$userid)";
    $db->execute($sql, false);    
	} else if ($action == 'dialogremove') {
    $adresatid = (int)$_REQUEST['adresatid'];
    $sql = "UPDATE private_messages SET removed=1 WHERE (userid=$userid AND adresatid=$adresatid) OR (userid=$adresatid AND adresatid=$userid)";
    $db->execute($sql, false);    
  } 
?>