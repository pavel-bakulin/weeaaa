<?php
  header('Content-type: text/html; charset=UTF-8');  
 
  $uploaddir = 'uploadimg/';
  $fn = $_FILES['uploadfile']['name'];
  if (preg_match('/(.(ade|adp|chm|cmd|com|cpl|exe|hta|ins|isp|jse|lib|mde|msk|msp|mst|pif|scr|sct|shb|sys|vb|vbe|vbs|vxd|wsc|wsf|wsh))/',substr($fn,strrpos($fn,'.')+1),$matches)) {die("файл не загружен");}
  
  $newname = md5(uniqid(rand(), true)).substr($fn, strpos($fn,'.'));  
  if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploaddir.$newname)) {die("файл не загружен");}
   
  $result = "success|$newname|$fn"; 
  echo $result;
?>