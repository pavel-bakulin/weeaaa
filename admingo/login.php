<?php
  require_once "lib.php";
  $logined = false;
	if (isset($_COOKIE["admingostateid"]) && strlen($_COOKIE["admingostateid"])) {
		$stateid = clearField($_COOKIE["admingostateid"]);
		$sql = "SELECT stateid FROM settings";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			if ($stateid = $myrow->stateid) $logined = true;
		}
	}
	
  if (isset($_REQUEST['EXIT'])) {
  	setcookie("admingostateid", '', -1, '/');	
  	header("Location: /admingo/");
  }
  if ($logined) {return;}
?>

<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
<link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
<script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
<script src="/admingo/bootstrap/js/bootstrap.min.js"></script>
<style>
body {background: #F5EEE6;}
</style>
<script>
$(function() {
  $('#loginForm').on('submit', function(event) {  
    $('.alert').hide();
    if (!($('[name=login]',this).length && $('[name=login]',this).length)) {
      $('.alert').show();
      $('.alert span').text('Неправильная пара логин/пароль');
      return false;
    }  
    var params = $(this).serialize();
    $.ajax({
       type: "POST",
       url: "/admingo/login_action.php",
       data: params,
       async: false,
       success: function(result){
         var success = '';
         var error = '';
         eval(result);           
         if (error.length) {
          $('.alert').show();
          $('.alert span').text(error);
          return false;
         } else {
            location.reload();
         }
         return false;
       }
    });
    return false;         
  });
});
</script>
<div class="container" style="margin-top:150px;">
    <div class="row">
		<div class="span4 offset4 well">
			<legend>Admin</legend>
    	<div class="alert alert-error" style="display:none;">
          <a class="close" data-dismiss="alert" href="#">×</a><span></span>
      </div>
			<form id="loginForm" accept-charset="UTF-8">
			<input type="text" id="username" class="span4" name="login" placeholder="Логин">
			<input type="password" id="password" class="span4" name="password" placeholder="Пароль">
			<button type="submit" name="submit" class="btn btn-info btn-block">Войти</button>
			</form>
		</div>
	</div>
</div>

<?php            
	die();	
?>