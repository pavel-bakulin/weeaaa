window.gmap = new (function(){
  this.coords_start = {lat:56.3017817, lng:44.0465775};
  this.clickAction = true;
  this.container = '';
  this.markerMe = null;  
  this.markers = [];
  this.markerCluster = {};  
  this.objectMapView = {};
  this.objectMapView.map = null;  
  this.objectMapView.current_position = [];
  this.zoom = 12;
  this.radius = 3000;         
  this.radiuses = {12:3000, 13:2200, 14:1800, 15:1400, 16:1000, 17:600, 18:200, 19: 100};
	this.userSearchCircleOptions = {
      strokeColor: '#000000',
      strokeOpacity: 0.1,
      strokeWeight: 2,
      fillColor: '#555555',
      fillOpacity: 0.1,
      clickable: false,
      radius: this.radius
	};
	this.singleClick = false;
	
	this.getRadius = function(zoom) {
    if (zoom<12) return 5000;
    else if (zoom>19) return 200;
    else return this.radiuses[zoom];
	}
      
  this.createMarker = function(lat, lng, html, html_infowin, socid) {
    var marker = new RichMarker({
      position: new google.maps.LatLng(lat,lng),
      map: gmap.objectMapView.map,
      flat: true,
      draggable: false,
      content: html
    });    
    this.markers[socid] = marker;
    google.maps.event.addListener(marker, 'click', function(e) {
      gmap.clickAction = false;
    });
    
    return marker;
  }  
  
  this.createMarkerSCluster = function(lat, lng, count) {
    var marker = new RichMarker({
      position: new google.maps.LatLng(lat,lng),
      map: gmap.objectMapView.map,
      flat: true,
      draggable: false,
      content: '<div class="markeSCluster" lat="'+lat+'" lng="'+lng+'">'+count+'</div>'
    });    
    this.markers.push(marker);
    
    google.maps.event.addListener(marker, 'click', function(e) {
      gmap.clickAction = false;
    });
    
    return marker;
  }  
  
  this.mapClear = function () {
    for (var m in this.markers) {
      try {        
        this.markers[m].setMap(null);        
			} catch(e) {}             
    }
		try {
      this.markerCluster.clearMarkers();
    } catch(e) {}
    this.markers = [];
  }
  
  this.resize = function() {
    google.maps.event.trigger(gmap.objectMapView.map, 'resize');
  }
  
  this.initialize_view = function() {
    var mapOptions = {
        center: new google.maps.LatLng(gmap.coords_start.lat,gmap.coords_start.lng),
        zoom: this.zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    this.objectMapView.map = new google.maps.Map(document.getElementById('map'),mapOptions);    
    for (var m in this.objectMapView.markers) {
      try {
        this.objectMapView.markers[m].setMap(null);
			} catch(e) {}
    }
    
    this.createMarkerMe();
    search.radius = this.getRadius(this.objectMapView.map.getZoom());
    $("#slider_radius [data-slider]").simpleSlider("setValue", search.radius); 
    search.getList();
    //this.createCircle();
              
    var input_ac = document.getElementById('autocompleteField');    
    if (input_ac) {
      var options = {};
      var autocomplete = new google.maps.places.Autocomplete(input_ac,options);
      autocomplete.bindTo('bounds', this.objectMapView.map);
          
      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        if (place.geometry.viewport) {
            gmap.objectMapView.map.fitBounds(place.geometry.viewport);
        } else {
            gmap.objectMapView.map.setCenter(place.geometry.location);
        }
        var marker_position = place.geometry.location;
        
        if ($('.markerMe').hasClass('arr')) {
          $('#objectForm [name=lat]').val(marker_position.lat());
          $('#objectForm [name=lng]').val(marker_position.lng());
          gmap.coords_start = {"lat":marker_position.lat(), "lng":marker_position.lng()};
          gmap.createMarkerMe();          
          $('.markerMe').addClass('arr');
          return;
        }
              
        gmap.coords_start = {"lat":marker_position.lat(), "lng":marker_position.lng()};
        lib.SetCookie('lat',marker_position.lat());
        lib.SetCookie('lng',marker_position.lng());
    		  
    		gmap.createMarkerMe();    		  
        gmap.reSearch();        
      });
    }   
    
    google.maps.event.addListener(gmap.objectMapView.map,'click',function(event) {
      if (!gmap.clickAction) return; 
      gmap.singleClick = true;
      setTimeout(function() {gmap.runIfNotDblClick(event);}, 250);   
    });    
    
    google.maps.event.addListener(gmap.objectMapView.map,'dblclick',function(event) {
      gmap.clearSingleClick();
      if ($('.objectFormWrap:visible').length) return;
      if (lib.userid) {
        var marker_position = event.latLng;
        gmap.coords_start = {"lat":marker_position.lat(), "lng":marker_position.lng()};
        $('.itemsList').hide();        
        $('.objectFormWrap').show();
        $('.markerMe').addClass('arr'); 
        lib.objectFormVisible = true;        
        lib.objectFormReset();
        $('#objectForm [name=lat]').val(marker_position.lat());
        $('#objectForm [name=lng]').val(marker_position.lng());        
        //console.log(marker_position.lat()+','+marker_position.lng());
      } else { 
        alert('Необходимо авторизоваться');
      }
    });
    
    google.maps.event.addListener(gmap.objectMapView.map,'zoom_changed',function(event) {
      if (gmap.objectMapView.map.getZoom()<=7) {
        search.getList();
      }
    }); 
  }
  
  this.runIfNotDblClick = function (event) {
    var marker_position = event.latLng;
    var marker_LatLng = new google.maps.LatLng(marker_position.lat(),marker_position.lng());
        
    gmap.objectMapView.map.panTo(marker_position);
    gmap.coords_start = {"lat":marker_position.lat(), "lng":marker_position.lng()};
    lib.SetCookie('lat',marker_position.lat());
    lib.SetCookie('lng',marker_position.lng());
		  
		gmap.createMarkerMe();    		  
    gmap.reSearch();
    event.stop();     
  }
  
  this.clearSingleClick = function () {
    this.singleClick = false;
  }
  
  this.markerDump = function () {
    if (gmap.objectMapView.map.getZoom()>14) {
      setTimeout(function(){
        var similarPlace = [];
        $('.marker').each(function(){
          similarPlace[$(this).attr('latlon')] = $('.marker[latlon="'+$(this).attr('latlon')+'"]').length;
        });
          console.log(similarPlace);
        $('.marker').parent().parent().css('transition','all 0.4s');
        var d = 60;
        var offset = [[0,0],[d/2,-d*0.9],[-d/2,d*0.9],[-d/2,-d*0.9],[d/2,d*0.9],[d,0],[-d,0],[1.5*d,-d*0.9],[-1.5*d,d*0.9],[1.5*d,d*0.9],[2*d,0],[2.5*d,-d*0.9],[-1.5*d,-d*0.9],[-2*d,0],[-2.5*d,d*0.9],[1*d,2*d*0.9],[0,2*d*0.9],[-1*d,2*d*0.9],[-2*d,2*d*0.9],[-3*d,2*d*0.9],[2.5*d,d*0.9],[3*d,0],[3.5*d,-1*d*0.9]];
        for (var sp in similarPlace) {
          if (similarPlace[sp]>1 && similarPlace[sp]<=24) {
            var i = 0;
            $('.marker[latlon="'+sp+'"]').each(function(){
              $(this).parent().parent().css('marginLeft',offset[i][0]+'px');
              $(this).parent().parent().css('marginTop',offset[i][1]+'px');
              i++;
            });
          } else if (similarPlace[sp]>24) {
            $('.marker[latlon="'+sp+'"]').each(function(){
              $(this).parent().parent().addClass('latlon'+sp.replace(/\./g,''));
            });                
            var $marker = $('.latlon'+sp.replace(/\./g,'')+':last-child').find('.markerUser');                  
          }
        }
      },500);
    } else {
      $('.marker').parent().parent().css('marginLeft','0px');
      $('.marker').parent().parent().css('marginTop','0px');
    }    
  }    
  
  this.createCircle = function() {
		if (this.userSearchCircle) this.userSearchCircle.setMap(null);
		this.userSearchCircle = new google.maps.Circle(this.userSearchCircleOptions);
		this.userSearchCircle.setCenter(new google.maps.LatLng(this.coords_start.lat, this.coords_start.lng));
		this.userSearchCircle.setMap(gmap.objectMapView.map);
  }  
  
  this.createMarkerMe = function() {
    var arrClass = $('.markerMe.arr').length?'arr':'';
    if (this.markerMe !== null) this.markerMe.setMap(null);
    //var content = (window.nav.userLogged)?'<div class="markerMe logged"><div class="img"><img src="'+userData.image+'"/></div></div>':'<div class="markerMe"></div>';
    var content = '<div class="markerMe '+arrClass+'"><div>Объект будет здесь</div></div>';
    this.markerMe = new RichMarker({
      position: new google.maps.LatLng(gmap.coords_start.lat,gmap.coords_start.lng),
      map: gmap.objectMapView.map,
      flat: true,
      draggable: true,
      content: content
    });    
    
    google.maps.event.addListener(gmap.markerMe, 'click', function(e) {
      gmap.clickAction = false;
    }); 
        
    google.maps.event.addListener(gmap.markerMe, 'dragend', function(event) {        
      var marker_position = gmap.markerMe.getPosition();
      if ($('.markerMe').hasClass('arr')) {
        $('#objectForm [name=lat]').val(marker_position.lat());
        $('#objectForm [name=lng]').val(marker_position.lng());
        gmap.markerMove($('#objectForm [name=socid]').val(),marker_position.lat(),marker_position.lng());
        return;
      }      
      
      if (parseFloat(gmap.coords_start.lat).toFixed(3)==parseFloat(marker_position.lat()).toFixed(3) && 
          parseFloat(gmap.coords_start.lng).toFixed(3)==parseFloat(marker_position.lng()).toFixed(3)) {                        
        return;
      }
      
      gmap.objectMapView.map.panTo(marker_position);
      gmap.coords_start = {"lat":marker_position.lat(), "lng":marker_position.lng()};
      lib.SetCookie('lat',marker_position.lat());//lib.GetCookie('lat');
      lib.SetCookie('lng',marker_position.lng());
  
		  marker_LatLng = new google.maps.LatLng(marker_position.lat(),marker_position.lng());
		  //gmap.userSearchCircle.setCenter(marker_LatLng);          		  		  
      gmap.reSearch();              
    });
  }  
  
  this.reSearch = function() {    
    search.radius = this.getRadius(this.objectMapView.map.getZoom());
    search.getList();
  }
  
  this.mapCenter = function(lat,lng) {
    var center = new google.maps.LatLng(lat, lng);    
    this.objectMapView.map.panTo(center);
  }  
   
  this.getParams = function() {
    return {zoom:this.objectMapView.map.getZoom(), lat:gmap.objectMapView.map.getBounds().getCenter().lat(), lng:gmap.objectMapView.map.getBounds().getCenter().lng()};
  }  
  
  this.init = function() {
    if (lib.GetCookie('lat') && lib.GetCookie('lng')) {
      this.coords_start.lat = lib.GetCookie('lat');
      this.coords_start.lng = lib.GetCookie('lng');
      this.initialize_view();
    } else {        
      this.initialize_view();
    }
    var geoloc = window.navigator.geolocation;
    if (geoloc != null) {
        geoloc.getCurrentPosition(this.successCallbackProfile, this.errorCallbackProfile);
    }     
  }
  
  this.successCallbackProfile = function(position) {
    gmap.coords_start = {'lat':position.coords.latitude, 'lng':position.coords.longitude};
    /*lib.SetCookie('lat',position.coords.latitude);
    lib.SetCookie('lng',position.coords.longitude);*/
    //gmap.initialize_view();      
  }
  
  this.successCallbackFindMe = function(position) {
    gmap.objectMapView.map.panTo(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
    gmap.coords_start = {'lat':position.coords.latitude, 'lng':position.coords.longitude};
    lib.SetCookie('lat',position.coords.latitude);
    lib.SetCookie('lng',position.coords.longitude);    
    gmap.createMarkerMe(); 
    gmap.reSearch();
  }

  this.errorCallbackProfile = function(error) {
    alert(error);                            
  }  
  
  this.markerMove = function(oid,lat,lng) {
    this.markers[oid].setPosition(new google.maps.LatLng(lat,lng));    
  }
  
  this.findme = function() {
    var geoloc = window.navigator.geolocation;
    if (geoloc != null) {
        geoloc.getCurrentPosition(this.successCallbackFindMe, this.errorCallbackProfile);
    } 
  }

})(jQuery);