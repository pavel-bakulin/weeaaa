<?php
  require_once "config.php";	
	require_once "db_connect.php";
	require_once "login.php";
	require_once "lib.php";
	require_once "htmlcleaner.php";

	$sid=intval($_REQUEST['sid']);
	$docid=intval($_REQUEST['docid']);
	$action=$_REQUEST['action'];
		
	if ($action=='delete') 
	{
		$sql = "DELETE FROM goods WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
    $sql = "DELETE FROM linkeds WHERE docid = $docid OR linkedid = $docid;";
    $db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$result = $db->execute("SELECT * FROM goods WHERE documentid = $docid");
		while ($myrow = mysql_fetch_object($result)) {
			$title = htmlspecialchars($myrow->title);
			$code = htmlspecialchars($myrow->code);
			$description = br2rn($myrow->description);
			$image = $myrow->image;
			$quantity = $myrow->quantity;
			$content = $myrow->content;
			$price1  = $myrow->price1;
			$price2  = $myrow->price2;
  		$param1 = $myrow->param1;
  		$param2 = $myrow->param2;
  		$param3 = $myrow->param3;
  		$param4 = $myrow->param4;
  		$param5 = $myrow->param5;
  		$param6 = $myrow->param6;
  		$param7 = $myrow->param7;
  		$param8 = $myrow->param8;
  		$param9 = $myrow->param9;
  		$param10 = $myrow->param10;
  		$param11 = $myrow->param11;
  		$param12 = $myrow->param12;
  		$param13 = $myrow->param13;            
      $date = $myrow->date;      
			$keywords = $myrow->keywords;
			$pagetitle = $myrow->pagetitle;
			$metadescription = $myrow->metadescription;
			$available = $myrow->available;		
			$market = $myrow->market;
		}
		mysql_free_result($result);
	}
	else
	if (isset($_REQUEST['submit'])) {
		$title = mysql_real_escape_string($_REQUEST['title']);
		$description = rn2br($_REQUEST['description']);	
    $image = mysql_real_escape_string($_REQUEST['image']);
    $param1 = $_REQUEST['param1'];	
    $param2 = $_REQUEST['param2'];
    $param3 = $_REQUEST['param3'];
    $param4 = $_REQUEST['param4'];
    $param5 = $_REQUEST['param5'];
    if (is_array($_REQUEST['param6'])) {
      $param6_a = $_REQUEST['param6'];
      if (count($param6_a))
        foreach ($param6_a as $value) {
          $param6 .= ','.$value.',';          
        }            
    } else {
      $param6 = $_REQUEST['param6'];
    }            
    $param7 = $_REQUEST['param7'];
    $param8 = $_REQUEST['param8'];
    $param9 = $_REQUEST['param9'];
    $param10 = $_REQUEST['param10'];
    $param11 = $_REQUEST['param11'];	
    $param12 = $_REQUEST['param12'];
    $param13 = $_REQUEST['param13'];        
    $date = $_REQUEST['date'];
		$code = $_REQUEST['code'];
		$keywords = trim(mysql_real_escape_string($_REQUEST['keywords']));
		$pagetitle = trim(mysql_real_escape_string($_REQUEST['pagetitle']));
		$metadescription = trim(mysql_real_escape_string($_REQUEST['metadescription']));
		$available = (int)$_REQUEST['available'];
		$market = (int)$_REQUEST['market'];
		$quantity = (int)$_REQUEST['quantity'];		
		
		if (get_magic_quotes_gpc())  { 
			$title=stripslashes($title);		 
			$description=stripslashes($description);
			$keywords = stripslashes($keywords);
			$pagetitle = stripslashes($pagetitle);
			$metadescription = stripslashes($metadescription);
		}		
		$price1 = floatval($_REQUEST['price1']); 							           	
		$price2 = floatval($_REQUEST['price2']);
		
		if (get_magic_quotes_gpc()) { 
			$content = stripslashes(quote2code($_REQUEST['content']));
		}
		else { 
			$content = quote2code($_REQUEST['content']);
		}		
	  $cleanup = intval($_REQUEST['cleanup']);
	  if ($cleanup) {
    	$content = htmlcleaner::cleanup($content);
	  }
		
		/*проверка на ошибка*/
		$error="";
		if (strlen($title)==0) {$error.="Поле Заголовок обязательно для заполнения<br/>";}
		if (strlen($title)>500) {$error.="Поле Заголовок не может быть длиннее 500 символов<br/>";}
				
		if (strlen($error)) {
			if ($action=='update') $action='edit';
		}
		else {	
      if ($_REQUEST['linked_id']) {        
        linked_save($docid);
      }
      else {
        linked_del($docid);
      }
      
			if ($action=='update') {      	
				$sql = "UPDATE goods SET title='$title', code='$code', description='$description', content='$content', image='$image', date='$date', price1=$price1, price2=$price2, param1='$param1', param2='$param2', param3='$param3', param4='$param4', param5='$param5', param6='$param6', param7='$param7', param8='$param8', param9='$param9', param10='$param10', param11='$param11', param12='$param12', param13='$param13', keywords='$keywords', quantity=$quantity, metadescription='$metadescription', pagetitle='$pagetitle', available=$available, market=$market WHERE documentid = $docid";				
				$db->execute($sql, false);
				$sql = "UPDATE alldocs SET title='$title' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else {        			
				getID();
				getSTID();
				
				$sql = "INSERT INTO goods (documentid, title, code, description, content, image, date, price1, price2, param1, param2, param3, param4, param5, param6, param7, param8, param9, param10, param11, param12, param13, keywords, metadescription, pagetitle, available, market, quantity) VALUES ($lastid, '$title', '$code', '$description', '$content', '$image', '$date', $price1, $price2, '$param1', '$param2', '$param3', '$param4', '$param5', '$param6', '$param7', '$param8', '$param9', '$param10', '$param11', '$param12', '$param13', '$keywords', '$metadescription', '$pagetitle', $available, $market, $quantity)";
				//echo $sql;
				$db->execute($sql, false);
				
				insertToAllDocs($sid, $lastid, $lastid, $title, 'goods', $stid);
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Товар</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/datepicker.css">
	<script type="text/javascript" src="scripts/bootstrap-datepicker.js"></script>			
	 <script src="/zoom/image_zoom.js"></script>
	 <style type="text/css" media="all">@import url(/zoom/highslide.css);</style> 
	 <script src="/zoom/highslide.js"></script>
	 <script> hs.graphicsDir = 'zoom/graphics/';</script>
   <script src="/admingo/scripts/ajaxupload.js"></script>
	<script>
$(function() {
  $('.datepick').datepicker({format:'yyyy-mm-dd'});
    
	var btnUpload=$('#addFileImage');
	new AjaxUpload(btnUpload, {
		action: '/admingo/goods_image.php',
		data: {action: 'goodsimage', docid : <?php echo $docid; ?>},
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
  $('#delFileImage').on('click', function(event) {
    if (confirm('Удалить картинку?')) {
      $('#imageImg').hide();
      $('#delFileImage').hide();
      $('#imageInput').val('');
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
    <h1>Товар</h1>        
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
			<td bgColor="#f4f4f4">Наименование:</td>
			<td></td>
			<td><input type="text" name="title" class="inp" value="<?php echo $title; ?>"/></td>
		</tr> 
		<tr>
			<td bgColor="#f4f4f4">Цена:</td>
			<td></td>
			<td>			
			  <input type="text" name="price1" class="input-medium" value="<?php echo $price1; ?>"/> руб
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Старая:
			  <input type="text" name="price2" class="input-medium" value="<?php echo $price2; ?>"/> руб
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Артикул:</td>
			<td></td>
			<td>			
			  <input type="text" name="code" class="input-medium" value="<?php echo $code; ?>"/>
      </td>
		</tr>  
		<tr>
			<td valign="top">Кратко:</td>
			<td></td>
			<td><textarea name="description"><?php echo $description; ?></textarea></td>
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
		window.onload = function(){      
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
		<textarea id="myFF" cols="120" rows="50" style="width:100%; height:400px;" name="content"><?php echo $content;?></textarea>
			</td>
		</tr>	
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td><input type="checkbox" name="cleanup" value="1" id="clearcode"/>
			<label for="clearcode">Установите этот флаг, если вы копируете текст из Word, Excel или с другого сайта.</label>
      </td>
		</tr>			
		<!--<tr>
			<td bgColor="#f4f4f4">Дата:</td>
			<td></td>
			<td><input type="text" name="date" class="input-medium datepick" value="<?php echo $date; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td>
		       <input type="hidden" name="market" value="1"/>
           <input type="hidden" name="available" value="1"/> 
      </td>
		</tr>	-->					
		<input type="hidden" name="available" value="1"/>
		<tr>
			<td bgColor="#f4f4f4">Размеры:</td>
			<td></td>
			<td>			
			  <input type="text" name="param1" class="input-medium" value="<?php echo $param1; ?>"/>
      </td>
		</tr>    		
		<tr>
			<td bgColor="#f4f4f4">Вес:</td>
			<td></td>
			<td>			
			  <input type="text" name="param2" class="input-medium" value="<?php echo $param2; ?>"/>
      </td>
		</tr>		
		<!--<tr>
			<td bgColor="#f4f4f4">Количество:</td>
			<td></td>
			<td>			
			  <input type="text" name="quantity" class="input-medium" placeholder="Наличие" value="<?php echo $quantity; ?>"/>
      </td>
		</tr>			
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td>			
			  <table width="100%">
			  <tr>
  			 <td>
    			  <input type="checkbox" name="param10" id="param10" value="1" <?php if ($param10==1) echo 'checked="checked"'; ?>/>
    			  <label for="param10">Скидки</label>  			 
  			 </td>
  			 <td>
    			  <input type="checkbox" name="param11" id="param11" value="1" <?php if ($param11==1) echo 'checked="checked"'; ?>/>
    			  <label for="param11">Новинка</label>  			 
  			 </td>
			  </tr>
			  </table>
      </td>
		</tr>-->
		<tr>
			<td bgColor="#f4f4f4">Картинка:</td>
			<td></td>
			<td>
			   <input type="hidden" name="image" value="<?php echo $image; ?>" id="imageInput"/>			   
			   <img src="/uploads/<?php echo $image; ?>" id="imageImg" <?php if(!strlen($image)) echo 'style="display:none"'; ?>/>
			   <span class="dottedlink" id="addFileImage">загрузить</span>
			   <?php if (strlen($image)) echo '<br/><span class="dottedlink" id="delFileImage">удалить</span>'; ?>
         <input type="hidden" id="imgwidth" class="input-medium"/>      
      </td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4"></td>
			<td></td>
			<td><b>Блок поисковой оптимизации:</b></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Ключевые слова:</td>
			<td></td>
			<td><input type="text" name="keywords" class="inp" value="<?php echo $keywords; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Описание:</td>
			<td></td>
			<td><input type="text" name="metadescription" class="inp" value="<?php echo $metadescription; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Заголовок страницы:</td>
			<td></td>
			<td><input type="text" name="pagetitle" class="inp" value="<?php echo $pagetitle; ?>"/></td>
		</tr>
<?php
  if (isset($docid) && $docid> 0) {
    require_once "linkedid.php";
  }  
?>
		<tr>
			<td bgColor="#f4f4f4" valign="top">Фотографии:</td>
			<td></td>
			<td>
<? if ($action=='edit') { ?> 			
        <iframe src="goods_include_img.php?docid=<?php echo $docid;?>" width="100%" height="400px;"></iframe>
<? } else { ?>
        Необходимо сначала сохранить объект, затем появится возможность добавлять фотографии.
<? } ?>
      </td>
		</tr>
		</table>		
</div>
</form>
</body>
</html>