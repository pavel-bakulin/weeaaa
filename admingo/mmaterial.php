<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	require_once "htmlcleaner.php";

	$mrid = (int)$_REQUEST['mrid'];
	$regionid = (int)$_REQUEST['regionid'];
	$action = $_REQUEST['action'];
	$sid = (int)$_REQUEST['sid'];
	
  if (!$mrid) {die();}
  if (!$regionid) {
    echo '<link rel="stylesheet" type="text/css" href="css/style.css"><div class="regions"><h1>Выберите регион</h1><div>';
  	$sql = "SELECT * FROM region";
  	$result = $db->execute($sql);	
  	$path = '';
  	while ($myrow = mysql_fetch_object($result)) {
  	   echo "<a href='mmaterial.php?mrid=$mrid&regionid=$myrow->id&action=edit&sid=$sid'>$myrow->name</a>";
       if ($path!=$myrow->path) {
          $path = $myrow->path;
          echo '</div><div>';
       } 
  	} 
    echo '</div></div>';
    die();   
  }		  
	
	$sql = "SELECT * FROM region WHERE id = $regionid";
	$result = $db->execute($sql);	
	if ($myrow = mysql_fetch_object($result)) {
	   $region = $myrow->name; 
	}		
	$DATE = date('Y-m-d');
	if ($action=='edit') {
		$sql = "SELECT * FROM mmaterial WHERE mrid = $mrid AND regionid = $regionid";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {
			$TITLE = stripslashes(htmlspecialchars($myrow->title,ENT_QUOTES));
			$CONTENT = $myrow->content;
			$KEYWORDS = $myrow->keywords;
			$PAGETITLE = $myrow->pagetitle;
			$METADESCRIPTION = $myrow->metadescription;
			$DATE = $myrow->date;
		}
		if (strlen($DATE)) $DATE = substr($DATE,0,10);
	}
	else
	if (isset($_REQUEST['submit'])) {
		$TITLE=trim(mysql_real_escape_string($_REQUEST['TITLE']));
		$KEYWORDS=trim(mysql_real_escape_string($_REQUEST['KEYWORDS']));
		$PAGETITLE=trim(mysql_real_escape_string($_REQUEST['PAGETITLE']));
		$METADESCRIPTION=trim(mysql_real_escape_string($_REQUEST['METADESCRIPTION']));		
		$DATE=trim($_REQUEST['DATE']);		
		
		if (get_magic_quotes_gpc())  { 
			$CONTENT=stripslashes(quote2code($_REQUEST['CONTENT'],ENT_QUOTES));		 
			$DESCRIPTION = stripslashes($DESCRIPTION);
			$KEYWORDS = stripslashes($KEYWORDS);
			$PAGETITLE = stripslashes($PAGETITLE);
			$METADESCRIPTION = stripslashes($METADESCRIPTION);
			$DATE = stripslashes($DATE);
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
		if (strlen($TITLE)==0) {$error.="Поле Заголовок обязательно для заполнения<br/>";}
		if (strlen($TITLE)>1000) {$error.="Поле Заголовок не может быть длиннее 1000 символов<br/>";}		
		$temp = $DATE; 
		$DATE = checkDateTime($DATE);		 
		if ($DATE === false) {$error.="Не правильный формат даты.<br/>"; $DATE = $temp;}
		
		date('Y-m-d H:i:s');
				
		if (strlen($error)>0)
		{
			echo "<div class='error'>$error</div>";
			if ($action=='update') $action='edit';
		}
		else
		{
			$sql = "REPLACE INTO mmaterial (mrid, regionid, title, content, keywords, metadescription, pagetitle, date) 
              VALUES ($mrid, $regionid, '$TITLE', '$CONTENT', '$KEYWORDS', '$METADESCRIPTION', '$PAGETITLE', '$DATE')";
			$result = $db->execute($sql, false);
			header("Location: $_SERVER[PHP_SELF]?mrid=$mrid&regionid=$regionid&action=edit&sid=$sid");			
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование материала</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/datepicker.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="scripts/ui.datepicker.js"></script>
	<script>
    $(function() {  
      $('.datepick').attachDatepicker({dateFormat: 'yy-mm-dd'});
    });	
	</script>	
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td><img src="images/0.gif" width="6" height="6"/></td>
	<td></td>
	<td width="100%"><b><?php echo $region;?></b>&nbsp;<a class="links2" href="mmaterial.php?mrid=<?php echo $mrid;?>&sid=<?php echo $sid;?>">сменить регион</a></td>
	<td></td>
	<td><img src="images/0.gif" width="6" height="6"/></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
<?php
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="fckform" id="fckform">';
  echo "<input type='hidden' name='sid' value='$sid'/>";	
	echo "<input type='hidden' name='mrid' value='$mrid'/>";
	echo "<input type='hidden' name='regionid' value='$regionid'/>";
?>
		<table class="conttbl2" id="paddtbl">
		<tr>
			<td><img src="images/0.gif" width="120" height="6"/></td>
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
			<td>Заголовок:</td>
			<td></td>
			<td><input type="text" name="TITLE" class="inp" value="<?php echo $TITLE; ?>"/></td>
		</tr>	    	
		<tr>
			<td valign="top">Содержание:</td>
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
		<tr>
			<td></td>
			<td></td>
			<td><input type="checkbox" name="cleanup" value="1" id="clearcode"/>
			<label for="clearcode">Установите этот флаг, если вы копируете текст из Word, Excel или с другого сайта.</label>
      </td>
		</tr>
		<tr>
			<td>Дата:</td>
			<td></td>
			<td><input type="text" name="DATE" class="inp2 datepick" value="<?php echo $DATE; ?>"/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><b>Блок поисковой оптимизации:</b></td>
		</tr>
		<tr>
			<td>Ключевые слова:</td>
			<td></td>
			<td><input type="text" name="KEYWORDS" class="inp" value="<?php echo $KEYWORDS; ?>"/></td>
		</tr>
		<tr>
			<td>Описание:</td>
			<td></td>
			<td><input type="text" name="METADESCRIPTION" class="inp" value="<?php echo $METADESCRIPTION; ?>"/></td>
		</tr>
		<tr>
			<td>Заголовок страницы:</td>
			<td></td>
			<td><input type="text" name="PAGETITLE" class="inp" value="<?php echo $PAGETITLE; ?>"/></td>
		</tr>
		<tr height="100%">
			<td></td>
			<td></td>
			<td valign="top"><input type="submit" name="submit" class="btn" style="width:auto;" value="Сохранить контент для <?php echo $region;?>"/></td>
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