<?php
  require_once "config.php";
  require_once "db_connect.php";
  require_once "lib.php";
  
	$error = array();
	$success = array();
  if (isset($_REQUEST['submit'])) {
	  $error['email'] = '';
    $email = clearField($_REQUEST['email']); 
    
    if (!strlen($email)) {$error['email'] = 'Введите E-mail';}
    else {
      $sql = "SELECT email FROM settings";
      $result = $db->execute($sql);
		  $myrow = mysql_fetch_object($result);
		  if ($myrow->email != $email) {
        $error['email'] = 'Неверный E-mail';
      } else {
        $temp = md5(uniqid(rand(), true)); 
        $sql = "UPDATE settings SET temp = '".$temp."'";
        $db->execute($sql, false);
        $body = 'Из AdminGo Вашего сайта был сделан запрос на восстановление пароля. Если Вы этого не делали, просто проигнорируйте данное письмо. Для восстановления пароля, перейдите по ссылке: '.$_SERVER['SERVER_NAME'].'/admingo/changepass.php?temp='.$temp;        
        /*$sucess = send_mime_mail('AdminGo', 
                       $email, 
                       'AdminGo', 
                       $email, 
                       'UTF8',  // кодировка, в которой находятся передаваемые строки 
                       'KOI8-R//IGNORE', // кодировка, в которой будет отправлено письмо 
                       'Восстановление пароля.', 
                       $body);       */
        mail($email, 'AdminGo', $body);   
        $success['email'] = 'На Вашу почту '.$email.' выслано письмо с подтверждением.';
      }                  
    }  
  } 
  
?>
<html>
<head>
	<title>CMS AdminGo</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="scripts/main.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="scripts/index.js"></script>
</head>
<body class="index">
  <div class="header">      
      <div class="logoWrap"><a class="logo" href="/admingo/"></a> — управление сайтом</div>
  </div>
  
  <div class="index_page">
    
    <form class="bs-docs-example form-horizontal" method="post" action="">        
      <legend>Восстановление пароля</legend>
<?php
  if (strlen($error['email'])) echo '<div class="alert alert-error">'.$error['email'].'</div>';
  else if (strlen($success['email'])) echo '<div class="alert alert-success">'.$success['email'].'</div>';
?>      
      <div class="control-group">
          <label class="control-label" for="email">E-mail</label>
          <div class="controls"><input type="email" name="email"/></div>    
      </div>
      <div class="control-group">
        <div class="controls"><button type="submit" name="submit" class="btn btn-primary">Отправить</button></div>        
      </div>
    </form>      

  </div>
</body>
</html>