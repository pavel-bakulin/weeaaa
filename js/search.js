window.search = new (function(){
  this.radius = 3000;
  this.tag = '';
  this.period = '';   
  this.apioff = '';
  this.data = [];
  this.my = false;
  this.text = '';
  
  this.getList = function () {
    if (lib.objectFormVisible) return false;
    var params = 'radius='+this.radius+'&text='+this.text+'&lat='+gmap.coords_start.lat+'&lng='+gmap.coords_start.lng+'&zoom='+gmap.objectMapView.map.getZoom();
    if (this.my) params = params + '&my=1';
    if (this.period.length) params = params + '&period='+this.period;
    if (this.apioff.length) params = params + '&apioff='+this.apioff;
    if (this.tag.length) params = params + '&tag='+this.tag;
    $("#slider_radius [data-slider]").simpleSlider("setValue", this.radius);
    $('#refreshMap i').addClass('glyphicon-refresh-animate');
    $('#itemsList').html('<div class="preloader"></div>');
    this.data = [];
    gmap.mapClear();
    $.ajax({
       type: "GET",
       url: "/admingo/handlers/search.php",
       data: params,
       dataType: 'JSON',
       async: true,
       success: function(response) {       
          if (response.type=='cluster') {
            $('.listWrap').hide();
            $('.mapWrap').removeClass('col-sm-6').addClass('col-sm-12');
            $('#itemsList').html('');    
            gmap.resize();
            for (var item in response.data) {
              if (response.data[item].count>3) 
                gmap.createMarkerSCluster(response.data[item].lat, response.data[item].lng, response.data[item].count);
            }                      
          } else {
            if (!$('.listWrap:visible').length) {
              $('.listWrap').show();              
              $('.mapWrap').removeClass('col-sm-12').addClass('col-sm-6');
              gmap.resize();            
            }
            yoEjs.render('#itemsList', '/ejs/multy.ejs', response);
            setTimeout(function() {
              var active = '';
              var hash = location.hash.substr(1);
              var regexp = /object([0-9]+)/i;
              if (regexp.test(hash)) {
                matches = hash.match(regexp);
                var oid = parseInt(matches[1]);
              }                
            
              for (var item in response.data) {                
                search.data[response.data[item].socid] = response.data[item];
                if (oid) if (search.data[response.data[item].socid].oid==oid) {active = 'active';} else {active = '';}
                gmap.createMarker(response.data[item].lat, response.data[item].long, '<div class="marker '+active+'" iid="'+response.data[item].socid+'" id="marker'+response.data[item].socid+'"><img src="'+response.data[item].src_small+'"/></div>','',response.data[item].socid);
              }
            },1000);                                   
          }
          $('#itemsList .preloader').remove();
          $('#refreshMap i').removeClass('glyphicon-refresh-animate');
          gmap.clickAction = true;          
       }
    });    
  }  
  
})(jQuery);