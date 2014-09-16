(function($){

	$(document).ready( function() {
		// TOGGLERS
		var togglers = {

			// Main toggler
			"#edd_bk_enabled": [ "click",
				function(){
					$("fieldset.edd-bk-option-section").toggle( $("#edd_bk_enabled").is(":checked") );
				}
			],

			// All day toggler
			"#edd_bk_all_day": [ "click",
				function() {
					$("#edd_bk_start_time, #edd_bk_end_time").toggle( $("#edd_bk_all_day").is(":not(:checked)") );
				}
			],

			// Slot Duration Type
			"[type=radio][name=edd_bk_slots_type]": [ "change",
				function() {
					var fixed = $('[type=radio][name=edd_bk_slots_type]:checked').val() == 'fixed';
					$(".edd_bk_variable_slots_section").toggle( !fixed );
				}
			],

			// Min duration - on change set the max field's minimum to the min field's value
			"#edd_bk_slot_min_duration": [ "change",
				function() {
					var _this = $('#edd_bk_slot_min_duration');
					$('#edd_bk_slot_max_duration').attr('min', _this.val());
				}
			],

			// Max duration - on change set the min field's minimum to the max field's value
			"#edd_bk_slot_max_duration": [ "change",
				function() {
					var _this = $('#edd_bk_slot_max_duration');
					$('#edd_bk_slot_min_duration').attr('max', _this.val());
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
