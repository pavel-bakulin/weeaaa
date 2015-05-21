$(function() {
  lib.button_share42_show();
   
  $('#textsearch').on('keyup', function(event) {
     search.text = jQuery.trim($(this).val());
     if (search.text.length) {
        $('.mapSearch .x').show();
     } else {
        $('.mapSearch .x').hide();
     }
  });  
  $('.mapSearch .x').on('click', function(event) {    
    $('#textsearch').val('');
    search.text = '';
    $(this).hide();
    search.getList();
  });
  
  $(document.body).keyup(function(e) {
    if ($('#modalInfo:visible').length) {
      if (e.keyCode == 37) {
        $('.arr_left').click();
      } else if (e.keyCode == 39) {
        $('.arr_right').click();
      }
    }
  });  

  if ($('#itemsList').length) {
    var h = $('body').height()-85;
    $('#itemsList').height(h);
    $('#map').height(h);    
  }
  
  $(".switcher").bootstrapSwitch();
  
  $('.hint .x').on('click', function(event) {
     $('.hint').hide();
     lib.SetCookie('hint','hide');
  });  

  /* setting */
  $('#period_from,#period_to').datetimepicker({locale: 'ru',format:'DD-MM-YYYY',pickTime:false});
  
  $('#settings .settings-icon').on('click', function(event) {
      if ($(this).parent().hasClass('active')) {
        $(this).parent().removeClass('active');
      } else {
        $(this).parent().addClass('active');
      }
  });
  
  $(document.body).on('click', '#showsettings', function(event) {
    $('#settings').addClass('active');
  });        
  
  $('#settings select[name=period]').on('change', function(event) {
    if ($(this).val().length) {
      search.period = $(this).val();
      $('#settings .period').hide();
    } else {
      $('#settings .period').show();
    }    
  });
  
  $('#tagMenu a').on('click', function(event) {      
    search.tag = $(this).attr('data-value');    
    if (search.tag.length) {
      $('#settings [name="vk"]').bootstrapSwitch('state', false, false);
      $('#settings [name="inst"]').bootstrapSwitch('state', false, false);
    } else {
      $('#settings [name="vk"]').bootstrapSwitch('state', true, false);
      $('#settings [name="inst"]').bootstrapSwitch('state', true, false);    
    }
    $(this).closest('.btn-group').find('button').removeClass('btn-default').removeClass('btn-success').removeClass('btn-danger').removeClass('btn-info').addClass($(this).attr('data-class')).html($(this).text()+' <span class="caret"></span>');
    $(this).closest('.btn-group').removeClass('open');
    search.getList();    
    return false;
  });
  $(document.body).on('click', '#showtagmenu', function(event) {
    $('#header .btn-group').addClass('open');
    return false;
  });    
  
  $('#period_submit').on('click', function(event) {
     search.period = $('#period_from').val()+','+$('#period_to').val();
  });
  
  $('#settings .switcher').on('switchChange.bootstrapSwitch', function(event) {    
    var apioff = '';
    if (!$('#settings [name="vk"]').is(':checked')) apioff += 'vk';
    if (!$('#settings [name="inst"]').is(':checked')) apioff += 'inst';
    search.apioff = apioff;
  });  
  
  $('#refreshMap2').on('click', function(event) {
     search.radius = parseInt($("#current_radius").html());     
     search.getList();    
     $('#settings').removeClass('active');
  });  
  $('#preset1').on('click', function(event) {
     $("#slider_radius [data-slider]").simpleSlider("setValue", 100); 
     search.radius = 100;
     search.period = '';
     $('#settings [name="vk"]').bootstrapSwitch('state', false, false);
          
     gmap.objectMapView.map.setZoom(18);
     gmap.objectMapView.map.panTo(new google.maps.LatLng(gmap.coords_start.lat,gmap.coords_start.lng));
		 gmap.createMarkerMe();
     search.getList();
  });  
  $('#preset2').on('click', function(event) {
     $("#slider_radius [data-slider]").simpleSlider("setValue", 5000); 
     search.radius = 5000;
     search.period = '24';
     $('#settings [name="vk"]').bootstrapSwitch('state', true, true);
     gmap.objectMapView.map.setZoom(12);
     gmap.createMarkerMe();
     search.getList();
  });  

  $('#showmy').on('click', function(event) {
     if (search.my) {
       search.my = false;
       search.getList();
       $(this).removeClass('btn-danger').addClass('btn-default').find('span').text('Мои объекты');
       $(this).find('i').removeClass('glyphicon-globe').addClass('glyphicon-flag');
       gmap.createMarkerMe();
       $('#preset1,#preset2,.settings-icon').removeClass('disabled');     
     } else {      
       search.my = true;
       search.getList();
       $(this).removeClass('btn-default').addClass('btn-danger').find('span').text('Показать все');
       $(this).find('i').removeClass('glyphicon-flag').addClass('glyphicon-globe');
       gmap.markerMe.setMap(null);
       $('#preset1,#preset2,.settings-icon').addClass('disabled');
     }
  });
  
  $('#findme').on('click', function(event) {
    gmap.findme();
  });
  
  /*$('#autocompleteField').on('focus', function(event) {
    $('.presets,.settings').hide();
    $(this).css('width','500');    
  }); 
  $('#autocompleteField').on('blur', function(event) {    
    $(this).css('width','175');
    $('.presets,.settings').show();
  });*/  
  /* //setting */
  
  /* auth */
  $('#vkAuth').on('click', function(event) {
      window.open($(this).attr('url'),"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=600,height=500");
  });
  
  $('#fbAuth').on('click', function(event) {      
      window.open($(this).attr('url'),"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=600,height=500");      
  });
  
  $('#gAuth').on('click', function(event) {      
      window.open($(this).attr('url'),"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=600,height=500");      
  });
  
  $('#okAuth').on('click', function(event) {      
      window.open($(this).attr('url'),"pollwindow","toolbar=no,scrollbars=yes,directories=no,status=no,menubar=no,resizable=yes,left=0,top=0,width=600,height=500");      
  });  
  
  $('#emailSignin').on('click', function(event) {
    if ($('.email-signin:visible').length) {$('#loginForm').submit();}
    else {$('.email-signin').slideDown();}    
  });
  /* //auth */  
  
  $('#refreshMap').on('click', function(event) {
     search.radius = gmap.getRadius(gmap.objectMapView.map.getZoom());
     $("#slider_radius [data-slider]").simpleSlider("setValue", search.radius);
     search.getList();    
  });  
  
  /* create object */  
  $('#objectForm').on('submit', function(event) {
    if (!$('[name=text]',this).val().length && !$('[name=image]',this).length) return false;
    $('.modal-preloader').show();            
    var params = $(this).serialize();
    $.ajax({
       type: "POST",
       url: "/admingo/handlers/object.php",
       data: params,
       dataType: 'JSON',
       async: false,
       success: function(response) {
         $('.modal-preloader').hide();
         $('.itemsList').show();        
         $('.objectFormWrap').hide();
         $('.markerMe').removeClass('arr');
         $('#objectForm').trigger("reset");
         $('#objectForm #files').html('');
          $('#objectForm [name=oid]').val('');
          $('#objectForm [name=socid]').val('');
          $('#objectForm [name=action]').val('');
          $('#objectForm [name=lat]').val('');
          $('#objectForm [name=lng]').val('');         
         lib.objectFormVisible = false;
         gmap.reSearch();
       }
    });          
    return false;        
  });  
  
  $('#objectForm [name="link"]').on('change', function(event) {
    get_remote_content($(this));
  });  
  
  $('#objectForm [name="link"]').on('paste', function(event) {
    setTimeout(function() {
      get_remote_content($('#objectForm [name="link"]'));
    }, 100);
  });
  
  $('#objectForm [name="link"]').keyup(function(e) {
    if (e.ctrlKey && e.keyCode == 86) get_remote_content($(this));
  });
  
  $(document.body).on('click', '#objectForm #files .x', function(event) {
    $('#files').html('');
  });    

  function get_remote_content(input) {  
    if (!lib.userid) alert('Необхоимо авторизоваться');
    if ($('.modal-preloader').length && $(input).val().length && $(input).val()!=lib.flag2) {
      lib.flag2 = $(input).val();
      $('.modal-preloader').show();      
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/get_remote_content.php",
         dataType:"json",         
         data: {'url':$(input).val()},
         async: true,
         success: function(result) {        
            setTimeout(function(){$('.modal-preloader').hide();},0);           
            if (result.error) return;
            if (result.title) {
              $('#objectForm [name=text]').val(result.title+'\n'+result.description);                
            }
            if (result.image) {
              var iid = result.imageid;
              var image = result.image;
              $('#files').html('<span class="file" iid="'+iid+'"><img src="'+image+'"/><input type="hidden" name="image" value="'+iid+'"/><span class="x"></span></span>');
            }
         }
       });      
     }     
  }     
  
  if ($('#attachImage').length) {
  	var upload = new AjaxUpload($('#attachImage'), {
  		action: '/admingo/handlers/imageupload.php',
  		name: 'uploadfile',
  		data: {action: 'object'},
  		onSubmit: function(file, ext){
  		  $('.fileinfo').hide();
  			if (!(ext && /^(jpg|jpeg|png|gif|ico|bmp)$/.test(ext))){       
  				$('.fileinfo').show().text('Недопустимый формат');
  				return false;
  			}				
  			$('.fileinfo').show().html('загрузка <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>');		
  		},
  		onComplete: function(file, response){
        var clearResponse = response.replace('<embed id="embed_npwlo" type="application/npwlo" height="0">','');
        $("#files").html('<span class="file" iid="'+clearResponse+'"><img src="'+lib.getImgLink(clearResponse,'')+'"/><input type="hidden" name="image" value="'+clearResponse+'"/><span class="x"></span></span>');
        $('.fileinfo').hide();
  		}
  	});  
  }
  
  $('.objectFormWrap .close').on('click', function(event) {
    $('.itemsList').show();
    $('.markerMe').removeClass('arr');
    $('.objectFormWrap').hide();
    lib.objectFormVisible = false;
  });    
  /* //create object */
  
  /* edit object */
  $('body').on('click', '.editObject', function(event) {
    var iid = $(this).attr('socid');
    $('.itemsList').hide();        
    $('.objectFormWrap').show();
    $('.markerMe').addClass('arr'); 
    var marker_position = event.latLng;
    lib.objectFormVisible = true;
    lib.objectFormReset();
    $('#objectForm [name=oid]').val($(this).attr('oid'));
    $('#objectForm [name=socid]').val(iid);
    $('#objectForm [name=action]').val('update');
    $('#objectForm [name=lat]').val(search.data[iid].lat);
    $('#objectForm [name=lng]').val(search.data[iid].long);
    gmap.coords_start.lat = search.data[iid].lat;
    gmap.coords_start.lng = search.data[iid].long;
    gmap.objectMapView.map.panTo(new google.maps.LatLng(gmap.coords_start.lat,gmap.coords_start.lng));
    gmap.createMarkerMe();        
    
    if (search.data[iid].src) {     
      if (search.data[iid].src.indexOf('http')==-1) {
        var i = lib.strrpos(search.data[iid].src,'/',0);
        var imgId = search.data[iid].src.substr(i+1);
      } else {
        var imgId = search.data[iid].src;
      }
      $("#files").html('<span class="file" iid="'+imgId+'"><img src="'+search.data[iid].src+'"/><input type="hidden" name="image" value="'+imgId+'"/><span class="x"></span></span>');
    }
    if (search.data[iid].text) {      
      $('#objectForm [name=text]').val(lib.htmlspecialchars_decode(search.data[iid].text.replace(/<br\/>/g,'\r\n')));
    }
    if (search.data[iid].link) {
      $('#objectForm [name=link]').val(search.data[iid].link);
    }    
    if (search.data[iid].tag) {
      $('#objectForm [name=tag][value="'+search.data[iid].tag+'"]').attr('checked','checked');
    }    
    $('#modalInfo').modal('hide');  
  });
  /* //edit object */
  
  /* remove object */
  $('body').on('click', '.removeObject', function(event) { 
    if (confirm('Уверены, что хотите удалить объект?')) {
      var oid = $(this).attr('oid');           
      var params = 'iid='+oid+'&action=delete';
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/object.php",
         data: params,
         dataType: 'JSON',
         async: true,
         success: function(response) {
           
         }
      });
      if ($('#itemsList').length) {
        var socid = $(this).attr('socid');
        $('#itemsList .item[iid='+socid+']').slideUp();
        $('#marker'+oid).hide();
        $('#modalInfo').modal('hide');
      } else {
        $('.item[iid='+oid+']').slideUp();
      }
    }          
    return false;        
  });
  /* //remove object */
  
  $('.tomap').on('click', function(event) {
    lib.SetCookie('lat',$(this).attr('lat'));
    lib.SetCookie('lng',$(this).attr('lng'));
    return true;
  });
  
  $(document.body).on('click', '.itemsList .onthemap', function(event) {
    $('.marker').removeClass('active');
    var iid = $(this).attr('iid');
    $('#marker'+iid).addClass('active');
    gmap.mapCenter(search.data[iid].lat,search.data[iid].long);
  });
  
  $(document.body).on('click', '.messList .profile-sm', function(event) {
    $('#forumForm [name=content]').val(jQuery.trim($(this).text())+', '+$('#forumForm [name=content]').val());
    $('#forumForm [name=answer_username]').val(jQuery.trim($(this).text()));
    $('#forumForm [name=answer_userid]').val($(this).attr('userid'));
    return false;
  });
  
  $('#modalInfo').on('hide.bs.modal', function (e) {
    location.hash = '';
  });
  
  lib.hashChange();
  $(window).on('hashchange', function () {
    lib.hashChange();
  });  
  
  $(document.body).on('click', '.getLink button', function(event) {    
    var socid = $(this).attr('socid');
    lib.socid = socid;
    var oid = (search.data[socid] && search.data[socid].oid)?search.data[socid].oid:0;    
    if (oid) {
      $('.getLink button').hide();
      $('.getLink input').show().val(window.location.protocol + "//" + window.location.hostname + '/object'+oid).select();      
    } else {
      if (!lib.userid) alert('Необходимо авторизоваться');
      $('i',this).attr('class','glyphicon glyphicon-refresh glyphicon-refresh-animate');          
      var params = "action=getlink&socid="+socid+"&";
      if (search.data[socid]) {
        var profile_picture = (search.data[socid].profile_picture != '')?search.data[socid].profile_picture:"/img/noava40.gif";
        params += "oid=0&text="+search.data[socid].text+"&image="+search.data[socid].src_big+"&lat="+search.data[socid].lat+"&lng="+search.data[socid].long+"&text="+search.data[socid].text+"&author_name="+search.data[socid].fullname+"&author_link="+search.data[socid].profile_url+"&author_image="+profile_picture;
      }    
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/object.php",
         data: params,
         dataType: 'JSON',
         async: false,
         success: function(response) {        
           $('.getLink button i').attr('class','glyphicon glyphicon-globe');
           $('.getLink button').hide();
           $('.getLink input').show().val(response.link).select();
           if (!(search.data[lib.socid] && search.data[lib.socid].oid)) {
             search.data[lib.socid].oid = response.oid;
           }          
         }
      });
    }      
  });  
  
  /* on the map */  
  $(document.body).on('click touchstart', '.marker,.itemsList .forum,.itemsList .img', function(event) {          
    var iid = $(this).attr('iid');
    
    if (!search.data[iid]) {
      location.href = '/#object'+iid;
      return;
    }    
    yoEjs.render('#modalInfoContent','/ejs/object.ejs', search.data[iid]);
    
    lib.button_share42_show();    
    if (search.data[iid].oid) {
      lib.currentHash = 'object'+search.data[iid].oid;
      location.hash = '#'+lib.currentHash;
    }
    
    lib.showObject(search.data[iid].oid,search.data[iid].socid,search.data[iid].lat,search.data[iid].long);    
  });
  
  $(document.body).on('click', '.arr_right', function(event) {
    var current = $(this).attr('socid');
    var nextEl = $('#itemsList .item[iid="'+current+'"]').next();
    if (!nextEl.length) nextEl = $('#itemsList .item:first');
    var iid = $(nextEl).attr('iid');
    yoEjs.render('#modalInfoContent','/ejs/object.ejs', search.data[iid]);    
    lib.button_share42_show();
    lib.showObject(search.data[iid].oid,search.data[iid].socid,search.data[iid].lat,search.data[iid].long);    
  });
  
  $(document.body).on('click', '.arr_left', function(event) {
    var current = $(this).attr('socid');
    var nextEl = $('#itemsList .item[iid="'+current+'"]').prev();
    if (!nextEl.length) nextEl = $('#itemsList .item:last');
    var iid = $(nextEl).attr('iid');
    yoEjs.render('#modalInfoContent','/ejs/object.ejs', search.data[iid]);    
    lib.button_share42_show();
    lib.showObject(search.data[iid].oid,search.data[iid].socid,search.data[iid].lat,search.data[iid].long);    
  });  
  
  $(document.body).on('click', '.markeSCluster', function(event) {        
    var lat = $(this).attr('lat');
    var lng = $(this).attr('lng');
    gmap.objectMapView.map.setZoom(13);
    gmap.coords_start = {"lat":lat, "lng":lng};    
    gmap.objectMapView.map.panTo(new google.maps.LatLng(lat,lng));
    gmap.createMarkerMe();
    gmap.reSearch();     
    return false;
  });
    
  /* //on the map */    
  
  $(document.body).on('mouseover', '.likes a', function(event) {
    if ($('#flyLikesList:visible').length) return false;
    if ($(this).attr('oid') && $(this).attr('oid')!=0) {
      lib.ll_loading = $(this).attr('oid');
      lib.ll_value = $(this).attr('value');
      var params = 'oid='+$(this).attr('oid')+'&value='+$(this).attr('value');
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/likelist.php",
         data: params,
         dataType: 'JSON',
         async: true,
         success: function(response) {
           if (lib.ll_loading) {             
             $('#flyLikesList').remove();
             if ($('.likes a[oid='+lib.ll_loading+']').closest("#modalInfoContent").length) {               
               $('#modalInfoContent .btn-toolbar.likes').append('<div id="flyLikesList"></div>');
               var top = 6;
               var left = (lib.ll_value==1)?50:0;
             } else {
               $('body').append('<div id="flyLikesList"></div>');
               var top = $('.likes a[oid='+lib.ll_loading+'][value='+lib.ll_value+']').offset().top;
               var left = $('.likes a[oid='+lib.ll_loading+'][value='+lib.ll_value+']').offset().left;             
             }
             $('#flyLikesList').css({
                'top' : top + 35,
                'left' : left
             });
             yoEjs.render('#flyLikesList','/ejs/llItem.ejs', response);                          
             lib.ll_loading = 0;
           }
         }
      });
    }    
  });
  
  $(document.body).on('mouseleave', '.likes a', function(event) {
    lib.ll_loading = 0;
    $('#flyLikesList').remove();    
  });
    
  $(document.body).on('click', '.likes a', function(event) {
    if ($(this).hasClass('link')) return true;
    if ($(this).hasClass('active')) return false;
    var socid = $(this).attr('socid');
    lib.socid = socid;
    var oid = (search.data[socid] && search.data[socid].oid)?search.data[socid].oid:$(this).attr('oid');
    var params = "action=object&socid="+socid+"&value="+$(this).attr('value')+"&";
    if (!parseInt(oid)) {
      if (search.data[socid]) {
        var profile_picture = (search.data[socid].profile_picture != '')?search.data[socid].profile_picture:"/img/noava40.gif";
        params += "oid=0&text="+search.data[socid].text+"&image="+search.data[socid].src_big+"&lat="+search.data[socid].lat+"&lng="+search.data[socid].long+"&text="+search.data[socid].text+"&author_name="+search.data[socid].fullname+"&author_link="+search.data[socid].profile_url+"&author_image="+profile_picture;
      }
    } else {
      params += "oid="+oid;
    }
    lib.flag = $(this).attr('value');
    lib.flag2 = $(this).attr('socid');    
    if ($('#flyLikesList:visible').length) {
      if ($('#flyLikesList .count').length) {
        var count = parseInt($('#flyLikesList .count').text());
        count++;
        var nra = (lib.flag==1)?'Понравилось':'Не понравилось';
        $('#flyLikesList p').html(nra+' <span class="count">'+count+'</span> '+lib.peopleCount(count));
      } else {
        var nra = (lib.flag==1)?'Понравилось 1 человеку':'Не понравилось 1 человеку';
        $('#flyLikesList p').html(nra);
      }
      $('#flyLikesList').append('<a href="/u'+lib.userid+'/" class="profile"><img src="'+lib.userimage+'"/></a>');
    }
    $.ajax({
       type: "POST",
       url: "/admingo/handlers/vote.php",
       data: params,
       dataType: 'JSON',
       async: false,
       success: function(response) {
         if (response.result) {
            if (search.data.length) search.data[lib.flag2]['myvote'] = lib.flag;
            if (lib.flag==1) {
              $('.likes[socid='+lib.flag2+'] .btn-success').addClass('active');               
              $('.likes[socid='+lib.flag2+'] .btn-danger').addClass('disabled');
              $('.likes[socid='+lib.flag2+'] .rate').text(parseInt($('.likes[socid='+lib.flag2+'] .rate:first').text())+1);
              if (search.data.length) search.data[lib.flag2]['rate'] = parseInt(search.data[lib.flag2]['rate'])+1;
            } else {
              $('.likes[socid='+lib.flag2+'] .btn-danger').addClass('active');
              $('.likes[socid='+lib.flag2+'] .btn-success').addClass('disabled');
              $('.likes[socid='+lib.flag2+'] .rate').text(parseInt($('.likes[socid='+lib.flag2+'] .rate:first').text())-1);
              if (search.data.length) search.data[lib.flag2]['rate'] = parseInt(search.data[lib.flag2]['rate'])-1;
            }
            $('.likes[socid='+lib.flag2+'],.likes[socid='+lib.flag2+'] a').attr('oid',response.oid);
            if (!(search.data[lib.socid] && search.data[lib.socid].oid)) {
              search.data[lib.socid].oid = response.oid;
            }
            if (response.forum && $('.messList:visible').length) {
             $('.messList .notext').remove();
             $('.messList').append(yoEjs.getRenderResult('/ejs/forum_mess.ejs', response.forum));
             $('.messList').scrollTo(10000);              
            }
         } else {
           alert(response.msg);
         }
         return false;
       }
    });          
    return false;
  });  
  
  $(document.body).on('keypress', '#forumForm textarea', function(e) {
    if (e.ctrlKey && e.keyCode == 10) {
      $('#forumForm').submit();
    }
  });
  
  $(document.body).on('submit', '#forumForm', function(event) {
    if (!$('[name=content]',this).val().length) return false;
    if ($('[name=content]',this).val().indexOf($('[name=answer_username]',this).val())===false) {
      $('[name=answer_username]',this).val('');
      $('[name=answer_userid]',this).val('');    
    }
                
    var params = $(this).serialize();
    var socid = $('#forumForm [name=socid]').val();
    lib.socid = socid; 
    if (!parseInt($('[name=oid]',this).val())) {
      var oid = (search.data[socid] && search.data[socid].oid)?search.data[socid].oid:0;
      params = params + '&oid='+oid;
    }
    $.ajax({
       type: "POST",
       url: "/admingo/handlers/forum.php",
       data: params,
       dataType: 'JSON',
       async: false,
       success: function(response) {
         $('.messList .notext').remove();
         $('.messList').append(yoEjs.getRenderResult('/ejs/forum_mess.ejs', response));
         $('.messList').scrollTo(10000);
         $('#forumForm [name=content]').val('');
         if (!(search.data[lib.socid] && search.data[lib.socid].oid)) {
           if (search.data[lib.socid]) search.data[lib.socid].oid = response.oid;
         }
       }
    });          
    return false;        
  });
  
  /* pm */
  $('#pmForm').on('submit', function(event) {
    if (!lib.checkForm($(this))) return false;
    if (!($('[name=content]',this).val().length || $('.attachment p',this).length)) {return false;}
    $('.zaglushka').remove();
    var params = $(this).serialize();     
    $.ajax({
       type: "POST",
       url: "/admingo/handlers/pm.php",
       data: params,
       async: false,
       success: function(result){
          window.lib.pmGet();
          $('#pmForm [name="content"]').val('');
          $('.attachment p').remove();
          $('#'+lib.flag+' [type=submit]').removeClass('disabled');          
          $('#'+lib.flag+' [name=content]').val('');
          return false;
       }
    });
    return false;
  });         
  if ($('#pmForm').length) {
    setInterval(lib.pmGet, 5000);
    
    $('.pmList').scrollTo(10000);
                 
    $(document.body).on('click', '.attachment i', function(event) {
      $('.attachment p[iid="'+$(this).attr('iid')+'"]').remove();         
    });
  
  	var upload = new AjaxUpload($('#pmAttach'), {
  		action: '/admingo/handlers/fileupload.php',
  		name: 'uploadfile',
  		data: {action: 'pm'},
  		onSubmit: function(file, ext){
  		  $('.fileinfo').hide();
  			if (!(ext && /^(jpg|jpeg|png|gif|bmp|doc|docx|rtf|xls|xlsx|pdf|ppt|psd|txt|zip|rar)$/.test(ext))){       
  				$('.fileinfo').show().text('Недопустимый формат');
  				return false;
  			}				
  			$('.fileinfo').show().text('Идет загрузка...');		
  		},
  		onComplete: function(file, response){
        var clearResponse = response.replace('<embed id="embed_npwlo" type="application/npwlo" height="0">','');      		    		  
        $(".attachment").append('<p iid="'+clearResponse+'"><a target="_new" href="'+lib.getImgLink(clearResponse,'')+'">'+file+'</a><span></span><i iid="'+clearResponse+'"></i><input type="hidden" name="file[]" value="'+clearResponse+'"><input type="hidden" name="filename[]" value="'+file+'"></p>');
        $('.fileinfo').hide();                           			
  		}
  	});    	    	
  }      
  /* //pm */  
  
  $("#slider_radius [data-slider]").bind("slider:ready slider:changed", function (event, data) {
    $("#current_radius").html(data.value.toFixed(0) + '&nbsp;m');
  });
  
  
  $(document.body).on('click', '.showMoreMess', function(e) {
    $(this).remove();
    $('.messList .item').removeClass('hidden');
  });
  
  /* events */
  $('.evlink').on('click', function(event) {
    if ($(this).hasClass('active')) {
      $(this).removeClass('active');
    } else {
      $(this).addClass('active');
      if ($('.evlink>span').length) {
        $('.evlink>span').remove();
        var iid = parseInt($('.evlink .win .item:first').attr('iid'));
        $.ajax({
           type: "POST",
           url: "/admingo/handlers/event.php",
           data: 'action=read&id='+iid,
           dataType: 'JSON',
           async: true,
           success: function(response) {}
        });                 
      }
    }
  });
  
    
  /* //events */    

});
$(window).resize(function() {
  if ($('#itemsList').length) {
    var h = $('body').height()-50;
    $('#itemsList').height(h);
    $('#map').height(h);    
  }
});
