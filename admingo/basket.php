<?php 
  require_once "config.php";
  require_once "db_connect.php";
	require_once "lib.php";  
  
  $cid = (int)$_POST['id'];
  $ccount = (int)$_POST['count'];
  $cprice = (int)$_POST['price'];
       
  if (isset($_COOKIE["GOODS"])) {
    foreach ($_COOKIE['GOODS'] as $id => $value) {
      if ($id == $cid) {
        $ccount += $value;
        break;
      }
    }
  }   
  
  $summa = 0;
  $count = 0;  
  $cookie_goods = array();
  $cookie_goods = $_COOKIE['GOODS'];
  
  
  $cookie_goods[$cid] = $ccount;
        		  
  foreach ($cookie_goods as $id => $value)  {	       
    $count += $value;	   
		$sql = "SELECT price1 FROM goods WHERE documentid = $id";
		$result = $db->execute($sql);
		if ($myrow = mysql_fetch_object($result)) {  		
      $summa += $myrow->price1 * $value;  			
		}
  }
	
	setcookie("GOODS[$cid]", $ccount, 0, '/');
	header('Content-type: text/html; charset=utf-8');
	
  
	$ending = '';
	if ($count==1) {$ending='';}
	else if ($count>=2 && $count<=4) {$ending='а';}
	else {$ending='ов';}
	
	$sign = ' руб';
  if (isset($_COOKIE['currency'])) {
    $currency = $_COOKIE['currency'];
    
    $course = new Valute();  
    switch ($currency) {
        case 'dollar':
            $value = $course->dollar;
            $sign = '$';
            break;
        case 'euro':
            $value = $course->euro;
            $sign = '€';
            break;
        case 'rub':
            $value = $course->rub;
            break;
        default:
            $value = $course->rub;
            break;
    }
    $summa = round($summa / $value,2);
  }
  		 	
	if ($count>0) {
		echo '<p>'.$count.' товар'.$ending.'</p>';
		if ($sign == ' руб') echo '<p>на сумму '.$summa.$sign.'</p>';
    else echo '<p>на сумму '.$sign.$summa.'</p>';
  }
?>