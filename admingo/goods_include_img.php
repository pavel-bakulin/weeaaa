<?php
  require_once "config.php";	
	require_once "db_connect.php";

	$docid=(int)$_REQUEST['docid'];
?>	
<script language="JavaScript" type="text/JavaScript" src="/admingo/scripts/jquery-1.3.2.min.js"></script>
<script src="/zoom/image_zoom.js"></script>
<style type="text/css" media="all">@import url(/zoom/highslide.css);</style> 
<script src="/zoom/highslide.js"></script>
<script> hs.graphicsDir = 'zoom/graphics/';</script>

<span id="addFile">Загрузить</span><span class="fileinfo"></span><br/>
<div class="photoManage">
<?php
		$result = $db->execute("SELECT * FROM image_owner WHERE docid = $docid");
		while ($myrow = mysql_fetch_object($result)) {
		  if ((int)$myrow->main) $checked = 'checked="true"';
      else $checked = '';
		  echo '
  			 <div class="photo" iid="'.$myrow->id.'">
  			   <div class="item"><a href="'.$config->upfolder.$myrow->image.'" onClick="return hs.expand(this)" class="highslide"><img src="'.$config->upfolder.'small_'.$myrow->image.'"/></a></div>
  			   <input type="text" placeholder="Введите название" class="title" value="'.$myrow->title.'" iid="'.$myrow->id.'"/><br/>  			              
           <span class="del" iid="'.$myrow->id.'">удалить</span>			   
         </div>		  
		  ';
		}
?>
</div>       
<script type="text/javascript" src="/admingo/scripts/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/admingo/scripts/ajaxupload.js"></script>
<script>
$(function() {
  $('.photo .title').bind('blur', function(e) {
    var imgid = $(this).attr('iid');
    var title = $(this).val();
    params = {action:'title', imgid:imgid, title:title};
    $.ajax({
       type: "POST",
       url: "/admingo/goods_image.php",
       data: params,
       async: false,
       success: function(msg) {
        
       }          
    });      
  });
    
  $('.photo .del').bind('click', function(event) {
    var imgid = $(this).attr('iid');
    params = {action:'remove', imgid:imgid};
    $.ajax({
       type: "POST",
       url: "/admingo/goods_image.php",
       data: params,
       async: false,
       success: function(msg) {
          $('.photo[iid='+imgid+']').remove();
       }          
    });    
  });
  
	var btnUpload=$('#addFile');
	new AjaxUpload(btnUpload, {
		action: '/admingo/goods_image.php',
		name: 'uploadfile',
		data: {action: 'gallery', docid : <?php echo $docid; ?>},
		onSubmit: function(file, ext){
		  $('.fileinfo').hide();
		  $('#addFile').addClass('disabled');
			if (!(ext && /^(jpg|jpeg|png|bmp|gif)$/.test(ext))){       
        // extension is not allowed         
				$('.fileinfo').show().text('Недопустимый формат изображения.');
				return false;
			}				
			$('.fileinfo').show().text('Идет загрузка...');		
		},
		onComplete: function(file, response){		  
      $('#addFile').removeClass('disabled');
      $('.fileinfo').html(response);	
      location.reload();
		}
	});  
});
</script>
<style>
.photoManage {margin-top:20px;}
.photo {height:110px;min-height:110px;}
.photo .item {  
  height:100px;
  overflow:hidden;
  float:left;
  margin-right:10px;
}
.photo input.title {width:500px;padding-left:3px;}
.photo input.priority {width:50px;padding-left:3px;margin:3px 0;}
#addFile {font-weight:bold;margin-left:20px;}
.photo .del, #addFile {
  color: #C83400;
  border-bottom: 1px dotted #C83400;
  cursor: pointer;
}
.fileinfo {display:none;margin-left:20px;}
</style>		