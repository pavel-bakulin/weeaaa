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
		$sql = "DELETE FROM question WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$sql = "SELECT quest, multianswer, total FROM question WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$QUEST = htmlspecialchars($myrow[0]);
			$MULTIANSWER = $myrow[1];
			$TOTAL = $myrow[2];
		}
		$answers = array();
		$sql = "SELECT answer, acount FROM answers WHERE questid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$answers[0][] = $myrow[0];
			$answers[1][] = $myrow[1];
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{	
		$QUEST=mysql_real_escape_string($_REQUEST['QUEST']);
		if (get_magic_quotes_gpc())  { 
			$QUEST=stripslashes($QUEST);		 
		}		
		$answers = array();
		for ($q=0; $q<=10; $q++)
		{
			$answers[0][] = stripslashes(mysql_real_escape_string($_REQUEST['ANSWER'.$q]));
			$answers[1][] = intval($_REQUEST['ACOUNT'.$q]);
		}
		$MULTIANSWER=intval($_REQUEST['MULTIANSWER']);
		
		/*проверка на ошибка*/
		$error="";		
		if (strlen($QUEST)==0) {$error.="Поле Вопрос обязательно для заполнения<br/>";}				
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
		}
		else
		{
			if ($action=='update')
			{	
				$sql = "UPDATE question SET quest='$QUEST', multianswer=$MULTIANSWER WHERE documentid = $docid";
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$QUEST' WHERE documentid = $docid";
				$db->execute($sql, false);
				//удаляем все ответы, чтобы потом перезаписать их
				$sql = "DELETE FROM answers WHERE  questid = $docid";
				$db->execute($sql, false);
				for ($q=0; $q<=10; $q++)
				{
					if (strlen($answers[0][$q])>0)
					{
						$sql = "INSERT INTO answers (questid, answer, acount) VALUES ($docid, '".$answers[0][$q]."', ".$answers[1][$q].")";
						$db->execute($sql, false);
					}
					else {break;}
				}
				
				header("Location: $PHP_SELF?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{			
				getID();
				getSTID();
				
				$sql = "INSERT INTO question (documentid, quest, multianswer) VALUES ($lastid, '$QUEST', $MULTIANSWER)";
				$db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$QUEST', 'question', $stid)";
				$db->execute($sql, false);
				
				for ($q=0; $q<=10; $q++)
				{
					if (strlen($answers[0][$q])>0)
					{
						$sql = "INSERT INTO answers (questid, answer, acount) VALUES ($lastid, '".$answers[0][$q]."', ".$answers[1][$q].")";
						$db->execute($sql, false);
					}
					else {break;}
				}
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование интерактивного опроса</title>
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
	<td class="top2" valign="middle"><b>С</b>оздание интерактивного опроса</td>
	<td><img src="images/r.gif" width="6" height="25"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="get">';
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
			<td bgColor="#f4f4f4" valign="top"><b>Вопрос:</b></td>
			<td></td>
			<td><textarea name="QUEST"><?php echo $QUEST; ?></textarea></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td>
<?php
	if (isset($MULTIANSWER) && $MULTIANSWER==1) echo '<input checked="true" type="checkbox" name="MULTIANSWER" value="1"/>';
	else echo '<input type="checkbox" name="MULTIANSWER" value="1" id="MULTIANSWER"/>';
?><label for="MULTIANSWER">Разрешить опрашиваемому выбрать несколько ответов</label>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Число ответивших:</td>
			<td></td>
			<td><input type="text" name="TOTAL" class="inp2" value="<?php echo $TOTAL; ?>"/></td>
		</tr>
<?php
for ($q=0; $q<=10; $q++)
{
	echo '
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td align="center">ответ №'.($q+1).'</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Ответ:</td>
			<td></td>
			<td><input type="text" name="ANSWER'.($q).'" class="inp" value="'.$answers[0][$q].'"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Голосов:</td>
			<td></td>
			<td><input type="text" name="ACOUNT'.($q).'" class="inp2" value="'.$answers[1][$q].'"/></td>
		</tr>';
}
?>
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