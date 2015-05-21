window.yoEjs = new (function(tlist){
  var tplContents = [];
  var templates = {};
    
  for(var i in tlist){
    var _t = tlist[i];    
    if (_t && _t.name) {
      if (_t.data) {      
        templates[_t.name] = new EJS({text: lib.base64decode(_t.data)});
      } else {
        templates[_t.name] = new EJS({url: _t.name});
      }
    }
  }  

  this.render = function(_selector, _template, _object) {
    try {
      $(_selector).html(templates[_template].render(_object ? _object : undefined));
    } catch (e) {
      console.log(_template+': '+e);
    }
  }
  
  this.getRenderResult = function(_template, _object) {
    try {
      return templates[_template].render(_object ? _object : undefined);
    } catch (e) {
      console.log(_template+': '+e);
    }
  }
})([
  {
    name: '/ejs/multy.ejs',
    data: null
  },
  {
    name: '/ejs/forum_mess.ejs',
    data: null
  },      
  {
    name: '/ejs/object.ejs',
    data: null
  },  
  {
    name: '/ejs/llItem.ejs',
    data: null
  },  
]);