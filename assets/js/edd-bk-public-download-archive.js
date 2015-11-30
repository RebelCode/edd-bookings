;(function($){

	$(document).ready( function() {
		// Remove purchase options. Bookings need to be visited singularly for viewing the calendar and choosing options, before being purchased
		$('.edd_purchase_submit_wrapper').remove();
		// Remove the "plus" icon from the Vendd theme
		$('.product-link.vendd-show-button').remove();
	});

})(jQuery);
