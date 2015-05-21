<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	require_once "htmlcleaner.php";
	

	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
		
	$DATE = date('Y-m-d H:i:s');
	if ($action=='delete') 
	{
		$sql = "DELETE FROM mailform WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";		
		$db->execute($sql, false);
		$sql = "DELETE FROM linkeds WHERE docid = $docid OR linkedid = $docid";		
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$sql = "SELECT * FROM mailform WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$TITLE = stripslashes(htmlspecialchars($myrow->title,ENT_QUOTES));
			$DESCRIPTION = $myrow->description;
			$EMAIL = stripslashes(htmlspecialchars($myrow->email,ENT_QUOTES));
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{
		$TITLE=trim(mysql_real_escape_string($_REQUEST['TITLE']));
		$DESCRIPTION=trim(mysql_real_escape_string(rn2br($_REQUEST['DESCRIPTION'])));
		//$EMAIL=trim(mysql_real_escape_string($_REQUEST['EMAIL']));
		$EMAIL='...';
		
		if (get_magic_quotes_gpc())  { 
		  $TITLE = stripslashes($TITLE);
      $DESCRIPTION=stripslashes(quote2code($_REQUEST['DESCRIPTION'],ENT_QUOTES));			
			$EMAIL = stripslashes($EMAIL);
		} else {
      $DESCRIPTION=quote2code($_REQUEST['DESCRIPTION']);
    }
    
	  $cleanup = intval($_REQUEST['cleanup']);
	  if ($cleanup > 0) {
    	$DESCRIPTION = htmlcleaner::cleanup($DESCRIPTION);
	  }    
		
		/*проверка на ошибки*/
		$error="";
		if (strlen($EMAIL)==0) {$error.="Поле E-mail обязательно для заполнения<br/>";}
				
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
			if ($action=='update') $action='edit';
		}
		else
		{		        
			if ($action=='update')
			{	
				$sql = "UPDATE mailform SET title='$TITLE', email='$EMAIL', description = '$DESCRIPTION' WHERE documentid = $docid";
				$result = $db->execute($sql, false);				
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$result = $db->execute($sql, false);
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{	
				getID();
				getSTID();
				
				$sql = "INSERT INTO mailform (documentid, title, email, description) VALUES ($lastid, '$TITLE', '$EMAIL', '$DESCRIPTION')";
				$result = $db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$TITLE', 'mailform', $stid)";
				$result = $db->execute($sql, false);
				
				/* корневой документ родительской папки */
  			$sql = "SELECT RootDocId FROM sections WHERE id = $sid";
  			$result = $db->execute($sql);
  			if ($myrow = mysql_fetch_object($result)) {
  			 $sql2 = "SELECT count(*) as count FROM alldocs WHERE sid = $sid AND (doctype='mailform' OR doctype='material')";
  			 $result2 = $db->execute($sql2);
  			 if ($myrow2 = mysql_fetch_object($result2)) {
  			   if ($myrow2->count == 1 && $myrow->RootDocId==0) { //материал в папке только один, а корневой документ ещё не назначен
            $sql = "UPDATE sections SET RootDocId=$lastid WHERE id = $sid";
				    $db->execute($sql, false);  
  			   }
  			 }  			 
  			}
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование почтовой формы</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
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
	<td class="top2" valign="middle"><b>С</b>оздание почтовой формы</td>
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
			<td bgColor="#f4f4f4">Заголовок формы:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4" valign="top">Описание:</td>
			<td></td>
			<td>
		<input type="hidden" name="CHANGED"/>
		<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
		
<?php
		echo 'var URL="http://'.$_SERVER['SERVER_NAME'].'";';
		echo 'var SECTIONID="'.$sid.'";';
?>
		window.onload = function()
	      {      
          var editor = CKEDITOR.replace( 'myFF',
              {                                 
                  filebrowserImageWindowWidth : '600',
                  filebrowserImageWindowHeight : '750',
                  filebrowserImageBrowseUrl : URL+"/<?php echo $config->cms; ?>/selectimg.php?filename=ImageInsert&SECTIONID="+ SECTIONID,
                  filebrowserWindowWidth : '600',
                  filebrowserWindowHeight : '750',
                  filebrowserLinkBrowseUrl : URL+"/<?php echo $config->cms; ?>/selectdoc.php?filename=linkinsert&SECTIONID="+ SECTIONID
              });

                 editor.on('focus', function(event) {

                 document.forms[0].CHANGED.value = '1';

                }); 
            }
		</script>
		<textarea id="myFF" cols="120" rows="50" style="width:100%; height:200px;" name="DESCRIPTION"><?php echo $DESCRIPTION;?></textarea>
<?php
			if (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE") === false && strpos($_SERVER['HTTP_USER_AGENT'],"Firefox") === false) {
			echo '<br/><font color="#c83400" size="-2">Внимание! Редактор корректно работает только в браузерах Internet Explorer и Mozilla Firefox</font>';
			}
?>
			</td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td><input type="checkbox" name="cleanup" value="1" id="clearcode"/>
			<label for="clearcode">Установите этот флаг, если вы копируете текст из Word, Excel или с другого сайта.</label>
      </td>
		</tr>
		<!--
		<tr>
			<td bgColor="#f4f4f4">E-mail:</td>
			<td></td>
			<td><input type="text" name="EMAIL" class="inp" value="<?php echo $EMAIL; ?>"/></td>
		</tr>-->
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