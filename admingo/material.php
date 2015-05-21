<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	require_once "htmlcleaner.php";

	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
	
	$DATE = date('Y-m-d');
	if ($action=='delete') 
	{
		$sql = "DELETE FROM materials WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";		
		$db->execute($sql, false);
		$sql = "DELETE FROM linkeds WHERE docid = $docid OR linkedid = $docid";		
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') {
		$sql = "SELECT * FROM materials WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result))
		{
			$TITLE = htmlspecialchars(br2rn($myrow->title,ENT_QUOTES));
			$DESCRIPTION = htmlspecialchars(br2rn($myrow->description,ENT_QUOTES));
			$CONTENT = $myrow->content;
			$KEYWORDS = $myrow->keywords;
			$PAGETITLE = $myrow->pagetitle;
			$METADESCRIPTION = $myrow->metadescription;
			$IMAGEID = $myrow->imageid;
			$IMAGE = $myrow->image;
			$DATE = $myrow->date;
			$file = $myrow->file;
			$info = $myrow->info;
		}
		if (strlen($DATE)) $DATE = substr($DATE,0,10);
	}
	else
	if (isset($_REQUEST['submit'])) {
		$TITLE=trim(mysql_real_escape_string(rn2br($_REQUEST['TITLE'])));
		$DESCRIPTION=trim(mysql_real_escape_string(rn2br($_REQUEST['DESCRIPTION'])));
		$KEYWORDS=trim(mysql_real_escape_string($_REQUEST['KEYWORDS']));
		$PAGETITLE=trim(mysql_real_escape_string($_REQUEST['PAGETITLE']));
		$METADESCRIPTION=trim(mysql_real_escape_string($_REQUEST['METADESCRIPTION']));
		$IMAGE=mysql_real_escape_string($_REQUEST['IMAGE']);
		$DATE=trim($_REQUEST['DATE']);
		$info=trim($_REQUEST['info']);
		$file=$_REQUEST['file'];
		$IMAGEID=intval($_REQUEST['IMAGEID']);				
		if (!($IMAGEID>0)) {$IMAGEID="0";}
		
		if (get_magic_quotes_gpc())  { 
			$CONTENT=stripslashes(quote2code($_REQUEST['CONTENT'],ENT_QUOTES));		 
			$NAME = stripslashes($NAME);
			$DESCRIPTION = stripslashes($DESCRIPTION);
			$TITLE = stripslashes($TITLE);
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
				
		if (strlen($error)) {
			if ($action=='update') $action='edit';
		}
		else
		{		  
      if ($_REQUEST['linked_id']) {
        linked_save($docid);
      }
      else {
        linked_del($docid);
      }
			if ($action=='update')
			{	
				$sql = "UPDATE materials SET title='$TITLE', file='$file', description='$DESCRIPTION', content='$CONTENT', keywords='$KEYWORDS', metadescription='$METADESCRIPTION', pagetitle='$PAGETITLE', image='$IMAGE', imageid=$IMAGEID, date='$DATE', info='$info' WHERE documentid = $docid";
				$result = $db->execute($sql, false);				
				$sql = "UPDATE alldocs SET title='$TITLE' WHERE documentid = $docid";
				$result = $db->execute($sql, false);
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{	
				getID();
				getSTID();
				
				$sql = "INSERT INTO materials (documentid, title, file, description, content, keywords, metadescription, pagetitle, imageid, image, date, info) VALUES ($lastid, '$TITLE', '$file', '$DESCRIPTION', '$CONTENT', '$KEYWORDS', '$METADESCRIPTION', '$PAGETITLE', $IMAGEID, '$IMAGE', '$DATE', '$info')";
				$result = $db->execute($sql, false);
								
				insertToAllDocs($sid, $lastid, $lastid, $TITLE, 'material', $stid);
				
				/* корневой документ родительской папки */
  			$sql = "SELECT rootdocid FROM sections WHERE id = $sid";
  			$result = $db->execute($sql);
  			if ($myrow = mysql_fetch_object($result)) {
  			 $sql2 = "SELECT count(*) as count FROM alldocs WHERE sid = $sid AND doctype='material'";
  			 $result2 = $db->execute($sql2);
  			 if ($myrow2 = mysql_fetch_object($result2)) {
  			   if ($myrow2->count == 1 && !(int)$myrow->rootdocid) { //материал в папке только один, а корневой документ ещё не назначен
            $sql = "UPDATE sections SET rootdocid=$lastid WHERE id = $sid";
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
	<title>Создание/редактирование материала</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/datepicker.css">
	<script type="text/javascript" src="scripts/bootstrap-datepicker.js"></script>
	<script src="/admingo/scripts/ajaxupload.js"></script>
	<script>
$(function() {  
  $('.datepick').datepicker({format:'yyyy-mm-dd'});
  
	var btnUpload=$('#addFileImage');
	new AjaxUpload(btnUpload, {
		action: '/admingo/goods_image.php',
		data: {action: 'image', ximgwidth: $('#imgWidth').val(), docid : <?php echo $docid; ?>},
		name: 'uploadfile',
		onSubmit: function(file, ext){
		  $('.fileinfo').hide();
		  $('#addFileImage').addClass('disabled');
			if (!(ext && /^(jpg|jpeg|png|bmp|gif)$/.test(ext))){       
        // extension is not allowed         
				$('.fileinfo').show().text('Недопустимый формат изображения.');
				return false;
			}				
			$('.fileinfo').show().text('Идет загрузка...');		
		},
		onComplete: function(file, response){		  
      $('#addFileImage').removeClass('disabled');
  		var clearResponse = response.replace('<embed id="embed_npwlo" type="application/npwlo" height="0">','');  	
  		var base = '/uploads/';                               
      $("#imageImg").attr('src', base + clearResponse).show();
      $("#imageInput").val(clearResponse);
      if (!$('#delFileImage').length) $('<br/><span class="dottedlink" id="delFileImage">удалить</span>').insertAfter('#addFileImage');
      $('.fileinfo').hide();                 
		}
	});	
  $('#delFileImage').live('click', function(event) {
    if (confirm('Удалить картинку?')) {
      $('#imageImg').hide();
      $('#delFileImage').hide();
      $('#imageInput').val('');
    }
  });  	            
  
	var btnUpload=$('#addFile');
	new AjaxUpload(btnUpload, {
		action: '/admingo/goods_image.php',
		data: {action: 'file', docid : <?php echo $docid; ?>},
		name: 'uploadfile',
		onSubmit: function(file, ext){
		  $('.fileinfo').hide();
		  $('#addFile').addClass('disabled');				
			$('.fileinfo').show().text('Идет загрузка...');		
		},
		onComplete: function(file, response){		  
      $('#addFile').removeClass('disabled');
  		var clearResponse = response.replace('<embed id="embed_npwlo" type="application/npwlo" height="0">','');  	
  		var base = '/uploads/';                               
      $("#fileInput").val(clearResponse);
      $("#fileName").html(clearResponse);
      if (!$('#delFile').length) $('<br/><span class="dottedlink" id="delFile">удалить</span>').insertAfter('#addFile');
      $('.fileinfo').hide();                 
		}
	});	
  $('#delFile').live('click', function(event) {
    if (confirm('Удалить файл?')) {
      $('#fileName').hide();
      $('#delFile').hide();
      $('#fileInput').val('');
    }
  });
});	
	</script>	
</head>
<body>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="fckform" id="fckform">
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
    <h1>Материал</h1>        
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
			<td>Заголовок:</td>
			<td></td>
			<td><textarea style="height:55px;" name="TITLE"><?php echo $TITLE; ?></textarea></td>
		</tr>
		<tr>
			<td valign="top">Описание:</td>
			<td></td>
			<td><textarea name="DESCRIPTION"><?php echo $DESCRIPTION; ?></textarea></td>
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
			<td><input type="text" name="DATE" class="input-medium datepick" value="<?php echo $DATE; ?>"/></td>
		</tr>
		<tr>
			<td>Картинка:</td>
			<td></td>
			<td>
			   <input type="hidden" name="IMAGE" value="<?php echo $IMAGE; ?>" id="imageInput"/>			   
			   <img src="/uploads/<?php echo $IMAGE; ?>" id="imageImg" <?php if(!strlen($IMAGE)) echo 'style="display:none"'; ?>/>
			   <span class="dottedlink" id="addFileImage">загрузить</span>
			   <?php if (strlen($IMAGE)) echo '<br/><span class="dottedlink" id="delFileImage">удалить</span>'; ?>
         <!--<input type="text" id="imgWidth" class="input-medium" placeholder="Ширина (пикс)"/>-->      
      </td>
		</tr>    
		<tr>
			<td>Файл:</td>
			<td></td>
			<td>
			   <input type="hidden" name="file" value="<?php echo $file; ?>" id="fileInput"/>			   
			   <span class="dottedlink" id="addFile">загрузить</span>
			   <span id="fileName"><?php echo $file; ?></span>
			   <?php if (strlen($file)) echo '<br/><span class="dottedlink" id="delFile">удалить</span>'; ?>      
      </td>
		</tr>
		<tr>
			<td>Инфо:</td>
			<td></td>
			<td><input type="text" name="info" class="inp" value="<?php echo $info; ?>"/></td>
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
<?php
  if (isset($docid) && $docid> 0) {
    require_once "linkedid.php";
  }  
?>		
		</table>		
  </div>
</form>
</body>
</html>