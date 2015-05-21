<?php
  error_reporting(E_ERROR);
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
		$sql = "DELETE FROM user WHERE documentid = $docid";
		$db->execute($sql, false);
		$sql = "DELETE FROM alldocs WHERE documentid = $docid";
		$db->execute($sql, false);
		header("Location: ok.html");
	}		
	if ($action=='edit') 
	{
		$sql = "SELECT * FROM user WHERE documentid = $docid";
		$result = $db->execute($sql);
		while ($myrow = mysql_fetch_object($result)) {  
    	$email = $myrow->email;
    	$password = $myrow->password;
    	$title = $myrow->title;    			
      $status = $myrow->status;
      $soctype = '';
      if (strlen($myrow->vkid)) $soctype = 'vk';
      else if (strlen($myrow->fbid)) $soctype = 'fb';
      else if (strlen($myrow->googleid)) $soctype = 'google';
      else if (strlen($myrow->okid)) $soctype = 'okid';
		}
		mysql_free_result($result);
	}
	else
	if (isset($_REQUEST['submit']))
	{
  	$email = strtolower(clearField($_REQUEST['email']));
  	$password = clearField($_REQUEST['password']);
  	$title = clearField($_REQUEST['title']);  	
  	$phone = clearField($_REQUEST['phone']);  	      	
  	$status = clearField($_REQUEST['status']);
		$active = (int)$_REQUEST['active'];		
		$approval = (int)$_REQUEST['approval'];
		
		/*проверка на ошибка*/
		$error="";		
				
		if (strlen($error)) {
			if ($action=='update') $action='edit';
		}
		else {	      
			if ($action=='update') {	
				
        $sql = "UPDATE user SET title='$title', email='$email', password='$password', `phone`='$phone', `status`='$status', active=$active, approval=$approval WHERE documentid = $docid";        			
				$db->execute($sql, false);
				
				$sql = "UPDATE alldocs SET title='$title' WHERE documentid = $docid";
				$db->execute($sql, false);
				
				header("Location: $_SERVER[PHP_SELF]?docid=$docid&sid=$sid&action=edit&update=true");
			}
			else
			{	
				getID();
				getSTID();
				
				$sql = "INSERT INTO user SET documentid=$lastid, title='$title', email='$email', password='$password', `phone`='$phone', `status`='$status', active=$active, approval=$approval";
				$db->execute($sql, false);
				
				$sql = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ($sid, $lastid, $lastid, '$title', 'user', $stid)";
				$db->execute($sql, false);
		
				header("Location: ok.html");
			}
		}
	}
?>
<html>
<head>
	<title>Создание/редактирование пользователя</title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
  <link rel="stylesheet" href="/admingo/bootstrap/css/bootstrap.css"  type="text/css" media="screen"/>
  <script src="/admingo/bootstrap/js/jquery-1.8.2.min.js"></script>
  <script src="/admingo/bootstrap/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<body>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
    <h1>Пользователь</h1>        
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
			<td bgColor="#f4f4f4">Имя</td>
			<td></td>
			<td><input type="text" name="title" class="inp" value="<?php echo $title; ?>"/></td>
		</tr>    	
		<tr>
			<td bgColor="#f4f4f4">Вошел через</td>
			<td></td>
			<td><?php echo $soctype; ?></td>
		</tr>		
		<tr>
			<td bgColor="#f4f4f4">E-mail:</td>
			<td></td>
			<td><input type="text" name="email" class="inp" value="<?php echo $email; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Пароль:</td>
			<td></td>
			<td><input type="text" name="password" class="inp" value="<?php echo $password; ?>"/></td>
		</tr>
		<tr>
			<td bgColor="#f4f4f4">Статус:</td>
			<td></td>
			<td>
        <select name="status" class="inp2">
          <option value="0"<?php if ($status=='0') echo ' selected';?>>Пользователь</option>
          <option value="1"<?php if ($status=='1') echo ' selected';?>>Модератор</option>
        </select>
      </td>
		</tr>		
		</table>		
  </div>
</form>
</body>
</html>