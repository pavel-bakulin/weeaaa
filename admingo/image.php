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
		$image = '';
		$preview = '';
		$sql = "SELECT image, preview FROM images WHERE documentid = $docid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
		  $image = $myrow->image;
		  $preview = $myrow->preview;
		}
		if (strlen($image)>0 && file_exists($image)) unlink($image);
		if (strlen($preview)>0 && file_exists($preview)) unlink($preview);
		
		$sql = "DELETE FROM images WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		
		header("Location: ok.html");
	}
	else
	if ($action=='edit') 
	{
		$sql = "SELECT title, author FROM images WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$TITLE = htmlspecialchars($myrow[0]);
			$AUTHOR = htmlspecialchars($myrow[1]);
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{		
		$TITLE=mysql_real_escape_string($_REQUEST['TITLE']);
		$AUTHOR=mysql_real_escape_string($_REQUEST['AUTHOR']);
		if (get_magic_quotes_gpc())  { 
			$TITLE=stripslashes($TITLE);		 
			$AUTHOR = stripslashes($AUTHOR);
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
				
		if (!strlen($error)) {	
			$IMAGE = "";
			$PREVIEW = "";
			
			getID();
			getSTID();
			
			/* Загрузка картинки */
        
			$uploaddir = '../uploads/';
			
			if (strlen($_FILES['IMAGE']['tmp_name'])>0)
			{
				$newname = md5(uniqid(rand(), true)).substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
				$IMAGE = $uploaddir.$newname;				
        
				if ($_REQUEST['IMAGE_WIDTH']>0 || $_REQUEST['IMAGE_HEIGHT']>0)
				{				  
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

					$sw=imagesx($src);
					$sh=imagesy($src);
					$nw = intval($_REQUEST['IMAGE_WIDTH']);
					$nh = intval($_REQUEST['IMAGE_HEIGHT']);
					if (!($nh>0))
					{
						$nh = round($sh/$sw*$nw);
					}
					elseif (!($nw>0))
					{
						$nw = round($sw/$sh*$nh);
					}
	
      		$dist = imagecreatetruecolor($nw,$nh);
      		if ($fext=='.png') {
        		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
            imagefill($dist, 0, 0, $transparent);  		
            imagesavealpha($dist, true); 
          }   						
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
          imagedestroy($dist);					
				}
				else
				{
					if (!move_uploaded_file($_FILES['IMAGE']['tmp_name'], $uploaddir.$newname)) {die("файл не загружен");}
				}
			}
			
			if (strlen($_FILES['PREVIEW']['tmp_name'])>0)
			{
				//$PREVIEW=mysql_real_escape_string($_REQUEST['PREVIEW']);
				$newname_preview = md5(uniqid(rand(), true))."_pre".substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
				if (!move_uploaded_file($_FILES['PREVIEW']['tmp_name'], $uploaddir.$newname_preview)) {die("файл превью не загружен");}
				$PREVIEW = $uploaddir.$newname_preview;
			}
			if ($_REQUEST['PREVIEW_WIDTH']>0 || $_REQUEST['PREVIEW_HEIGHT']>0)
			{
				if (strlen($PREVIEW)>0)	{
          switch ($fext) {
            case '.jpg':
              $src = @imagecreatefromjpeg($PREVIEW);
              break;
            case '.gif':
              $src = @imagecreatefromgif($PREVIEW);
              break;
            case '.png':
              $src = @imagecreatefrompng($PREVIEW);
              break;
          }          
        }
				else 
				{
					$newname_preview = md5(uniqid(rand(), true))."_pre".substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
					$PREVIEW = $uploaddir.$newname_preview;

          switch ($fext) {
            case '.jpg':
              $src = @imagecreatefromjpeg($IMAGE);
              break;
            case '.gif':
              $src = @imagecreatefromgif($IMAGE);
              break;
            case '.png':
              $src = @imagecreatefrompng($IMAGE);
              break;
          }
				}
				
				$sw=imagesx($src);
				$sh=imagesy($src);
				$nw = intval($_REQUEST['PREVIEW_WIDTH']);
				$nh = intval($_REQUEST['PREVIEW_HEIGHT']);
				if (!($nh>0))
				{
					$nh = round($sh/$sw*$nw);
				}
				elseif (!($nw>0))
				{
					$nw = round($sw/$sh*$nh);
				}
				$dist = imagecreatetruecolor($nw,$nh);
    		if ($fext=='.png') {
      		$transparent = imagecolorallocatealpha($dist, 0, 0, 0, 127);
          imagefill($dist, 0, 0, $transparent);  		
          imagesavealpha($dist, true); 
        }   				
				imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);

        switch ($fext) {
          case '.jpg':
            imagejpeg ($dist, $PREVIEW, 100);
            break;
          case '.gif':
            imagegif ($dist, $PREVIEW);
            break;
          case '.png':
            imagepng ($dist, $PREVIEW, 0);
            break;
        }	
			}
			
			/* большая */
			if (strlen($_FILES['BIGIMAGE']['tmp_name'])>0)
			{
				$newname_bigimage = md5(uniqid(rand(), true))."_big".substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
				if (!move_uploaded_file($_FILES['BIGIMAGE']['tmp_name'], $uploaddir.$newname_bigimage)) {die("файл большой картинки не загружен");}
				$BIGIMAGE = $uploaddir.$newname_bigimage;
			}
			if ($_REQUEST['BIGIMAGE_WIDTH']>0 || $_REQUEST['BIGIMAGE_HEIGHT']>0)
			{
				if (strlen($BIGIMAGE)>0)	{
          switch ($fext) {
            case '.jpg':
              $src = @imagecreatefromjpeg($BIGIMAGE);
              break;
            case '.gif':
              $src = @imagecreatefromgif($BIGIMAGE);
              break;
            case '.png':
              $src = @imagecreatefrompng($BIGIMAGE);
              break;
          }          
        }
				else 
				{
					$newname_bigimage = md5(uniqid(rand(), true))."_pre".substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.') );
					$BIGIMAGE = $uploaddir.$newname_bigimage;

          switch ($fext) {
            case '.jpg':
              $src = @imagecreatefromjpeg($BIGIMAGE);
              break;
            case '.gif':
              $src = @imagecreatefromgif($BIGIMAGE);
              break;
            case '.png':
              $src = @imagecreatefrompng($BIGIMAGE);
              break;
          }
				}
				
				$sw=imagesx($src);
				$sh=imagesy($src);
				$nw = intval($_REQUEST['BIGIMAGE_WIDTH']);
				$nh = intval($_REQUEST['BIGIMAGE_HEIGHT']);
				if (!($nh>0))
				{
					$nh = round($sh/$sw*$nw);
				}
				elseif (!($nw>0))
				{
					$nw = round($sw/$sh*$nh);
				}
				$dist = imagecreatetruecolor($nw,$nh);
				imagecopyresampled($dist,$src,0,0,0,0,$nw,$nh,$sw,$sh);

        switch ($fext) {
          case '.jpg':
            imagejpeg ($dist, $BIGIMAGE, 100);
            break;
          case '.gif':
            imagegif ($dist, $BIGIMAGE);
            break;
          case '.png':
            imagepng ($dist, $BIGIMAGE, 0);
            break;
        }	
			}
			/* /// большая */
				    
			if ($action=='update')
			{
			  if (strlen($newname)>0) {$up_img = ", image='$newname'";} else {$up_img = '';}
			  if (strlen($newname_preview)>0) {$up_pre = ", preview='$newname_preview'";} else {$up_pre = '';}
			  if (strlen($newname_bigimage)>0) {$up_big = ", bigimage='$newname_bigimage'";} else {$up_big = '';}
			  
				$sql = "UPDATE images SET title='$TITLE'$up_img$up_pre$up_big, author='$AUTHOR' WHERE documentid = $docid";
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{
			  if (strlen($TITLE)==0) {
			   if (strlen($_FILES['IMAGE']['name'])>0) $TITLE = $_FILES['IMAGE']['name'];
         else  if (strlen($_FILES['PREVIEW']['name'])>0) $TITLE = $_FILES['PREVIEW']['name'];
         else $TITLE = 'image'.$lastid;
        }
				$sql = "INSERT INTO images (documentid, title, image, preview, bigimage, author) VALUES ($lastid, '$TITLE', '$newname', '$newname_preview', '$BIGIMAGE', '$AUTHOR')";
				$db->execute($sql, false);
				
				insertToAllDocs($sid, $lastid, $lastid, $TITLE, 'image', $stid);
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование картинки</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<?php		
	echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
	if ($action=='edit') 
	{
		echo "<input type='hidden' name='action' value='update'/>";
		echo "<input type='hidden' name='docid' value='$docid'/>";
	}
?>
  <div class="docHeader"><div>
    <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
    <h1>Картинка</h1>        
  </div></div>
  <div class="docBody">
<?php
	if (isset($_REQUEST['update'])) {
    echo '<div class="alert alert-success">Изменения внесены</div>';
  }
	if (strlen($error)) {
		echo '<div class="alert alert-error">'.$error.'</div>';
  }
?>
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td bgColor="#f4f4f4"><b>Название:</b></td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Картинка:</td>
			<td></td>
			<td>
				<input type="file" name="IMAGE" class="inp" value=""/>
<?php
	if ($action=='edit') {print '<a class="links2" href="JavaScript:showImg();">Показать картинку</a>';}
?>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Размеры:</td>
			<td></td>
			<td>
				<table width="100%">
				<tr>
					<td>Ширина: </td>
					<td width="50%"><input type="text" name="IMAGE_WIDTH" class="inp" value=""/></td>
					<td>Высота: </td>
					<td width="50%"><input type="text" name="IMAGE_HEIGHT" class="inp" value=""/></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td>
				<br/><br/><hr/>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Превью:</td>
			<td></td>
			<td>
				<input type="file" name="PREVIEW" class="inp" value=""/>
<?php
	if ($action=='edit') {print '<a class="links2" href="JavaScript:showPreview();">Показать превью</a>';}
?>	
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Размеры:</td>
			<td></td>
			<td>
				<table width="100%">
				<tr>
					<td>Ширина: </td>
					<td width="50%"><input type="text" name="PREVIEW_WIDTH" class="inp" value=""/></td>
					<td>Высота: </td>
					<td width="50%"><input type="text" name="PREVIEW_HEIGHT" class="inp" value=""/></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td valign="top">
<script>
function showImg()
{
	<?php
		print 'document.getElementById("img").src="/?id='.$docid.'";';
	?>
}
function showPreview()
{
	<?php
		print 'document.getElementById("img").src="/?id='.$docid.'&preview=true";';
	?>
}
</script>
				
				<img src="images/0.gif" id="img"/>
			</td>
		</tr>
		</table>
	</div>
</form>
</body>
</html>