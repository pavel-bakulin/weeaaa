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
		$sql = "DELETE FROM banners WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$sql = "SELECT title, type, link, linkdocid, width, height, info FROM banners WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_row($result))
		{
			$TITLE = htmlspecialchars($myrow[0]);
			$TYPE = $myrow[1];
			$LINK = $myrow[2];
			$LINKDOCID = $myrow[3];
			$WIDTH = $myrow[4];
			$HEIGHT = $myrow[5];
			$INFO = $myrow[6];
		}
	}
	else
	if (isset($_REQUEST['submit']))
	{
	  $error="";
		$TITLE=mysql_real_escape_string($_REQUEST['TITLE']);
		$TYPE=(int)$_REQUEST['TYPE'];
		$LINK=stripslashes(mysql_real_escape_string($_REQUEST['LINK']));
		if (get_magic_quotes_gpc())  { 
			$TITLE=stripslashes($TITLE);		 
			$LINK=stripslashes($LINK);
		}
		if (!is_integer(strpos($LINK,"http://")) && strlen($LINK)>0) {$LINK="http://".$LINK;}
		$LINKDOCID=(int)$_REQUEST['LINKDOCID'];
		$WIDTH=(int)$_REQUEST['WIDTH'];
		$HEIGHT=(int)$_REQUEST['HEIGHT'];
		$INFO=mysql_real_escape_string($_REQUEST['INFO']);

    $FILEPATH = '';    
    $tmp_name = $_FILES['IMAGE']['tmp_name'];
    //echo $tmp_name.'  '.$WIDTH;    
    if (strlen($tmp_name)>0) {
      $uploaddir = '../uploads/';  
      $fname = uniqid(true).substr($_FILES['IMAGE']['name'], strpos($_FILES['IMAGE']['name'],'.'));
      $FILEPATH = $uploaddir.$fname;
      if (!move_uploaded_file($tmp_name, $FILEPATH)) {$error.="файл не загружен";}
      $FILEPATH = '/'.$config->cms.'/'.$FILEPATH;
    }
    if ($TYPE == 1) {
      $img_params = getimagesize('http://'.$_SERVER['SERVER_NAME'].$FILEPATH);
      $WIDTH = (int)$img_params[0];
      $HEIGHT = (int)$img_params[1];
    }
		
		/* проверка на ошибки */		
		if ($TYPE > 0 && strlen($FILEPATH)==0 && $action!='update') {$error.="При указанном типе баннера необходимо загрузить файл<br/>";}
		if ($TYPE == 2 && ($WIDTH==0 || $HEIGHT==0)) {$error.="Укажите размер флешки<br/>";}
		if (strlen($TITLE)==0) {$error.="Поле Заголовок обязательно для заполнения<br/>";}
		if (strlen($TITLE)>255) {$error.="Поле Заголовок не может быть длиннее 255 символов<br/>";}
		if (strlen($LINK)>255) {$error.="Поле Cсылка не может быть длиннее 255 символов<br/>";}
 
		if (!strlen($error)) {	
			if ($action=='update')
			{	
			  if ($FILEPATH == '') {
			    $sql = "UPDATE banners SET title='$TITLE', type=$TYPE, link='$LINK', linkdocid='$LINKDOCID', width=$WIDTH, height=$HEIGHT, info='$INFO' WHERE documentid = $docid";
			  }
			  else {
				  $sql = "UPDATE banners SET title='$TITLE', file='$fname', type=$TYPE, link='$LINK', linkdocid='$LINKDOCID', width=$WIDTH, height=$HEIGHT, info='$INFO' WHERE documentid = $docid";
				}
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{	
				getID();
				getSTID();
				
				$sql = "INSERT INTO banners (documentid, title, file, type, link, linkdocid, width, height, info) VALUES ($lastid, '$TITLE', '$fname', $TYPE, '$LINK', $LINKDOCID, $WIDTH, $HEIGHT, '$INFO')";
				$db->execute($sql, false);
				
				insertToAllDocs($sid, $lastid, $lastid, $TITLE, 'banner', $stid);
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование баннера</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">	
	<script language="JavaScript" type="text/JavaScript" src="scripts/jquery-1.3.2.min.js"></script>
	<script language="JavaScript" type="text/JavaScript">
    $(document).ready(function(){
<?php 
if (!isset($TYPE)) $TYPE =1;
?>    
        chType(<?php echo (int)$TYPE; ?>);
    }); 
    function chType(type) {
      if (type==0) {
        document.getElementById("file_").style.display = "none";
        document.getElementById("sizes_").style.display = "none";
        document.getElementById("link_").style.display = "";
        document.getElementById("linkid_").style.display = "";
      }
      else if (type==1) {
        document.getElementById("file_").style.display = "";
        document.getElementById("sizes_").style.display = "none";
        document.getElementById("link_").style.display = "";
        document.getElementById("linkid_").style.display = "";
      }
      else if (type==2) {
        document.getElementById("file_").style.display = "";
        document.getElementById("sizes_").style.display = "";
        document.getElementById("link_").style.display = "none";
        document.getElementById("linkid_").style.display = "none";
      }  
    }
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
    <h1>Баннер</h1>        
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
			<td bgColor="#f4f4f4">Заголовок:</td>
			<td width="10"></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Тип баннера:</td>
			<td></td>
			<td>
<?php 
$che = array('','','');
if (isset($TYPE))
  $che[$TYPE] = 'checked="true"';
else $che[0] = 'checked="true"';
?>
<label class="radio">
  <input type="radio" name="TYPE" id="id_rad1" onClick="chType(1);" value="1" <?php echo $che[1] ?>/>
   картинка
</label> 
<label class="radio">
  <input type="radio" name="TYPE" id="id_rad0" onClick="chType(0);" value="0" <?php echo $che[0] ?>/>
   текстовый
</label>
<label class="radio">
  <input type="radio" name="TYPE" id="id_rad2" onClick="chType(2);" value="2" <?php echo $che[2] ?>/>
  флеш
</label>
			</td>
		</tr>		
		<tr id="file_">
			<td bgColor="#f4f4f4" valign="top">Загрузить файл:</td>
			<td></td>
			<td>
				<input type="file" name="IMAGE" class="inp" value=""/>
			</td>
		</tr>
		<tr id="sizes_">
			<td bgColor="#f4f4f4" valign="top">Размеры:</td>
			<td></td>
			<td>
				<table width="100%">
				<tr>
					<td>Ширина: </td>
					<td width="50%"><input type="text" name="WIDTH" class="inp" value="<?php echo $WIDTH; ?>"/></td>
					<td>Высота: </td>
					<td width="50%"><input type="text" name="HEIGHT" class="inp" value="<?php echo $HEIGHT; ?>"/></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr id="link_">
			<td bgColor="#f4f4f4">Ссылка:</td>
			<td></td>
			<td><input type="text" name="LINK" class="inp" value="<?php echo $LINK; ?>"/></td>
		</tr>
    <tr>
			<td bgColor="#f4f4f4">Инфо:</td>
			<td></td>
			<td><input type="text" name="INFO" class="inp" value="<?php echo $INFO; ?>"/></td>
		</tr>		
		</table>
  </div>		
</form>
</body>
</html>