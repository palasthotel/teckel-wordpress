jQuery(function($) {
	$('document').ready(function(){
	 if ($('#teckeledcomments')[0]) {
		var whichpost = $('#teckeledcomments').data('wpid');
		jQuery.post(
			TheAjax.ajaxurl, 
			{
				action: 'teckel_replace',
				security: TheAjax.teckel_nonce,
				thepost: whichpost
			},
			function(response) {
			$('#teckeledcomments').after(response);
			$('#teckeledcomments').remove();
        });
	 }
	});
	
});