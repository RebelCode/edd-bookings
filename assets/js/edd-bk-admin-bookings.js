;(function($) {
	
	$(document).ready( function(){
		$('a.page-title-action').remove();
	});

	$(window).load( function(){
		$('input[name="screen_columns"][value="1"]').click();
	});

})(jQuery);