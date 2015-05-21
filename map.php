<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple markers</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }

      .labels {
        color: #ffffff;
        font-family: "Lucida Grande", "Arial", sans-serif;
        font-size: 10px;
        font-weight: bold;
        white-space: nowrap;
        text-align: center;
        width:40px;
      }
     
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
    <script src="js/jquery-2.1.0.min.js"></script>
    <script src="js/markerwithlabel.js"></script>
    <script>
    
  

function initialize() {
  var myLatlng = new google.maps.LatLng(56.326843, 43.999819);
  var mapOptions = {
    zoom: 10,
    center: myLatlng
  }
  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

 function get_markers(coord,func){
    $.ajax({
        type: 'POST',
        url: 'cluster/cluster.php',
        dataType : 'JSON',
        data: coord,
        success: function (response) {
          if(typeof response !== "undefined"){
            func(response);
          }
        }
    });
  }     

  // Add a marker to the map and push to the array.
  var markers = [];
  function addMarker(location,cluster) {
    if(cluster > 0){
      var image = 'cluster/m3.png';
      var marker = new MarkerWithLabel({
        position: location,
        map: map,
        icon: image,
        labelContent: cluster,
        labelAnchor: new google.maps.Point(20, 28),
        labelClass: "labels", // the CSS class for the label
      });
    } else {
      var marker = new google.maps.Marker({
        position: location,
        map: map        
      });
    }
    google.maps.event.addListener(marker, "click", function (e) { 
      if(map.getZoom() < 18){
        map.setZoom(map.getZoom() + 3);
      }  
      map.setCenter(marker.getPosition());
    });     
    markers.push(marker);
  }
    
  // Sets the map on all markers in the array.
  function setAllMap(map) {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(map);
    }
  }    
  
  // Removes the markers from the map, but keeps them in the array.
  function clearMarkers() {
    setAllMap(null);
  }

  // Deletes all markers in the array by removing references to them.
  function deleteMarkers() {
    clearMarkers();
    markers = [];
  }  
  
  google.maps.event.addListener(map, 'idle', function() {
    var bounds = map.getBounds();
    var center = bounds.getCenter();
    var ne = bounds.getNorthEast();
    // r = radius of the earth in statute km
    var r = 6377.83;  
    // Convert lat or lng from decimal degrees into radians (divide by 57.2958)
    var lat1 = center.lat() / 57.2958; 
    var lon1 = center.lng() / 57.2958;
    var lat2 = ne.lat() / 57.2958;
    var lon2 = ne.lng() / 57.2958;
    // distance = circle radius from center to Northeast corner of bounds
    var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1));   
    
var MAX_ZOOM = 21;
var OFFSET = 268435456;
var RADIUS = 85445659.4471;
    
var latToY = function(value) {
	return Math.round(OFFSET - RADIUS * Math.log((1 + Math.sin(value * Math.PI / 180)) / (1 - Math.sin(value * Math.PI / 180))) / 2);
};
 
var lngToX = function(value) {
    return Math.round(OFFSET + RADIUS * value * Math.PI / 180);        
};
 
var pixelDistance = function(p1, p2, zoom) {
    var x1 = lngToX(p1.lng());
    var y1 = latToY(p1.lat());
    var x2 = lngToX(p2.lng());
    var y2 = latToY(p2.lat());
    return Math.sqrt(Math.pow((x1 - x2), 2) + Math.pow((y1 - y2), 2)) >> (MAX_ZOOM - zoom);
};    

var dis1 = pixelDistance(center,ne,map.getZoom());
    
    console.log('dis:' + dis);
    
    var data = {
      "lat0":this.getBounds().getSouthWest().lat(),
      "lng0":this.getBounds().getSouthWest().lng(),
      "lat1":this.getBounds().getNorthEast().lat(),
      "lng1":this.getBounds().getNorthEast().lng(),
      "zoom":map.getZoom(),
      "radius":20
    };

    function func(obj){
      deleteMarkers();
      
      for (var key in obj) {
        var obj_1 = obj[key];
        if(typeof obj_1 === 'object'){
          var count_marker = 0;
          for (var key_1 in obj_1) {
            if(key_1=='location'){
              var myLatlng = new google.maps.LatLng(obj_1[key_1][0],obj_1[key_1][1]);
              addMarker(myLatlng,0);
            } else {
              if(key_1=='count'){
                count_marker = obj_1[key_1];
              }
              if(key_1=='coordinate'){
                var myLatlng = new google.maps.LatLng(obj_1[key_1][0],obj_1[key_1][1]);
                addMarker(myLatlng,count_marker);
                count_marker = 0;
              }
            }
          }
        }
      }
    }
    
    get_markers(data,func);
    
  });
  

}

google.maps.event.addDomListener(window, 'load', initialize);



    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>

