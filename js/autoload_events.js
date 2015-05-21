$(function() {
	$(window).scroll(function() {
		if ($(window).scrollTop() + 250 >= $(document).height() - $(window).height()) {
			if (!lib.disableAJAX) {
				$('#autoloading').show(); 
				lib.disableAJAX = true;
				params = {start : paramsAutoLoad.start, count : paramsAutoLoad.count, auto : true, type : paramsAutoLoad.type, data : paramsAutoLoad.data, action : 'getlist'};
        $.ajax({
           type: "POST",
           url: "/admingo/handlers/event.php",
           data: params,
           async: false,
           success: function(res) {             
             if (res.length) lib.disableAJAX = false; 
             paramsAutoLoad.start += paramsAutoLoad.count;             
             $('#autoloading').hide();
             $('.eventsFullList').append(res);
           }
         });
			}
		}
	});
});