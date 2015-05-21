<?php
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../login.php";  
  header('Content-type: text/html; charset=UTF-8');     

  $value = (float)$_REQUEST['value'];
  $manufs = $_REQUEST['manufs'];
  if (!count($manufs)) {
    echo "Выберите производителя";
    die();
  }
  $manufs = implode($_REQUEST['manufs'],',');  
  
  if ($value>0) {
    $formula = "round(price1*(100+($value))/100)";
  } else {
    $value = abs($value);
    $formula = "round(price1/(1+$value/100))";
  }
  
  $sql = "UPDATE goods SET price1 = $formula WHERE param7 IN ($manufs)";          
  $db->execute($sql, false);
  $rows = mysql_affected_rows();
  echo "Изменения внесены. Затронуто $rows строк.";        
?>