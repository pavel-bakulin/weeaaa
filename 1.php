<?php

Error_Reporting(E_ALL & ~E_NOTICE);

function getTextFromNode($Node, $Text = "") { 
    if ($Node->tagName == null) 
        return $Text.$Node->textContent; 

    $Node = $Node->firstChild; 
    if ($Node != null) 
        $Text = getTextFromNode($Node, $Text); 

    while($Node->nextSibling != null) { 
        $Text = getTextFromNode($Node->nextSibling, $Text); 
        $Node = $Node->nextSibling; 
    } 
    return $Text; 
} 

function getTextFromDocument($DOMDoc) { 
    return getTextFromNode($DOMDoc->documentElement); 
} 


  $result = new stdClass();
  //$sites_html = file_get_contents("http://www.nn.ru/community/pokupka/glavpristroi/pristroy_obshchiy_zhenskaya_odezhda_44_razmera_29697571.html");
  $sites_html = file_get_contents("http://www.nn.ru/community/pokupka/glavpristroi/dve_malenkie_sumochki_dila_chernaya_i_belaya.html");
 // $sites_html = mb_convert_encoding($sites_html, 'HTML-ENTITIES', 'utf-8');    
  $html = new DOMDocument();
  $html->loadHTML($sites_html);
  $xpath = new DOMXPath($html);

  $res = $xpath->query('//div[@class="topic-head"][1]');
  if ($res->length > 0) {
    $result->title = $res->item(0)->nodeValue;
  }

  $res = $xpath->query('//div[@class="branch-body"][1]');
  if ($res->length > 0) {
    $result->description = $res->item(0)->nodeValue;
  }
  
  $res = $xpath->query('//div[@class="attached-img"]'); 
  if ($res->length > 0) {
    $result->img = DOMinnerHTML($res);
  }
   /* if ($meta->getAttribute('property')=='og:image'){
      $imgSrc = $meta->getAttribute('content');
      if (checkURL($imgSrc)) {
        $hash = md5(uniqid(rand(), true));
        $img = downloadImage($imgSrc,$hash,'');
        downloadImage($imgSrc,$hash,'big');
        $result->imageid = $img;
        $result->image = getImgLink($img); 
      }
    }*/
    
  var_dump($result->img);
  //$result->title = $res[0]->textContent;
  //echo $result->title;

/*
foreach ($xpath->query('//div[@class="branch-body"][not(self::*[@class="extra-cont"])]') as $textNode) {
  echo $textNode->textContent."<br>";
}
*/

//echo getTextFromDocument($html)."\n";branch-item

echo "ddddddd";
exit;


?>