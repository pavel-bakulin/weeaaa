window.soc = new (function(){
  this.auth = function(html,userid,image,result,status) {
    $('#profileBlock').html(html);
    lib.userid = userid;
    lib.userimage = image;
    lib.userstatus = status;
    $('#modalAuth').modal('hide');
    $('#showmy').show();
    $('.authfalse').removeClass('authfalse');
    
    $.ajax({
       type: "POST",
       url: "/admingo/handlers/event.php",
       data: 'action=getlist',
       async: false,
       success: function(response) {        
         $('#eventList').html(response);         
       }
    });    
  }
});