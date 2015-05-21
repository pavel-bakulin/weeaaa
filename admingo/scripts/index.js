$(document).ready(function () {

  $('#doctypes a.doc').on("click",function(event) {
    $('#doctypes').modal('hide');
  });    

  $('#createDoc').on("click",function(event) {
    $('#doctypes').modal();
  });
  
  $('#upBtn').on("click",function(event) {
    location.href = $(this).attr('href');
  });    
  
  $('body').on("click",function(event) {
		var elt = (event.target) ? event.target : event.srcElement;			
		if (!($(elt).is('.folderIcon') || $(elt).is('.folderIcon *') || $(elt).is('.docIcon'))) {  
      $('#docmenu').hide();
      $('#foldmenu').hide();
    }
    if (!($(elt).is('#doctypes') || $(elt).is('#doctypes *') )) {
      $('#doctypes').hide();
    }
  });    
  
  $('#selectMode').on("click",function(event) {
    if ($(this).hasClass('on')) {
      $('.docidCheckbox').show();
      $('.selectMode label').show();
      $(this).removeClass('on').text('Выключить режим мультивыбора');
    } else {
      $('.docidCheckbox').hide();
      $('.selectMode label').hide();
      $(this).addClass('on').text('Включить режим мультивыбора');
    }
  });
  
  $('.ckeckAll').on("change",function(event) {
    if ($(this).is(':checked')) {
      $('.docidCheckbox').attr('checked','checked');
      $('.withSelects').show();
    } else {
      $('.docidCheckbox').removeAttr('checked');
      $('.withSelects').hide();
    } 
  });
  
  $('.docidCheckbox').on("change",function(event) {
    if ($('.docidCheckbox').is(':checked')) {
      $('.withSelects').show();
    } else {
      $('.withSelects').hide();
    }
  });
  
  $('#multycopy').on("click",function(event) {
    var ids = '';
    $('.docidCheckbox').each(function(){
      if ($(this).is(':checked')) {
        ids += $(this).val()+',';
      }      
    })
    if (ids.length) ids = ids.substr(0,ids.length-1);
    else return false;
    
    copyDoc(ids);
  });
  
  $('#multyremove').on("click",function(event) {
    if (confirm('Уверены, что хотите удалить выбранные документы?')) {
      var ids = '';
      $('.docidCheckbox').each(function(){
        if ($(this).is(':checked')) {
          ids += $(this).val()+',';
        }      
      })
      if (ids.length) ids = ids.substr(0,ids.length-1);
      else return false;
      
      multyRemove(ids);
    }
  });    
});	