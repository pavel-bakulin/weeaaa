window.lib = new (function(){
  this.flag = 0;
  this.userid = 0;
  this.userstatus = 0;  
  this.userimage = '/img/noava40.gif';
  this.socid = 0;
  this.currentHash = '';
  this.objectFormVisible = false;
  this.ll_loading = false;
  this.ll_value;
  this.disableAJAX = false;

  this.getImgLink = function(img,pre) {
    var path = '/uploads/'+img.substring(0,2)+'/';
    if (pre.lemgth) path = path+pre+'/';
    return path+img;
  }
      
  this.checkForm = function(form) {  
    this.flag = form.attr('id');
    if ($('button[type=submit]',form).hasClass('disabled')) return false;
    $('input[type=submit]',form).addClass('disabled');    
    var error = [];
    var required=true;
    this.hideErrors();
    $("[required='true']:visible",form).each(function (i) {
      $(this).removeClass('has-error');
      if (!$(this).val().length) {
        $(this).addClass('has-error');
        required=false;        
      }
    });
    if (!required) {
      $('input[type=submit]',form).removeClass('disabled');
      return false;
    }    
    return true;
  }
    
  this.hideErrors = function() {
    $('.infopanel div').remove();
  }
  this.showErrors = function(error, form, scroll) {
    var inf = $('.infopanel',form);
    $('div.alert',inf).remove();
    inf.html('<div class="alert alert-danger" role="alert">'+error+'</div>');
    if (scroll) $(document).scrollTop(0);
  }  
  
  this.showSuccess = function(success, form, scroll) {
    var inf = $('.infopanel',form);     
    $('div.alert',inf).remove();
    inf.html('<div class="alert alert-success" role="alert">'+success+'</div>');    
    if (scroll) $(document).scrollTop(0);        
  }  
  
  this.isValidEmail = function(email) {
    email = email.replace(/^\s+|\s+$/g, '');
    return (/^([a-z0-9_-]+.)*[a-z0-9_-]+@([a-z0-9][a-z0-9-]*[a-z0-9].)+[a-z]{2,4}$/i).test(email);
  }  
  
  this.getCookieVal = function(offset) {
     var endstr = document.cookie.indexOf (";", offset);
     if (endstr == -1)
        endstr = document.cookie.length;
     return unescape(document.cookie.substring(offset, endstr));
  }
  
  this.GetCookie = function(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
            var j = i + alen;
            if (document.cookie.substring(i, j) == arg)
                    return this.getCookieVal (j);
            i = document.cookie.indexOf(" ", i) + 1;
                    if (i == 0)
                            break;
            }
     return null;
  }
  
  this.SetCookie = function(name, value) {  
    var argv = this.SetCookie.arguments;
    var argc = this.SetCookie.arguments.length;
    var expires = (argc > 2) ? argv[2] : null;
    var path = '/';
    var domain = (argc > 4) ? argv[4] : null;        
    var secure = (argc > 5) ? argv[5] : false;
    document.cookie = name + "=" + escape (value) +
            ((expires == null) ? "" : ("; expires=" +
            expires.toGMTString())) +
            ((path == null) ? "" : ("; path=" + path)) +
            ((domain == null) ? "" : ("; domain=" + domain)) +
            ((secure == true) ? "; secure" : "");
  }
  
  this.deleteCookie = function (name) {
    this.SetCookie(name, null, new Date(0));
    return true;
  }  
  
  this.pmGet = function () {
    var params = 'touserid='+lib.currentAdresatId+'&lastMessId='+$('.pmList .item:last').attr('iid');
    $.ajax({
       type: "GET",
       url: "/admingo/handlers/pmGet.php",
       data: params,
       async: false,
       success: function(msg){
         $('.pmList').append(msg);
         if (msg.length) $('.pmList').scrollTo(5000);       
       }
    });    
  }  
  
  this.base64decode = function(str) { 
    // Символы для base64-преобразования 
    var b64chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefg'+ 
                   'hijklmnopqrstuvwxyz0123456789+/='; 
    var b64decoded = ''; 
    var chr1, chr2, chr3; 
    var enc1, enc2, enc3, enc4; 
  
    str = str.replace(/[^a-z0-9\+\/\=]/gi, ''); 
  
    for (var i=0; i<str.length;) { 
        enc1 = b64chars.indexOf(str.charAt(i++)); 
        enc2 = b64chars.indexOf(str.charAt(i++)); 
        enc3 = b64chars.indexOf(str.charAt(i++)); 
        enc4 = b64chars.indexOf(str.charAt(i++)); 
  
        chr1 = (enc1 << 2) | (enc2 >> 4); 
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2); 
        chr3 = ((enc3 & 3) << 6) | enc4; 
  
        b64decoded = b64decoded + String.fromCharCode(chr1); 
  
        if (enc3 < 64) { 
            b64decoded += String.fromCharCode(chr2); 
        } 
        if (enc4 < 64) { 
            b64decoded += String.fromCharCode(chr3); 
        } 
    } 
    return b64decoded; 
  }    
  
  this.escapeHtml = function (text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };  
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }  
  
  this.hashChange = function () {
    var hash = location.hash.substr(1);
    if (hash==lib.currentHash) return false;
    lib.currentHash = hash;
    var regexp = /object([0-9]+)/i;
    if (regexp.test(hash)) {
      matches = hash.match(regexp);
      var oid = matches[1];    
      var params = 'action=getitem&id='+oid;
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/object.php",
         data: params,
         dataType: 'JSON',
         async: false,
         success: function(response) {
           yoEjs.render('#modalInfoContent','/ejs/object.ejs', response);
           lib.showObject(response.oid,response.socid,response.lat,response.long);
           lib.button_share42_show();           
         }       
      });          
    }
  }  
  
  this.showObject = function (oid,socid,lat,long) {
    $('#modalInfo').modal();
    
    setTimeout(function(){
      var h = ($('#modalInfoContent .text').length)?parseInt($('#modalInfoContent .text').height()):0;    
      $('#modalInfoContent .messList').height(432-h);
      $('#modalInfoContent .messList .notext').show();
    },220);
    
    $('.marker').removeClass('active');
    $('#marker'+socid).addClass('active');           
    gmap.objectMapView.map.panTo(new google.maps.LatLng(lat,long));    
    
    if (parseInt(oid)>0) {
      var params = 'action=getlist&oid='+oid+'&socid='+socid; 
      $.ajax({
         type: "POST",
         url: "/admingo/handlers/forum.php",
         data: params,
         dataType: 'JSON',
         async: false,
         success: function(response) {
           $('.messList .preloader').remove();
           if (response.data) {                  
            yoEjs.render('.messList','/ejs/forum_mess.ejs', response);
            setTimeout(function(){$('.messList').scrollTo(10000);},200);
           }           
           $('#forumForm [name=content]').val('');
         },
         error: function(response) {
           $('.messList .preloader').remove();
         }       
      });    
    } else {
      $('.messList .preloader').remove();
    }
    gmap.clickAction = true;  
  }
  
  this.zerofill = function(s) { return (s < 10) ? '0' + s : s; }  
  
  this.fromNow = function(date) {
    var year = date.substr(6,4);
    var month = parseInt(date.substr(3,2))-1;
    var day = parseInt(date.substr(0,2));
    var hour = date.substr(11,2);
    var min = date.substr(14,2);
    var sec = 0;    
    if (moment().diff(moment([year,month,day]), 'days')<=0) {
      moment.lang('ru');
      return moment([year,month,day,hour,min,sec]).fromNow(); 
    } else {
      day = this.zerofill(day);
      month=month+1; 
      month = this.zerofill(month);
      return day+'.'+month+'.'+year;
    }
  }
  
  this.dateDiff = function(date) {  
    var year = date.substr(6,4);
    var month = parseInt(date.substr(3,2))-1;
    var day = parseInt(date.substr(0,2));
    var hour = date.substr(11,2);
    return -moment().diff(moment([year,month,day,hour]), 'hours');
  }  
  
  this.calcUTC = function(date) {  
    var year = date.substr(6,4);
    var month = date.substr(3,2)-1;
    var day = date.substr(0,2);
    var hour = date.substr(11,2);
    var min = date.substr(14,2);
    var sec = 0;      
    var d = new Date(year,month,day,hour,min,sec);
    var utc = d.getTime() - (d.getTimezoneOffset() * 60000);
    var nd = new Date(utc);        
    console.log(nd.toString('dd.MM.yyyy HH:mm:ss'));
    return nd.toString('dd.MM.yyyy HH:mm:ss');
  }
  
  this.button_share42_show = function(){
    if($("#share42init").length > 0 && parseInt($("#share42init").attr('oid')) > 0){
      if (!window.location.origin) {
      window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
      }
      $("#share42init").attr('data-url',window.location.origin + '/object' + $('.share42init').attr('oid')) + '/';
      $("#share42init").attr('data-title',window.location.origin + '/object' + $('.share42init').attr('oid')) + '/';
      $("#share42init").attr('data-description', $(".info .top .text").text());
      $("#share42init").attr('data-image',$('.img .bigimg').attr('src'));
      this.share42();
    }
  }
  
  this.objectFormReset = function() {
    $('#objectForm').find("input[type=text], textarea").val("");
    /*$('#objectForm').find("input[type=radio]").removeAttr('checked');
    $('#objectForm').find("input[type=radio]:first").attr('checked','checked');*/
    $('#objectForm [name=action]').val('add');
    $('#objectForm').find("#files").html('');
    $('#objectForm .fileinfo').html('');      
  }
  
  this.strrpos = function(haystack, needle, offset) {
    var i = -1;
    if (offset) {
      i = (haystack + '')
        .slice(offset)
        .lastIndexOf(needle);
      if (i !== -1) {
        i += offset;
      }
    } else {
      i = (haystack + '')
        .lastIndexOf(needle);
    }
    return i >= 0 ? i : false;
  }  
  
  this.peopleCount = function(count) {
    if (count % 10 == 1 && (count<10 || count>20)) return 'человеку';
    else return 'людям'; 
  }  
      
  this.share42 = function(){
    $('div.share42init').each(function(idx){var el=$(this),u=el.attr('data-url'),t=el.attr('data-title'),i=el.attr('data-image'),d=el.attr('data-description'),f=el.attr('data-path'),fn=el.attr('data-icons-file'),z=el.attr("data-zero-counter");if(!u)u=location.href;if(!fn)fn='icons.png';if(!z)z=0;if(!f){function path(name){var sc=document.getElementsByTagName('script'),sr=new RegExp('^(.*/|)('+name+')([#?]|$)');for(var p=0,scL=sc.length;p<scL;p++){var m=String(sc[p].src).match(sr);if(m){if(m[1].match(/^((https?|file)\:\/{2,}|\w:[\/\\])/))return m[1];if(m[1].indexOf("/")==0)return m[1];b=document.getElementsByTagName('base');if(b[0]&&b[0].href)return b[0].href+m[1];else return document.location.pathname.match(/(.*[\/\\])/)[0]+m[1];}}return null;}f=path('share42.js');}if(!t)t=document.title;if(!d){var meta=$('meta[name="description"]').attr('content');if(meta!==undefined)d=meta;else d='';}u=encodeURIComponent(u);t=encodeURIComponent(t);t=t.replace(/\'/g,'%27');i=encodeURIComponent(i);d=encodeURIComponent(d);d=d.replace(/\'/g,'%27');var fbQuery='u='+u;if(i!='null'&&i!='')fbQuery='s=100&p[url]='+u+'&p[title]='+t+'&p[summary]='+d+'&p[images][0]='+i;var vkImage='';if(i!='null'&&i!='')vkImage='&image='+i;var s=new Array('"#" data-count="fb" onclick="window.open(\'http://www.facebook.com/sharer.php?m2w&'+fbQuery+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Поделиться в Facebook"','"#" data-count="gplus" onclick="window.open(\'https://plus.google.com/share?url='+u+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Поделиться в Google+"','"#" data-count="mail" onclick="window.open(\'http://connect.mail.ru/share?url='+u+'&title='+t+'&description='+d+'&imageurl='+i+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Поделиться в Моем Мире@Mail.Ru"','"#" data-count="odkl" onclick="window.open(\'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl='+u+'&title='+t+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Добавить в Одноклассники"','"#" data-count="twi" onclick="window.open(\'https://twitter.com/intent/tweet?text='+t+'&url='+u+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Добавить в Twitter"','"#" data-count="vk" onclick="window.open(\'http://vk.com/share.php?url='+u+'&title='+t+vkImage+'&description='+d+'\', \'_blank\', \'scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0\');return false" title="Поделиться В Контакте"');var l='';for(j=0;j<s.length;j++)l+='<span class="share42-item" style="display:inline-block;margin:0 6px 6px 0;height:32px;"><a rel="nofollow" style="display:inline-block;width:32px;height:32px;margin:0;padding:0;outline:none;background:url('+f+fn+') -'+32*j+'px 0 no-repeat" href='+s[j]+' target="_blank"></a></span>';el.html('<span id="share42">'+l+'</span>'+'');});  
  }
  
  this.htmlspecialchars_decode = function (string, quote_style) {
    var optTemp = 0,
      i = 0,
      noquotes = false;
    if (typeof quote_style === 'undefined') {
      quote_style = 2;
    }
    string = string.toString()
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>');
    var OPTS = {
      'ENT_NOQUOTES': 0,
      'ENT_HTML_QUOTE_SINGLE': 1,
      'ENT_HTML_QUOTE_DOUBLE': 2,
      'ENT_COMPAT': 2,
      'ENT_QUOTES': 3,
      'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
      noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
      quote_style = [].concat(quote_style);
      for (i = 0; i < quote_style.length; i++) {
        // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
        if (OPTS[quote_style[i]] === 0) {
          noquotes = true;
        } else if (OPTS[quote_style[i]]) {
          optTemp = optTemp | OPTS[quote_style[i]];
        }
      }
      quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
      string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
      // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
    }
    if (!noquotes) {
      string = string.replace(/&quot;/g, '"');
    }
    // Put this in last place to avoid escape being double-decoded
    string = string.replace(/&amp;/g, '&');
  
    return string;
  }  
})(jQuery);