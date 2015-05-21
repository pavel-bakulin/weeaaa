<?php
  error_reporting(E_ERROR);
  setlocale(LC_ALL,'ru_RU.UTF-8');
  require_once "config.php";
  require_once "db_connect.php";
  $config->rootOnly = true;                                   
	require_once "login.php";  	
	
	$error = array();
	$success = array();
	if (isset($_REQUEST['submit_password'])) {
	  $error['password'] = '';
    $password = trim($_REQUEST['password']); 
    $password2 = trim($_REQUEST['password2']);
    
    if ($password != $password2) {$error['password'] = 'Пароли не совпадают';}
    else {
      $password = md5($password);
      $sql = "UPDATE settings SET password = '$password'";
      $db->execute($sql, false);
      $success['password'] = 'Изменения внесены';
    }
  } else if (isset($_REQUEST['submit_email'])) {
	  $error['email'] = '';
    $email = trim($_REQUEST['email']); 
    
    if (!strlen($email)) {$error['email'] = 'Введите E-mail';}
    else {
      $sql = "UPDATE settings SET email = '$email'";
      $db->execute($sql, false);
      $success['email'] = 'Изменения внесены';
    }  
  } else if (isset($_REQUEST['submit_code'])) {
    $code = trim(mysql_real_escape_string($_REQUEST['code']));
    $sql = "UPDATE settings SET code = '$code'";
    $db->execute($sql, false);
    $success['code'] = 'Изменения внесены';     
  }
  $sql = "SELECT email, code FROM settings";
  $result = $db->execute($sql);
  if ($myrow = mysql_fetch_object($result)) {
  	$email = $myrow->email;
  	$code = $myrow->code;
  }    

  include 'header.php';  
?>

<div class="index_page">
   <h4>Смена пароля</h4>   
   <form method="post" action="">
<?php
  if (strlen($error['password'])) echo '<font color="red">'.$error['password'].'</font><br/>';
  else if (strlen($success['password'])) echo '<font color="green">'.$success['password'].'</font><br/>';
?>   
    <table id="paddtbl">
    <tr>
      <td>Пароль:</td><td><input type="password" name="password" value=""/></td>
    </tr>
    <tr>
      <td>Ещё раз:</td><td><input type="password" name="password2" value=""/></td>
    </tr>
    <tr>           
      <td colspan="2"><input type="submit" name="submit_password" class="btn" value="Отправить"/></td>
    </tr>
    </table> 
   </form>
   <br/><br/><br/>   
   
   <h4>E-mail администратора</h4>
   <form method="post" action="">
<?php
  if (strlen($error['email'])) echo '<font color="red">'.$error['email'].'</font><br/>';
  else if (strlen($success['email'])) echo '<font color="green">'.$success['email'].'</font><br/>';
?>    
    <table id="paddtbl">
    <tr>
      <td>E-mail:</td><td><input type="email" name="email" value="<?php echo $email; ?>"/></td>
    </tr>
    <tr>           
      <td colspan="2"><input type="submit" name="submit_email" class="btn" value="Отправить"/></td>
    </tr>
    </table> 
   </form>   
   
   <br/><br/><br/>   
   
   <h4>Код</h4>
   <form method="post" action="">
<?php
  if (strlen($error['code'])) echo '<font color="red">'.$error['code'].'</font><br/>';
  else if (strlen($success['code'])) echo '<font color="green">'.$success['code'].'</font><br/>';
?>    
    <table id="paddtbl">
    <tr>
      <td valign="top">Код:</td><td><textarea style="width:500px;height:300px;" name="code"><?php echo $code; ?></textarea></td>
    </tr>
    <tr>           
      <td colspan="2"><input type="submit" name="submit_code" class="btn" value="Отправить"/></td>
    </tr>
    </table> 
   </form>   
</div>
</body>
</html>
<?php
  die();
?>