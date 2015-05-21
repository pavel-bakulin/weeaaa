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
		$sql = "DELETE FROM record WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$sql = "SELECT title, content, addparam, ip FROM record WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$TITLE = $myrow[0];
			$CONTENT = br2rn($myrow[1]);
			$ADDPARAM = $myrow[2];
			$IP = $myrow[3];
		}
	}
	else
	if ($_REQUEST['submit']) 
	{	
		$TITLE = mysql_real_escape_string($_REQUEST['TITLE']);
		$CONTENT = mysql_real_escape_string(rn2br($_REQUEST['CONTENT']));
		$ADDPARAM = mysql_real_escape_string($_REQUEST['ADDPARAM']);
 
		
		/*проверка на ошибка*/
		$error="";
		if (strlen($TITLE)>254) {$error.="Поле Заголовок не может быть длиннее 255 символов<br/>";}
		if (strlen($CONTENT)>1024) {$error.="Поле Содержание не может быть длиннее 1024 символов<br/>";}
		if (strlen($ADDPARAM)>1024) {$error.="Поле Дополнительно не может быть длиннее 1024 символов<br/>";}
		if (strlen($TITLE)==0) {$error.="Поле Заголовок обязательно для заполнения<br/>";}
    				
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
		}
		else
		{
			if ($action=='update')
			{	
				$sql = "UPDATE record SET title='$TITLE', content='$CONTENT', addparam='$ADDPARAM' WHERE documentid = $docid";
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{			
				getID();
				getSTID();
				
				$sql = "INSERT INTO record (documentid, title, content, addparam) VALUES ($lastid, '$TITLE', '$CONTENT', '$ADDPARAM')";
				$db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$TITLE', 'record', $stid)";
				$db->execute($sql, false);
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование записи</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript">
	</script>
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
	<td class="top2" valign="middle"><b>С</b>оздание записи</td>
	<td><img src="images/r.gif" width="6" height="25"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
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
			<td bgColor="#f4f4f4">Название:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Содержание:</td>
			<td></td>
			<td><textarea name="CONTENT"><?php echo $CONTENT; ?></textarea></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">IP-адрес:</td>
			<td></td>
			<td><?php echo $IP; ?></td>
		</tr>		
		<tr height="100%">
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td valign="top"><input type="submit" name="submit" class="btn" value="Отправить"/></td>
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