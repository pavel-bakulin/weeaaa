<?php
  require_once "config.php";
  require_once "db_connect.php";
  require_once "lib.php";
  
	$error = array();
	$success = array();  
  if (isset($_REQUEST['temp'])) {
    $changePass = true;
    $temp = trim(mysql_real_escape_string($_REQUEST['temp']));
    $sql = "SELECT temp FROM settings";
    $result = $db->execute($sql);
	  $myrow = mysql_fetch_object($result);
	  if ($myrow->temp != $temp) {
      $changePass = false;
      die();
    } else {
      $changePass = true;
      if (isset($_REQUEST['password'])) {
    	  $error['password'] = '';
        $password = trim($_REQUEST['password']); 
        $password2 = trim($_REQUEST['password2']);
        
        if ($password != $password2) {$error['password'] = 'Пароли не совпадают';}
        else {
          $password = md5($password);
          $sql = "UPDATE settings SET password = '$password', temp = ''";
          $db->execute($sql, false);
          $success['password'] = 'Изменения внесены';
        }      
      }
    }     
  } else {
    die();
  }
?>
<html>
<head>
	<title>CMS AdminGo</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td><img src="images/0.gif" width="10" height="10"/></td>
	<td></td>
	<td width="100%"></td>
	<td></td>
	<td><img src="images/0.gif" width="10" height="10"/></td>
</tr>
<tr>
	<td></td>
	<td><img src="images/l.gif" width="10" height="50"/></td>
	<td class="top" valign="middle">Admin<span>Go</span> — Восстановление пароля</td>
	<td><img src="images/r.gif" width="10" height="50"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
   <a href="/<?php echo $config->cms; ?>" class="links2">Вернуться к управлению сайтом</a><br/><br/>
   <h2>Смена пароля</h2>   
   <form method="post" action="">
<?php
  if (strlen($error['password'])) echo '<font color="red">'.$error['password'].'</font><br/>';
  else if (strlen($success['password'])) echo '<font color="green">'.$success['password'].'</font><br/>';
?>   
    <input type="hidden" name="temp" value="<?php echo $_REQUEST['temp']; ?>"/>
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

	</td>
	<td></td>
	<td></td>
</tr>
</table>
</body>
</html>