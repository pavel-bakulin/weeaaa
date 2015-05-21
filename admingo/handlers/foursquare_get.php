<?php
//Client id KLVAW1TFWOV3W3GZXTBZER055105OZQPOVABBHSO4ET15DP0
//Client secret YLGP3CCWPH43JPWX5DAILYZYSMWYF5GTZZCZ4K1NSRV1JGSN
//https://api.foursquare.com/v2/venues/search?client_id=KLVAW1TFWOV3W3GZXTBZER055105OZQPOVABBHSO4ET15DP0&client_secret=YLGP3CCWPH43JPWX5DAILYZYSMWYF5GTZZCZ4K1NSRV1JGSN&v=20130815&ll=56.298836,43.987170&query=coffe
//https://api.foursquare.com/v2/venues/explore?client_id=KLVAW1TFWOV3W3GZXTBZER055105OZQPOVABBHSO4ET15DP0&client_secret=YLGP3CCWPH43JPWX5DAILYZYSMWYF5GTZZCZ4K1NSRV1JGSN&ll=44.3,37.2&near=Chicago, IL

function foursquareget($query, $lat, $lon, $radius, $limit) {
  global $socids;
  $s = 'https://api.foursquare.com/v2/venues/search?client_id=KLVAW1TFWOV3W3GZXTBZER055105OZQPOVABBHSO4ET15DP0&client_secret=YLGP3CCWPH43JPWX5DAILYZYSMWYF5GTZZCZ4K1NSRV1JGSN&v=20130815&intent=browse&ll='.$lat.','.$lon.'&radius='.$radius;
  if(strcmp(trim($query),'') !== 0){
    $s.='&query='.$query;
  }
  if((int)$limit > 0){
    $s.='&limit='.$limit;
  }  
  echo $s."<br>";
  $obj = file_get_contents($s);
  $obj = json_decode($obj);
  
  //var_dump($obj);
  
  $obj1 = $obj->response->venues;

  $array_data = array();
  $i = 0;
  foreach($obj1 as $key=>$res){
    $fullname = ''; $profile_picture = ''; $bdate = ''; $socid = ''; $owner_id = ''; $img = ''; $width = '';
    $height = ''; $src_small = ''; $src_big = ''; $text = ''; $created = ''; $url = ''; $address = '';
    $contact_phone = '';
    foreach($res as $key1=>$res1){ 
      var_dump($res1);
      echo "<br>";      
      echo $key1."<br>";
      if($key1=='id'){
        $socid =  $res->id;
      }    
      if($key1=='name'){
        $fullname =  $res->name;
      }        
      if($key1=='contact'){
        $contact_phone =  $res1->formattedPhone;        
      }        
      if($key1=='location'){
        $lat = $res1->lat;
        $lon = $res1->lng;
        $address = $res1->address;
      }
      if($key1=='categories'){
        $text = $res1->name;
      }      
      if($key1=='url'){
        $url = $res->url;
      }
      if($key1=='specials'){
        if(count($res1) > 0 && count($res1->items) > 0 ){
          $created = date("d.m.y H:i",(int)$res1->items[0]->photo->createdAt);
          $img = $res1->items[0]->photo->prefix . '130x130' . $res1->items[0]->photo->suffix;
          $img = $res1->items[0]->photo->prefix . '70x70' . $res1->items[0]->photo->suffix;
          $img = $res1->items[0]->photo->prefix . '550x550' . $res1->items[0]->photo->suffix;
        }  
      }      
          
    }  
    $array_data[$i] = array(  'fullname' => $fullname,  
                              'socid' => $socid,
                              'lat' => $lat,
                              'long' => $lon,
                              'text' => $text,
                              'contact_phone' => $contact_phone,                              
                              'url' => $url,
                              'address' => $address,
                              'src' => $img, 
                              'src_big' => $src_big,
                              'src_small' => $src_small,
                              'created' => $created
                              );
    $i++;                              
  }
  
  $res = array('type'=>'foursquare','data'=>$array_data);
  return $res;
}


?>

