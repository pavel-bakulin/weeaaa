<?php
	$sort = $_REQUEST['sort'];
	$objtype = $_REQUEST['objtype'];
	$id = (int)$_REQUEST['id'];
  
  function sortEchoResult($str) {
    echo "<p class='sortp'>".$str."</p>";
    echo "<script>window.opener.location.reload();</script>";
  }
   
if (isset($sort))
{

  if ($objtype=="section")
  {
    $tablename = 'sections';
    $filedname1 = 'id';
    $filedname2 = 'ancestor';
  }
  else
  {
    $tablename = 'alldocs';
    $filedname1 = 'documentid';
    $filedname2 = 'sid';
  }
    
  require_once "config.php";  
	require_once "db_connect.php";
	require_once "login.php";

	$sql = "SELECT `$filedname2` FROM `$tablename` WHERE `$filedname1`=$id";
	$result = $db->execute($sql);

	$ancestor=-1;
	while ($myrow = mysql_fetch_row($result))
	{
		$ancestor = $myrow[0];
	}	
	//$sql = "SELECT id, position FROM sections WHERE ancestor=$ancestor ORDER BY position desc";
	//$sql = "SELECT documentid, position FROM alldocs WHERE sid=$ancestor ORDER BY position desc";
	$sql = "SELECT $filedname1, position FROM `$tablename` WHERE `$filedname2`=$ancestor ORDER BY position desc";
	
	$result = $db->execute($sql);
	$positions = array();
	while ($myrow = mysql_fetch_row($result))
	{
		$positions[$myrow[0]] = $myrow[1];
	}
	$change_position = $positions[$id];

	switch ($sort) {
		case 'upall':
		  if (current($positions)!=$change_position)
		  {
		    $top_pos = current($positions);
        $new_pos = $top_pos + 1;
  			$sql = "UPDATE `$tablename` SET position=$new_pos WHERE $filedname1 = $id";
  			$db->execute($sql, false);
  			sortEchoResult("Вверху");
		  }
			else
			{
			   echo "<p class='sortp'>Выше некуда</p>";
			}
			break;
		case 'up1':			
		  if (current($positions)!=$change_position)
		  {
  			foreach ($positions as $key=>$value) {
			    if ($value == $change_position) {
			    	$new_sid = $key_prev;
			    	$new_pos = $value_prev;  			    	
			    	break;
			    }
			    $key_prev = $key;
			    $value_prev = $value;
  			}
  			$sql = "UPDATE `$tablename` SET position = $new_pos WHERE $filedname1 = $id";
  			$db->execute($sql, false);
  			$sql = "UPDATE `$tablename` SET position=$change_position WHERE $filedname1 = $new_sid";
  			$db->execute($sql, false);  			  			
  			 sortEchoResult("Вверх на 1 - ok");
			}
			else
			{
			   echo "<p class='sortp'>Выше некуда</p>";
			}
		  	break;
		case 'down1':
		  if (end($positions)!=$change_position) {		  
        $keys = array_keys($positions);
        for ($i=sizeof($keys)-1; $i>=0; $i--) {
			    if ($positions[$keys[$i]] == $change_position) {
			    	$new_sid = $key_prev;
			    	$new_pos = $value_prev;  			    	
			    	break;
			    }
			    $key_prev = $keys[$i];
			    $value_prev = $positions[$keys[$i]];         
        }
  			$sql = "UPDATE `$tablename` SET position=$new_pos WHERE $filedname1 = $id";
  			$db->execute($sql, false);
  			$sql = "UPDATE `$tablename` SET position=$change_position WHERE $filedname1 = $new_sid";
  			$db->execute($sql, false);
  			  sortEchoResult("Вниз на 1 - ok");  			
			}
			else
			{
			   echo "<p class='sortp'>Ниже некуда</p>";
			}
			break;
		case 'downall':
		  if (end($positions)!=$change_position)
		  {
		    $down_pos = end($positions);
        $new_pos = $down_pos - 1;
  			$sql = "UPDATE `$tablename` SET position=$new_pos WHERE $filedname1 = $id";
  			$db->execute($sql, false);
  			sortEchoResult("Внизу");
		  }
			else
			{
			   echo "<p class='sortp'>Ниже некуда</p>";
			}
		break;
	}
}
?>
<html>
<head>
	<title>Сортировать</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/JavaScript">
	</script>
</head>
<body style="background-color:#f4f4f4;">
<div class="sortdiv">
	<div>
		<a href="<?php echo $PHP_SELF.'?sort=upall&id='.$id.'&objtype='.$objtype; ?>"><img src="images/up_all.gif" width="24" height="24" hspace="8" align="left"/>В самый верх</a><br/><br/>
		<a href="<?php echo $PHP_SELF.'?sort=up1&id='.$id.'&objtype='.$objtype; ?>"><img src="images/up_1.gif" width="24" height="24" hspace="8" align="left"/>Вверх на 1 </a><br/><br/>
		<a href="<?php echo $PHP_SELF.'?sort=down1&id='.$id.'&objtype='.$objtype; ?>"><img src="images/down_1.gif" width="24" height="24" hspace="8" align="left"/>Вниз на 1</a><br/><br/>
		<a href="<?php echo $PHP_SELF.'?sort=downall&id='.$id.'&objtype='.$objtype; ?>"><img src="images/down_all.gif" width="24" height="24" hspace="8" align="left"/>В самый низ</a><br/><br/>
	</div>
</div>
</body>
</html>