<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	
	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
	
	$uploaddir = '../uploads/';
	
	if ($action=='delete') 
	{
		$file = '';
		$sql = "SELECT link FROM file WHERE documentid = $docid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
		  $file = $myrow->link;
		}
		if (strlen($file)>0 && file_exists($uploaddir.$file)) unlink($uploaddir.$file);
				
		$sql = "DELETE FROM file WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		
		header("Location: ok.html");
	}
	else
	if ($action=='edit') 
	{
		$sql = "SELECT * FROM file WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$TITLE = htmlspecialchars($myrow->title);
			$LINK = htmlspecialchars($myrow->link);
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{		
		$TITLE = trim(mysql_real_escape_string($_REQUEST['TITLE']));
		$LINK = trim(mysql_real_escape_string($_REQUEST['LINK']));
		if (get_magic_quotes_gpc())  { 
			$TITLE=stripslashes($TITLE);		 
			$LINK = stripslashes($LINK);
		}
		
		if (!strlen($LINK)) $LINK = makeFileName($_FILES['FILE']['name']); 
		if (!strlen($TITLE)) $TITLE = mysql_real_escape_string($_FILES['FILE']['name']);
		/* проверка на ошибка */
		$pattern = '/^[a-zA-Z0-9.\-_]+$/';
		$error="";		

		if (strlen($LINK) == 0) {		  
		  if (preg_match($pattern, $_FILES['FILE']['name'])) $LINK = $_FILES['FILE']['name'];  
		  else $error .= 'Не заполнено поле "Ссылка". '.$_FILES['FILE']['name'];		  
    } else		    
		if (!preg_match($pattern, $LINK)) {
		  $error .= 'Поле "Ссылка" содержит недопустимые символы. ';
    }    
		
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
		}
		else
		{	
			$FILE = "";
			
			getID();
			getSTID();
			
			/* Загрузка */			
			if (strlen($_FILES['FILE']['tmp_name'])>0) {
				$FILE = $uploaddir.$LINK;				
				if (!move_uploaded_file($_FILES['FILE']['tmp_name'], $FILE)) {die("Ошибка. Файл не был загружен. ");}				
			}
			$SIZE = $_FILES['FILE']['size'];
			$TYPE = $_FILES['FILE']['type']; 

				    
			if ($action=='update')
			{
				$sql = "UPDATE file SET title='$TITLE', link='$LINK', size=$SIZE, type='$TYPE' WHERE documentid = $docid";
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{
			  //if (strlen($TITLE)==0 && strlen($_FILES['FILE']['name'])>0) $TITLE = $_FILES['FILE']['name'];
        
				$sql = "INSERT INTO file (documentid, title, link, size, type) VALUES ($lastid, '$TITLE', '$LINK', $SIZE, '$TYPE')";
				$db->execute($sql, false);
				
				insertToAllDocs($sid, $lastid, $lastid, $TITLE, 'file', $stid);
				
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Загрузка файла</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript">
	</script>
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
    <h1>Файл</h1>        
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
			<td bgColor="#f4f4f4" valign="top"><b>Файл:</b></td>
			<td></td>
			<td><input type="file" name="FILE" class="inp" value=""/></td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4">Название:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Ссылка:</td>
			<td></td>
			<td><input type="text" name="LINK" class="inp" value="<?php echo $LINK; ?>"/><br/>
      <small>Допустимы символы: 0-9, a-z, точка, тире, символ подчеркивания.</small>
      </td>
		</tr>
		</table>
</div>		
</form>
</body>
</html>