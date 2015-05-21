window.lib = (function() {
  var flag = 0;
  
  var hideErrors = function() {
    $('.infopanel div').remove();
  }
  var showErrors = function(error, form, scroll) {
    var inf = $('.infopanel',form);
    $('div',inf).remove();
    inf.html('<div class="alert alert-error">'+error+'</div>');
    if (scroll) $(document).scrollTop(0);
  }  
  
  var showSuccess = function(success, form, scroll) {
    var inf = $('.infopanel',form);     
    $('div',inf).remove();
    inf.html('<div class="alert alert-success">'+success+'</div>');
    if (scroll) $(document).scrollTop(0);        
  }  
  
  var getUrlVars = function () {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
  }
  
  var makeUrl = function (params) {
    var url = '';
    for (var i=0; i<params.length; i++) {
      if (params[i].length) url += params[i]+'&';
    }
    if (url.length) url = '?'+url.substring(0, url.length-1);
    return url;
  }            
  
  var isValidEmail = function(email) {
    email = email.replace(/^\s+|\s+$/g, '');
    return (/^([a-z0-9_-]+.)*[a-z0-9_-]+@([a-z0-9][a-z0-9-]*[a-z0-9].)+[a-z]{2,4}$/i).test(email);
  }  
  
  var getCookieVal = function(offset) {
     var endstr = document.cookie.indexOf (";", offset);
     if (endstr == -1)
        endstr = document.cookie.length;
     return unescape(document.cookie.substring(offset, endstr));
  }
  
  var GetCookie = function(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
            var j = i + alen;
            if (document.cookie.substring(i, j) == arg)
                    return getCookieVal (j);
            i = document.cookie.indexOf(" ", i) + 1;
                    if (i == 0)
                            break;
            }
     return null;
  }
  
  var SetCookie = function(name, value) {
    var argv = SetCookie.arguments;
    var argc = SetCookie.arguments.length;
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
  
  var deleteCookie = function (name) {
    SetCookie(name, null, new Date(0));
    return true;
  }  
 
  
  return {
      flag: flag, 
      showErrors: showErrors,
      hideErrors: hideErrors,
      showSuccess: showSuccess,
      getUrlVars: getUrlVars,
      makeUrl: makeUrl,
      isValidEmail: isValidEmail,      
      SetCookie: SetCookie,
      GetCookie: GetCookie,
      deleteCookie: deleteCookie                  
  };
})(jQuery);