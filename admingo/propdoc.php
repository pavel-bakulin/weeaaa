<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	
	$sid=intval($_REQUEST['sid']);	
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
	
	$rqpath_before = '/';
	$sql = "SELECT rqpath FROM sections WHERE id = $sid";
	$result = $db->execute($sql);
	if ($myrow = mysql_fetch_object($result)) {
	  if (strlen($myrow->rqpath)>0) $rqpath_before = $myrow->rqpath;
	}
	
	if ($action=='edit') 
	{
		$sql = "SELECT title, rqpath, stid FROM alldocs WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$title = stripslashes(htmlspecialchars($myrow->title));
			$rqpath = $myrow->rqpath;
			if (strlen($rqpath)>0) {
			 $pos = strrpos($rqpath, "/");
       $rqpath = substr($rqpath, $pos+1, strlen($rqpath)-$pos);			 
			}
			
			$stid = $myrow->stid;
		}
	}
	else
	if ($_REQUEST['submit']) 
	{		
		$STID = intval($_REQUEST['STID']);
		$TITLE = mysql_real_escape_string($_REQUEST['TITLE']);
		$RQPATH = mysql_real_escape_string($_REQUEST['RQPATH']);
		if (strlen($RQPATH)>0) {$RQPATH = $rqpath_before.$RQPATH;}
		
		$sql = "UPDATE alldocs SET title='$TITLE', rqpath='$RQPATH', stid='$STID' WHERE documentid = $docid";
		$db->execute($sql, false);
		
		header("Location: ok.html");
	}
?>
<html>
<head>
	<title>Свойства документа</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>		
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript">
	</script>
</head>
<body>
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
	if ($action=='edit') 
	{
		echo "<input type='hidden' name='action' value='update'/>";
		echo "<input type='hidden' name='docid' value='$docid'/>";
	}
?>
<br/>
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td valign="top">Название:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $title; ?>"/></td>
		</tr>
		<tr>
			<td valign="top">Ссылка:</td>
			<td></td>
			<td><input type="text" name="RQPATH" class="inp" value="<?php echo $rqpath; ?>"/></td>
		</tr>
		<tr>
			<td valign="top">Шаблон:</td>
			<td></td>
			<td><input type="text" name="STID" class="inp" value="<?php echo $stid; ?>"/></td>
		</tr>		
		<tr height="100%">
			<td valign="top"></td>
			<td></td>
			<td valign="top"><input type="submit" name="submit" class="btn btn-primary" value="Отправить"/></td>
		</tr>
		</table>
		</form>

</body>
</html>