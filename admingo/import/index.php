<?php
  require_once "../config.php";
  require_once "../db_connect.php";
	require_once "../login.php";	
?>
<html>
<head>
	<title>CMS AdminGo</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<script language="JavaScript" type="text/JavaScript" src="../scripts/main.js"></script>
</head>
<body onClick="ActionsWithMenu();" id="B">
<table width="100%" height="100%">
<tr>
	<td><img src="../images/0.gif" width="10" height="10"/></td>
	<td></td>
	<td width="100%"></td>
	<td></td>
	<td><img src="../images/0.gif" width="10" height="10"/></td>
</tr>
<tr>
	<td></td>
	<td><img src="../images/l.gif" width="10" height="50"/></td>
	<td class="top" valign="middle">Admin<span>Go</span> — Синхронизация каталога товаров</td>
	<td><img src="../images/r.gif" width="10" height="50"/></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td height="100%" valign="top">
	 <br/><br/><br/>
   <form name="exc_upload" method="post" action="sample.php" enctype="multipart/form-data">
     <input type="hidden" name="style" value="segment"/>
     Загрузить файл формата Excel: <input type="file" class="inp2" name="excel_file"/><br/><br/>
     <input type="submit" name="submit" class="btn" value="Отправить"/> 
   </form>
   <br/><br/><br/>
   <a href="/<?php echo $config->cms; ?>" class="links2">Вернуться к управлению сайтом</a>
	</td>
	<td></td>
	<td></td>
</tr>
</table>
</body>
</html>