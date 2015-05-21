<script>
$(function() {
  $('#addAdminForm').on('submit', function(event) {
    lib.flag = 'addAdminForm'; 
    if ($('button[type=submit]',this).hasClass('disabled')) return false;
    $('button[type=submit]',this).addClass('disabled');    
    var error = '';
    var req=true;
    window.lib.hideErrors();
    $("[req='true']:visible",this).each(function (i) {
      $(this).parent().parent().removeClass('error');
      if (!$(this).val().length) {
        $(this).parent().parent().addClass('error');
        req=false;        
      }
    });
    if (!req) error = 'Заполнены не все обязательные поля.';
    if (error.length) {
      window.lib.showErrors(error,this, false);
      $('button[type=submit]',this).removeClass('disabled');
      return false;
    } else {        
      var params = $(this).serialize()+'&action=add';
      $.ajax({
         type: "POST",
         url: "/admingo/adminuser.php",
         data: params,
         async: false,
         success: function(result){
           var success = '';
           var error = '';
           eval(result);
           if (error.length) {
            window.lib.showErrors(error,$('#'+lib.flag),false);
            $('#'+lib.flag+' button[type=submit]').removeClass('disabled');
           } else {
              location.reload();
           }
           $('#'+lib.flag+' button[type=submit]').removeClass('disabled');           
           return false;
         }
      });          
      return false;      
    }  
  });      
  
  $('#accessTable td.login,#accessTable td.password,#accessTable td.sid').on('click', function(event) {
    $(this).find('span').hide();
    $(this).find('input').show().focus();  
  });

  $('#accessTable input').on('blur', function(event) {
    var iid = $(this).attr('iid');
    var val = $(this).val();
    var params = 'action='+$(this).attr('action')+'&val='+val+'&iid='+iid;
    $.ajax({
       type: "POST",
       url: "/admingo/adminuser.php",
       data: params,
       async: true,
       success: function(result){           
         return false;
       }
    });
    $(this).hide();
    $(this).parent().find('span').text(val).show();  
  });
  
  $('#accessTable .remove').on('click', function(event) {
    if (confirm('Удалить администратора?')) {
      var iid = $(this).attr('iid');    
      var params = 'action=remove&iid=' + iid;
      $.ajax({
         type: "POST",
         url: "/admingo/adminuser.php",
         data: params,
         async: true,
         success: function(result){           
           return false;
         }
      });
      $('#accessTable tr[iid='+iid+']').hide(300);      
    }
  });
  
  $('#accessTable .sid span').on('mouseover', function(event) {
      lib.flag = $(this).attr('iid'); 
      var params = 'action=getnames&sids=' + $(this).text();
      $.ajax({
         type: "POST",
         url: "/admingo/adminuser.php",
         data: params,
         async: true,
         success: function(result){           
           $('#accessTable .sid span[iid='+lib.flag+']').attr('title',result)
         }
      });    
  });  
});  
</script>
<style>
#accessTable input[type='text'] {display:none;}  
</style>
<div class="index_page">
<legend>Администраторы сайта</legend>
<?php
		$sql = "SELECT * FROM access ORDER BY id DESC";
		$result = $db->execute($sql);
		if (mysql_num_rows($result)) echo '<table id="accessTable" class="table table-striped table-bordered table-condensed"><thead><tr><th>Логин</th><th>Пароль</th><th>Доступ</th><th></th></tr></thead>';
		while ($myrow = mysql_fetch_object($result)) {
      echo "<tr iid='$myrow->id'>
              <td width='30%' class='login'><span iid='$myrow->id'>$myrow->login</span><input type='text' action='login' class='loginInp' value='$myrow->login' iid='$myrow->id'/></td>
              <td width='30%' class='password'><span iid='$myrow->id'>$myrow->password</span><input type='text' action='password' class='passwordInp' value='$myrow->password' iid='$myrow->id'/></td>
              <td width='30%' class='sid'><span iid='$myrow->id'>$myrow->sid</span><input type='text' action='sid' class='sidInp' value='$myrow->sid' iid='$myrow->id'/></td>
              <td width='10%'>
                <button class='btn btn-mini btn-danger remove' iid='$myrow->id' type='button'>удалить</button>
              </td>
            </tr>";
    }
    if (mysql_num_rows($result)) echo '</table>';  
?>  
  
  <form class="bs-docs-example form-horizontal" id="addAdminForm">
    <legend>Добавить администратора сайта</legend>
    <div class="control-group">
        <label class="control-label" for="inputEmail">Логин</label>
        <div class="controls"><input type="text" name="login" id="inputEmail" req="true"/></div>    
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPassword">Пароль</label>
        <div class="controls"><input type="password" name="password" id="inputPassword" req="true"/></div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputSid">Доступные разделы</label>
        <div class="controls"><input type="text" name="sections" id="inputSid" req="true" placeholder="Например: 10,100,15"/></div>
    </div>    
    <div class="control-group">
      <div class="controls"><button type="submit" class="btn btn-primary">Добавить</button></div>        
    </div>
  </form>
</div>
</body>
</html>
<?php
  die();
?>