<?
	header('Content-type: text/html; charset=UTF-8');

	include('emoji.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Emoji HTML Test</title>
	<link href="emoji.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php 
/*
user1100655.atservers.net
Website URL 	http://user1100655.atservers.net
Redirect URI 	http://user1100655.atservers.net/no_auth
Client ID 	e6a91043126244df9fecf644efddd581
Client Secret 	06cca261fcf94df1b5d9d32e798ee88a
Website URL 	http://user1100655.atservers.net
Redirect URI 	http://user1100655.atservers.net/no_auth
https://api.instagram.com/v1/users/search?q=sex&count=100&client_id=e6a91043126244df9fecf644efddd581
https://api.instagram.com/v1/media/search?lat=56.16792040536160&lng=43.90857696533203&distance=1000&client_id=e6a91043126244df9fecf644efddd581&q=sex&count=10
*/

$lat = '56.161751453';
$lon = '43.920938884';
$radius = 2000;

$obj = file_get_contents('https://api.instagram.com/v1/media/search?lat=' . $lat . '&lng=' . $lon . '&distance=' . $radius . '&client_id=e6a91043126244df9fecf644efddd581' );
$obj= json_decode($obj);
$res = $obj -> data;

$array_data = array();
$i = 0;
foreach($res as $key=>$res1){  
  $fullname = '';
  $bdate = '';
  $pid = '';
  $owner_id = '';
  $img = '';
  $width = '';
  $height = '';
  $src_small = '';
  $src_big = '';
  $text = '';
  $created = '';
  foreach($res1 as $key1=>$field1){ 
    if($key1=='user'){
      $fullname = $field1->full_name;
    }
    if($key1=='caption'){
      //echo emoji_google_to_unified($field1->text) . "-------------";
      $text = htmlspecialchars($field1->text);
      //echo $text . "<br>";
      $data = $field1->text;
     # when you recieve text from a mobile device, convert it
        # to the unified format.

        //$data = emoji_docomo_to_unified($data);   # DoCoMo devices
        //$data = emoji_kddi_to_unified($data);     # KDDI & Au devices
        //$data = emoji_softbank_to_unified($data); # Softbank & pre-iOS6 Apple devices
        //$data = emoji_google_to_unified($data);   # Google Android devices

        # when sending data back to mobile devices, you can
        # convert back to their native format.

       // $data = emoji_unified_docomo($data);   # DoCoMo devices
       // $data = emoji_unified_kddi($data);     # KDDI & Au devices
       // $data = emoji_unified_softbank($data); # Softbank & pre-iOS6 Apple devices
       // $data = emoji_unified_google($data);   # Google Android devices


        # when displaying data to anyone else, you can use HTML
        # to format the emoji.

        $data = emoji_unified_to_html($data);

        # if you want to use an editor(i.e:wysiwyg) to create the content, 
        # you can use html_to_unified to store the unified value.

       // $data = emoji_html_to_unified(emoji_unified_to_html($data));   
        echo $data; 
        echo "<br>===============<br>";        
      
    }
    if($key1=='images'){
      $img =  $field1->low_resolution->url;
      $width = $field1->standard_resolution->width;
      $height = $field1->standard_resolution->height;
      $src_big = $field1->standard_resolution->url;
      $src_small = $field1->thumbnail->url;
    }
    if($key1=='created_time'){
      $created = date("d.m.y H:i",$field1);
    }
    if($key1=='location'){
      $lat = $field1->latitude;
      $lon = $field1->longitude;
    }
    if($key1=='id'){
        $a = explode('_',$field1);
        $owner_id = $a[0];
        $pid =  $a[1];
    }

  }
  $array_data[$i] = array( 'fullname' => $fullname,
                                     'bdate' => $bdate,
                                     'pid' => $pid, 
                                     'owner_id' => $owner_id,
                                     'src' => $img, 
                                     'src_big' => $src_big,
                                     'src_small' => $src_small,
                                     'width' => $width,
                                     'height' => $height,
                                     'text' => $text,
                                     'created' => $created,
                                     'lat' => $lat,
                                     'lon' => $lon
                                      );
  $i++;
}

$res = array('type'=>'instagram','data'=>$array_data);
var_dump($res);
echo "<br><br>";
echo json_encode($res);
?>
  </body>
</html>