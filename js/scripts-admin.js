/*
sz-comment-filter plugin
wordpress.org/plugins/sz-comment-filter/
*/
jQuery(function($) {
	$('#szmcf_loglist .ditail').not(':first').hide();
	$('#szmcf_loglist .dathead').click(function() {
	    if($(this).next('.ditail').is(':visible')) {
	        $(this).next('.ditail').slideUp(300);
	    } else {
	        $(this).next('.ditail').slideDown(300).siblings('.ditail').slideUp(300);
	    }
	});
});
