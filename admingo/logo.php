<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	
	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
	
	if ($action=='delete') 
	{
	 die("Нельзя удалять этот документ.");
	}
	else
	if ($action=='edit') 
	{
		$sql = "SELECT * FROM logo WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$TITLE = htmlspecialchars($myrow->title);
			$IMAGE = htmlspecialchars($myrow->image);			
			$EMAIL = htmlspecialchars($myrow->email);
			$DESCRIPTION = htmlspecialchars($myrow->description);
			$ICQ = htmlspecialchars($myrow->icq);
			$PHONE = htmlspecialchars($myrow->phone);
			$SKYPE = htmlspecialchars($myrow->skype);
			$ORDEREMAIL = htmlspecialchars($myrow->orderemail);
			$COUNTER = htmlspecialchars($myrow->counter);
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{		
		$TITLE=trim(mysql_real_escape_string($_REQUEST['TITLE']));
		$DESCRIPTION=trim(mysql_real_escape_string($_REQUEST['DESCRIPTION']));
		$EMAIL=trim(mysql_real_escape_string($_REQUEST['EMAIL']));
		$ICQ=trim(mysql_real_escape_string($_REQUEST['ICQ']));
		$PHONE=trim(mysql_real_escape_string($_REQUEST['PHONE']));
		$SKYPE=trim(mysql_real_escape_string($_REQUEST['SKYPE']));
		$ORDEREMAIL=trim(mysql_real_escape_string($_REQUEST['ORDEREMAIL']));
		$COUNTER=trim(mysql_real_escape_string($_REQUEST['COUNTER']));
		$COUNTER=str_replace('\r\n',chr(13).chr(10),$COUNTER);
		if (get_magic_quotes_gpc())  { 
			$TITLE=stripslashes($TITLE);		 
			$COUNTER=stripslashes($COUNTER);
			$DESCRIPTION = stripslashes($DESCRIPTION);
		}
		/*проверка на ошибка*/
		$error="";
		if (strlen($_FILES['IMAGE']['tmp_name'])>0) {
      $type = $_FILES['IMAGE']['type'];
      $ext = array (
        'image/gif' => '.gif',
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/pjpeg' => '.jpg',
        'image/x-png' => '.png');
      $fext = $ext[$type];
      if ($fext == NULL) {$error .= 'Неизвестный тип файла. ';}				
    }
				
		if (!strlen($ORDEREMAIL)) {
  	 $error .= 'Введите E-mail для заказа! ';
    }
		if (!strlen($TITLE)) {
  	 $error .= 'Введите Название! ';
    }    
				
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
			if ($action=='update') $action='edit';
		}
		else
		{	
			$IMAGE = "";
			
			getID();
			getSTID();
			
			/* Загрузка картинки */
        
			$uploaddir = 'uploadimg/';
			
			if (strlen($_FILES['IMAGE']['tmp_name'])>0) {
				$newname = md5(uniqid(rand(), true)).substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
				$IMAGE = $uploaddir.$newname;				
        											  
        switch ($fext) {
          case '.jpg':
            $src = @imagecreatefromjpeg($_FILES['IMAGE']['tmp_name']);
            break;
          case '.gif':
            $src = @imagecreatefromgif($_FILES['IMAGE']['tmp_name']);
            break;
          case '.png':
            $src = @imagecreatefrompng($_FILES['IMAGE']['tmp_name']);
            break;
        }

        $sh = imagesy($src);
        if ($sh > 75) {
					$sw=imagesx($src);					
					$nh = 75;
					$nw = round($sw/$sh*$nh);					
					$dist = imagecreatetruecolor($nw,$nh);
					imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);
					
          switch ($fext) {
            case '.jpg':
              imagejpeg ($dist, $IMAGE, 100);
              break;
            case '.gif':
              imagegif ($dist, $IMAGE);
              break;
            case '.png':
              imagepng ($dist, $IMAGE, 0);
              break;
          }          				
				}
				else
				{
					if (!move_uploaded_file($_FILES['IMAGE']['tmp_name'], $uploaddir.$newname)) {die("файл не загружен");}
				}
			}
					
			if ($action=='update')
			{
			  if (strlen($newname)>0) {$up_img = ", image='$IMAGE'";} else {$up_img = '';}
			  if (isset($_REQUEST['REMOVEIMAGE']) && (int)$_REQUEST['REMOVEIMAGE']==1) {$up_img = ", image=''";}
			  
				$sql = "UPDATE logo SET title='$TITLE'$up_img, email='$EMAIL', description='$DESCRIPTION', icq='$ICQ', phone='$PHONE', skype='$SKYPE', orderemail='$ORDEREMAIL' , counter='$COUNTER' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{
				$sql = "INSERT INTO logo (documentid, title, image, email, description, icq, phone, skype, orderemail, counter) VALUES ($lastid, '$TITLE', '$IMAGE', '$EMAIL', '$DESCRIPTION', '$ICQ', '$PHONE', '$SKYPE', '$ORDEREMAIL', '$COUNTER')";
				$db->execute($sql, false);
				
				$sql = "INSERT INTO AllDocs (sid, documentid, position, title, doctype) VALUES ($sid, $lastid, $lastid, 'Логотип, название, контакт', 'logo')";
				$db->execute($sql, false);
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Настройки</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/jquery-1.3.2.min.js"></script>
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td><img src="images/0.gif" width="6" height="6"/></td>
	<td></td>
	<td width="100%"></td>
	<td></td>
	<td><img src="images/0.gif" width="6" height="6"/></td>
</tr>
<tr>
	<td></td>
	<td><img src="images/l.gif" width="6" height="25"/></td>
	<td class="top2" valign="middle"><b>Н</b>астройки</td>
	<td><img src="images/r.gif" width="6" height="25"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">';
	echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
	if ($action=='edit') 
	{
		echo "<input type='hidden' name='action' value='update'/>";
		echo "<input type='hidden' name='docid' value='$docid'/>";
	}
?>
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td bgColor="#f4f4f4"><img src="images/0.gif" width="120" height="6"/></td>
			<td><img src="images/0.gif" width="15" height="6"/></td>
			<td width="100%">
<?php
	if (isset($_REQUEST['update'])) {
    echo "<span class='allright'>Изменения внесены</span></br>";
  }
?>
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"><b>Название:</b></td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Описание:</td>
			<td></td>
			<td><input type="text" name="DESCRIPTION" class="inp" value="<?php echo $DESCRIPTION; ?>"/></td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4" valign="top">Логотип:</td>
			<td></td>
			<td>
				<input type="file" name="IMAGE" class="inp" value=""/>
				<br/><small>Высота - 75 пикселей. Если Вы исходное изображение будет больше - оно пропорционально ужмется.</small><br/>
        <img src="<?php echo $IMAGE;?>"/>				
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top"></td>
			<td></td>
			<td>
				<input type="checkbox" name="REMOVEIMAGE" id="REMOVEIMAGE" value="1"/> <label for="REMOVEIMAGE">Удалить изображение</label>						
			</td>
		</tr>		
		<tr>
			<td valign="top" bgColor="#f4f4f4">Контакты:</td>
			<td></td>
			<td>
            <div style="display:inline-block;width:100px;margin-bottom:5px;">E-mail:</div><input type="text" name="EMAIL" class="inp2" value="<?php echo $EMAIL; ?>"/><br/>
            <div style="display:inline-block;width:100px;margin-bottom:5px;">Телефон:</div><input type="text" name="PHONE" class="inp2" value="<?php echo $PHONE; ?>"/><br/>
            <div style="display:inline-block;width:100px;margin-bottom:5px;">ICQ:</div><input type="text" name="ICQ" class="inp2" value="<?php echo $ICQ; ?>"/><br/>
            <div style="display:inline-block;width:100px;margin-bottom:5px;">Skype:</div><input type="text" name="SKYPE" class="inp2" value="<?php echo $SKYPE; ?>"/><br/>
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"><b>E-mail:</b></td>
			<td></td>
			<td><input type="text" name="ORDEREMAIL" class="inp2" style="width:250px;" value="<?php echo $ORDEREMAIL; ?>"/><br/>
        <small>На этот адрес будут приходить заказы Интернет-магазина</small>
      </td>
		</tr>  
		<tr>
			<td bgColor="#f4f4f4" valign="top">Счетчики:</td>
			<td></td>
			<td><textarea name="COUNTER" rows="6" onFocus="$(this).css('height','300px');" xonBlur="$(this).css('height','52px');"><?php echo $COUNTER; ?></textarea></td>
		</tr>      	
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td valign="top">
				<input type="submit" name="submit" class="btn" value="Отправить"/>
			</td>
		</tr>
		</table>
		</form>
	</td>
	<td></td>
	<td></td>
</tr>
</table>
</body>
</html>