<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";

	$docid=intval($_REQUEST['docid']);
	$sid=intval($_REQUEST['sid']);
	$action = $_REQUEST['action'];	
	$STID = $_REQUEST['STID'];
	$CONTENT = $_REQUEST['CONTENT'];
	$TITLE = $_REQUEST['TITLE'];
	$XSL = $_REQUEST['XSL'];
	
	$sid=intval($sid);
	if (isset($docid)) {$docid=intval($docid);}
	
	if ($action=='delete') 
	{
		$sql = "DELETE FROM st WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$docid=intval($docid);
		$sql = "SELECT title, xsl, content FROM st WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$title = $myrow[0];
			$xsl = $myrow[1];
			$content = iconv("UTF-8","windows-1251",$myrow[2]);
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{	
		$TITLE=mysql_real_escape_string($TITLE);
		/*бля, вот это вообще пиздец, никакой защиты нахуй. надо подумать как сделать правильно*/
		if (get_magic_quotes_gpc()) 
		{ 
			$CONTENT=stripslashes($CONTENT);
		}
		$CONTENT = iconv("windows-1251","UTF-8",$CONTENT);
		
		if ($action=='update')
		{	
			$sql = "UPDATE st SET title='$TITLE', xsl='$XSL', content='$CONTENT' WHERE documentid = $docid";
			$db->execute($sql, false);
			$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
			$db->execute($sql, false);
			
			header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
		}
		else
		{			
			getID();
			getSTID();
			
			$sql = "INSERT INTO st (documentid, title, xsl, content) VALUES ($lastid, '$TITLE', '$XSL', '$CONTENT')";
			$db->execute($sql, false);
			
			$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype) VALUES ($sid, $lastid, $lastid, '$TITLE', 'st')";
			$db->execute($sql, false);
	
			header("Location: ok.html");
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование структурного шаблона</title>	
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
	<td class="top2" valign="middle"><b>С</b>оздание структурного шаблона</td>
	<td><img src="images/r.gif" width="6" height="25"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="fckform" id="fckform">';
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
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $title; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">XSL:</td>
			<td></td>
			<td><input type="text" name="XSL" class="inp" value="<?php echo $xsl; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Содержание:</td>
			<td></td>
			<td><textarea name="CONTENT" style="height:500px;"><?php echo $content; ?></textarea></td>
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