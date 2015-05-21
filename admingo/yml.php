<?php
  require_once "config.php";  
  require_once "db_connect.php";
  require_once "login.php";   	
	
	$sid = 72; //Номер папки каталога
	$server_name = "http://".$_SERVER['SERVER_NAME'];
	$content = "";
	
  function category($parentid)
  {	
  	global $db, $sid, $content;
  	
  	$sql = "SELECT * FROM sections WHERE ancestor = ".$parentid." ORDER BY position DESC";
  	$result = $db->execute($sql);
  
    $parent = "";
    if ($parentid != $sid) {
      $parent = " parentId=\"".$parentid."\"";
    }
    
  	while ($myrow = mysql_fetch_object($result))
  	{  	  
      $content .= "<category id=\"".$myrow->id."\"".$parent.">".iconv("utf-8","windows-1251",htmlspecialchars($myrow->Name))."</category>\n";
      
  		$sql2 = "SELECT id FROM sections WHERE ancestor = ".$myrow->id;
  		$result2 = $db->execute($sql2);
			if (mysql_num_rows($result2)>0) {
				category($myrow->id);
			}
  	}
  }
  
  function offers() {
    global $db, $sid, $content, $server_name;
    
    $sql = "SELECT * FROM goods AS g 
            INNER JOIN alldocs ON alldocs.documentid = g.documentid 
            WHERE g.market = 1";
    $result = $db->execute($sql);
    while ($myrow = mysql_fetch_object($result)) {
      $available = (int)$myrow->quantity > 0 ? 'true' : 'false';
      $content .= "<offer id=\"".$myrow->documentid."\" available=\"$available\">\n";  	
      if (strlen($myrow->rqpath)>0) {
  		  $url = $myrow->rqpath;
  		} else {		
			  $url = "/site.php?id=".$myrow->documentid."&sid=$myrow->sid";
      }
      $url = htmlspecialchars($server_name.$url);            
      $content .= "<url>$url</url>\n";
      $content .= "<price>$myrow->price1</price>\n";
      $currencyId = 'RUR';
      switch ($myrow->param10) {
        case 'dollar':
          $currencyId = 'USD';
          break;
        case 'euro':
          $currencyId = 'EUR';
          break;        
      }      
      $content .= "<currencyId>$currencyId</currencyId>\n";           
      $content .= "<categoryId>$myrow->sid</categoryId>\n";
      if ((int)$myrow->imageid > 0) {
        $content .= "<picture>".$server_name."/site.php?id=".$myrow->imageid."</picture>\n";
      }
      $content .= "<delivery>true</delivery>";
      $content .= "<name>".iconv("utf-8","windows-1251",htmlspecialchars($myrow->title))."</name>\n";
      if (strlen($myrow->description) > 0) {  
        $content .= "<description>".iconv("utf-8","windows-1251",htmlspecialchars($myrow->description))."</description>\n";
      }      
      $content .= "</offer>\n";
    }
  }    
	
	$content .= "<?xml version=\"1.0\" encoding=\"windows-1251\"?".">\n";
	$content .= "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n";
	$content .= "<yml_catalog date=\"".Date("Y-m-d H:i")."\">\n";
	$content .= "<shop>\n";

  $content .= "<name>OFFITEX</name>\n";
  $content .= iconv("utf-8","windows-1251","<company>Офисное и банковское оборудование</company>\n");
  $content .= "<url>$server_name</url>\n";             
  
  $content .= "<currencies>\n";
  $content .= "<currency id=\"RUR\" rate=\"1\"/>\n";
  $content .= "<currency id=\"USD\" rate=\"CBRF\"/>\n";
  $content .= "<currency id=\"EUR\" rate=\"CBRF\"/>\n";
  $content .= "<currency id=\"UAH\" rate=\"CBRF\"/>\n";
  $content .= "</currencies>\n";

  $content .= "<categories>\n";
  category($sid);
  $content .= "</categories>\n";

  $content .= "<offers>\n";
  offers();
  $content .= "</offers>\n";

	$content .= "</shop>\n";
	$content .= "</yml_catalog>\n";
	
	//echo $content;
  header("Content-Type: text/xml; charset=windows-1251");  
  $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/yml_catalog.xml', 'w');
  fwrite($fp, $content);
  fclose($fp); 
  
  header("Content-Type: text/html; charset=utf-8");
  echo "<h1>Экспортный файл создан успешно!</h1>
  Ссылка: <a href='/yml_catalog.xml'>http://$_SERVER[HTTP_HOST]/yml_catalog.xml</a>";
?>

