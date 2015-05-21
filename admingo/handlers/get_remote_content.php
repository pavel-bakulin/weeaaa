<?php
  error_reporting(E_ERROR);
  require_once "../config.php";
  require_once "../db_connect.php";
  require_once "../lib.php";
  header('Content-type: text/html; charset=UTF-8');
  
  $userid = 0;
	if (isset($_COOKIE["stateid"])) {
		$stateid = clearField($_COOKIE["stateid"]);
		$sql = "SELECT documentid FROM user WHERE stateid = '$stateid'";
		$result = $db->execute($sql);		
		if ($myrow = mysql_fetch_object($result)) {			
			$userid = $myrow->documentid;
 		}
	}
	if (!$userid) die();
  
  $url = $_REQUEST['url'];	  
	$result = array();
	
	if (!checkURL($url)) {
    $result['error'] = 'Невозможно получить данные';
    echo json_encode($result);
    return;
	}
	
  $sites_html = file_get_contents($url);
  $sites_html_utf = mb_convert_encoding($sites_html, 'HTML-ENTITIES', 'utf-8');    
  $html = new DOMDocument();
  @$html->loadHTML($sites_html_utf);
  
  $meta_og_img = null;
  
  $result = new stdClass();
  $result->imageid = '';
  $result->image = '';
  $result->title = '';
  $result->description = '';

  foreach($html->getElementsByTagName('meta') as $meta) {
    if ($meta->getAttribute('property')=='og:image'){
      $imgSrc = $meta->getAttribute('content');
      if (checkURL($imgSrc)) {
        $hash = md5(uniqid(rand(), true));
        $img = downloadImage($imgSrc,$hash,'');
        downloadImage($imgSrc,$hash,'big');
        $result->imageid = $img;
        $result->image = getImgLink($img); 
      }
    } else if($meta->getAttribute('property')=='og:title'){    
      $result->title = str_replace('«','"',str_replace('»','"',$meta->getAttribute('content')));
      //var_dump($meta->getAttribute('content'));
    } else if($meta->getAttribute('property')=='og:description'){
      $result->description =  str_replace('«','"',str_replace('»','"',$meta->getAttribute('content')));
      //var_dump($meta->getAttribute('content'));
    }
  }
  
  // parsing nn.ru
  if(trim($result->title) == '' && trim($result->description) == ''){
    $result = new stdClass();
    
    @$html->loadHTML($sites_html);
    $xpath = new DOMXPath($html);
    
    $res = $xpath->query('//div[@class="topic-head"]/h1[1]');
    if ($res->length > 0) {
      $result->title =  preg_replace('/\s+/', ' ', trim($res->item(0)->nodeValue . "\r\n"));
    }
    
    $res = $xpath->query('//div[@class="branch-body"][1]');
    if ($res->length > 0) {
      $result->description = trim(str_replace('оригинал оригинал','',preg_replace('/\s+/', ' ', trim($res->item(0)->nodeValue))));
    }

    $res = $xpath->query('//div[@class="attached-img"]/*/*/*');
    if ($res->length > 0) {    
      $s = $res->item(0)->getAttribute('src');
      if(substr($s,0,2)=='//'){
        $imgSrc = 'http:' .$res->item(0)->getAttribute('src');      
      }      
      if (checkURL($imgSrc)) {
        $hash = md5(uniqid(rand(), true));
        $img = downloadImage($imgSrc,$hash,'');
        downloadImage($imgSrc,$hash,'big');
        $result->imageid = $img;
        $result->image = getImgLink($img); 
      }     
    }  
    
  }
  //end parsing nn.ru

  echo json_encode($result);
  return;	
?>
