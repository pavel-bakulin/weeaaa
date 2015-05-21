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
		$sql = "DELETE FROM feedback WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit')  {
		$sql = "SELECT * FROM feedback WHERE documentid = $docid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
			$userid = $myrow->userid;
			$username = $myrow->username;
			$content = $myrow->content;
			$rate = $myrow->rate;
			$active = $myrow->active;
		}
	}
	else
	if ($_REQUEST['submit']) {	
		$content = mysql_real_escape_string(rn2br($_REQUEST['content']));
		$rate = (int)$_REQUEST['rate'];
    $active = (int)$_REQUEST['active'];
		
		/*проверка на ошибка*/
		$error="";

		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
		}
		else
		{
			if ($action=='update')
			{	
				$sql = "UPDATE feedback SET content='$content', rate='$rate', active='$active' WHERE documentid = $docid";
				$db->execute($sql, false);
				/*$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$db->execute($sql, false);*/
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			/*else
			{			
				getID();
				getSTID();
				
				$sql = "INSERT INTO feedback (documentid, title, content, addparam) VALUES ($lastid, '$TITLE', '$CONTENT', '$ADDPARAM')";
				$db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$TITLE', 'feedback', $stid)";
				$db->execute($sql, false);
		
				header("Location: ok.html");
			}*/
		}
	}
?>
<html>
<head>
	<title>Отзыв</title>
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
	<td class="top2" valign="middle"><b>О</b>тзыв</td>
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
			<td bgColor="#f4f4f4">Пользователь:</td>
			<td></td>
			<td><a href="/admingo/user.php?docid=<?php echo $useid; ?>&sid=164&action=edit"><?php echo $username; ?></a></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Содержание:</td>
			<td></td>
			<td><textarea name="content"><?php echo $content; ?></textarea></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Оценка:</td>
			<td></td>
			<td>
			   <select name="rate">
			     <option value="0" <?php if ($rate==0) echo 'selected="selected"'; ?>>0<option>
			     <option value="1" <?php if ($rate==1) echo 'selected="selected"'; ?>>1<option>
			     <option value="2" <?php if ($rate==2) echo 'selected="selected"'; ?>>2<option>
			     <option value="3" <?php if ($rate==3) echo 'selected="selected"'; ?>>3<option>
			     <option value="4" <?php if ($rate==4) echo 'selected="selected"'; ?>>4<option>
			     <option value="5" <?php if ($rate==5) echo 'selected="selected"'; ?>>5<option>
			   </select>
      </td>
		</tr>			
		<tr>
			<td bgColor="#f4f4f4" valign="top">Активирован:</td>
			<td></td>
			<td>
  			<input type="checkbox" name="active" id="active" value="1" <?php if ($active==1) echo 'checked="checked"'; ?>/>
      </td>
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