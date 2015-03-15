/*
sz-comment-filter plugin
wordpress.org/plugins/sz-comment-filter/
*/
(function($) {
	var smzsf_getkeytm;

	$('#commentform').submit(function(event){
		if(smzsf_getkeytm > parseInt( new Date() /1000 ) - 10 ){
			return true;
		}
		
		// default submit cancel.
		//event.preventDefault();
		$('#submit').attr('disabled', true);		
		$.ajax({
	        type: 'POST',
	        url: szmcf_ajaxurl,
	        cache: false,
            dataType: 'text',
			data: {
	            'action' : 'szmcf_currentkey',
	        },
	        success: function( response ){
	    		smzsf_getkeytm = parseInt( new Date() /1000 );
				$('#szmcf-key').val(response);		
				$('#submit').attr('disabled', false);
				$('#submit').removeAttr('disabled');
				
				setTimeout(function(){
			        $('#submit').click();
				},300);

	        },
	        error: function(){
				$('#submit').attr('disabled', false);
				$('#submit').removeAttr('disabled');
				alert("Message sending not comleted.[Server Error]");
			}
	    });
		return false;
	});

	$(document).ready(function(){
		smzsf_getkeytm = 0;
		jQuery("#szmcf-input").css("display", "none");
    });

})(jQuery);

