(function($){

	$(document).ready( function() {
		// TOGGLERS
		var togglers = {

			// Main toggler
			"#edd_bk_enabled": [
				"click",
				function(){
					$(".edd_bk_toggled_row").toggle( $("#edd_bk_enabled").is(":checked") );
				}
			],

			// All day toggler
			"#edd_bk_all_day": [
				"click",
				function() {
					$("#edd_bk_start_time, #edd_bk_end_time").toggle( $("#edd_bk_all_day").is(":not(:checked)") );
				}
			],

		};

		// Initialize togglers
		for( selector in togglers ) {
			var toggler = togglers[selector];
			var event = toggler[0];
			var fn = toggler[1];
			$(selector).on( event, fn );
			fn();
		}

	});

})(jQuery);
