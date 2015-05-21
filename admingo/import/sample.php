<?php
header('Content-type: text/html; charset=Windows-1251');
require_once "../config.php";
require_once "../db_connect.php";
require_once "../login.php";
require_once "../lib.php";
require_once "excelparser.php";
require_once "startimp.php"; 
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=Windows-1251" />
  <link rel="stylesheet" type="text/css" href="/admingo/css/style.css">
</head>
<body>
<div style="padding:10px;">
<?php
$excel_data = array();
/* выгребаем данные из Экселя */
for( $ws_num=0; $ws_num<count($exc->worksheet['name']); $ws_num++ )
{
	$ws = $exc->worksheet['data'][$ws_num];

	if( is_array($ws) &&
	    isset($ws['max_row']) && isset($ws['max_col']) ) {

	 for( $i=0; $i<=$ws['max_row']; $i++ ) {		  
	  if(isset($ws['cell'][$i]) && is_array($ws['cell'][$i]) ) {
	   for( $j=0; $j<=$ws['max_col']; $j++ ) {

		if( ( is_array($ws['cell'][$i]) ) &&
		    ( isset($ws['cell'][$i][$j]) )
		   ){

		 // print cell data
		 $data = $ws['cell'][$i][$j];
		 $font = $ws['cell'][$i][$j]['font'];			 
		 $css = ExcelFont::ExcelToCSS($exc->fonts[$font]);
		 $style = " style ='".$css."'";      

	  switch ($data['type']) {
		// string
		case 0:
			$ind = $data['data'];
			if( $exc->sst['unicode'][$ind] ) {
			   $s = uc2html($exc->sst['data'][$ind]);
				//$s = html_entity_decode(uc2html($exc->sst['data'][$ind]),ENT_QUOTES);
			} else
				$s = $exc->sst['data'][$ind];			
      $excel_data[$i][$j] = trim($s); 
			break;
		// integer number
		case 1:
		  $excel_data[$i][$j] = (int)($data['data']);        
			break;
		// float number
		case 2:
		  $excel_data[$i][$j] = (float)($data['data']);
			break;
	   }
		   } 
	   }
	  }
	 }
	}
}

function delGoods() {
  $sql = "DELETE FROM goods";
  $result = mysql_query($sql) or die("Invalid query: " . $sql);
  $sql = "DELETE FROM alldocs WHERE doctype='goods'";
  $result = mysql_query($sql) or die("Invalid query: " . $sql);
};

function addGoods($data) {  
  $sql1 = "INSERT INTO goods (documentid, title, code, price1, quantity, param1, param2, param3) VALUES ";    
  $sql2 = "INSERT INTO alldocs (sid, documentid, position, title, doctype, stid) VALUES ";
  
  $len = count($data);
  for ($i = 1; $i < $len; $i++) {
    $lastid = getID();
    $title = mysql_real_escape_string($data[$i][3]);
    $code = trim($data[$i][1]);  
    $price1 = (float)$data[$i][5];
    $quantity = (int)$data[$i][4];
    $param1 = trim($data[$i][0]);
    $param2 = trim($data[$i][2]);
    $param3 = str_replace('.', '', str_replace(' ', '', str_replace(',', '', str_replace('-', '', $code))));    
    $rqpath = makeURL($title);
    $sql1 .= "($lastid, '$title', '$code', $price1, $quantity, '$param1', '$param2', '$param3')";
    $sql2 .= "(67, $lastid, $lastid, '$title', 'goods', 9)";
    if ($i != $len-1) {$sql1 .= ','; $sql2 .= ',';}
  }
  
  $fp = fopen('sql1.txt', 'w');
  $sql1 = iconv('Windows-1251','UTF-8',$sql1);
  fwrite($fp, $sql1);
  fclose($fp);  
  $handle = fopen("sql1.txt", "rb");
  $sql1 = stream_get_contents($handle);
  $sql1 = str_replace(chr(194), '', $sql1);
  $sql1 = str_replace(chr(160), '', $sql1);
  fclose($handle);                    
  $result = mysql_query($sql1) or die("Invalid query: " . $sql1 . ": ".mysql_error());    
  
  $fp = fopen('sql2.txt', 'w');
  $sql2 = iconv('Windows-1251','UTF-8',$sql2);
  fwrite($fp, $sql2);
  fclose($fp);  
  $handle = fopen("sql2.txt", "rb");
  $sql2 = stream_get_contents($handle);
  fclose($handle);                    
  $result = mysql_query($sql2) or die("Invalid query: " . $sql2 . ": ".mysql_error());  
}

//print_r($excel_data);die();
/*
0 Производитель	  param1
1 Артикул	        code
2 Кросс	          param2
  Кросс.сокр      param3
3 Наименование    title	
4 кол-во	        quantity
5 Цена            price1
*/

//delGoods();

addGoods($excel_data);


?>
<br/><br/>
<a href="/admingo/import/" class="links2">back to import</a><br/><br/><br/>
<a href="/admingo/" class="links2">back to admingo</a><br/>
</div>
</body>
</html>