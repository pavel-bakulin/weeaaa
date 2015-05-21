<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	require_once "htmlcleaner.php";

	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
			
	if ($action=='delete')  {
		$sql = "DELETE FROM multydoc WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";		
		$db->execute($sql, false);
		$sql = "DELETE FROM linkeds WHERE docid = $docid OR linkedid = $docid";		
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') {
		$sql = "SELECT * FROM multydoc WHERE documentid = $docid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
			$TITLE = stripslashes(htmlspecialchars($myrow->title,ENT_QUOTES));
			$CONTENT = $myrow->content;
		}
	}
	else
	if (isset($_REQUEST['submit'])) {
		$TITLE=trim(mysql_real_escape_string($_REQUEST['TITLE']));
    
		if (get_magic_quotes_gpc())  { 
			$CONTENT=stripslashes(quote2code($_REQUEST['CONTENT'],ENT_QUOTES));		 
		}
		else { 
			$CONTENT=quote2code($_REQUEST['CONTENT']);
		}		
	  $cleanup = intval($_REQUEST['cleanup']);
	  if ($cleanup > 0) {
    	$CONTENT = htmlcleaner::cleanup($CONTENT);
	  }    		
		
		/*проверка на ошибки*/
		$error="";
		if (!strlen($TITLE)) {$error.="Поле Заголовок обязательно для заполнения<br/>";}
		if (strlen($TITLE)>1000) {$error.="Поле Заголовок не может быть длиннее 1000 символов<br/>";}		

		if (strlen($error)) {
			echo "<div class='error'>$error</div>";
			if ($action=='update') $action='edit';
		}
		else {		  
      if ($_REQUEST['linked_id']) {
        linked_save($docid);
      }
      else {
        linked_del($docid);
      }
			if ($action=='update')
			{	
				$sql = "UPDATE multydoc SET title='$TITLE', content='$CONTENT' WHERE documentid = $docid";
				$result = $db->execute($sql, false);				
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$result = $db->execute($sql, false);
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{	
				getID();
				getSTID();
				
				$sql = "INSERT INTO multydoc (documentid, title) VALUES ($lastid, '$TITLE')";
				$result = $db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, content, doctype, stid) VALUES ($sid, $lastid, $lastid, '$TITLE', '$CONTENT', 'multydoc', $stid)";
				$result = $db->execute($sql, false);				
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование мультирегионального документа</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
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
	<td class="top2" valign="middle"><b>С</b>оздание мультирегионального документа</td>
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
			<td bgColor="#f4f4f4">Заголовок:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td valign="top" bgColor="#f4f4f4">Содержание:</td>
			<td></td>
			<td>
<?php
  if (isset($docid) && $docid> 0) {
    echo "<iframe src='mmaterial.php?mrid=$docid&sid=$sid' width='100%' height='700px' frameborder='no'></iframe>";
  } else {
    echo "Необходимо сначала сохранить мультирегиональный документ, после чего появится возможность создавать контент регионов.";
  }
?>      
      </td>
		</tr>	
		<tr>
			<td bgColor="#f4f4f4" valign="top">Нижний колонтитул:</td>
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
		<textarea id="myFF" cols="120" rows="50" style="width:100%; height:400px;" name="CONTENT"><?php echo $CONTENT;?></textarea>
			</td>
		</tr>		
<?php
  if (isset($docid) && $docid> 0) {
    require_once "linkedid.php";
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