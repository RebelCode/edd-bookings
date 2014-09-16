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

			// Slot Duration Type
			"[type=radio][name=edd_bk_pricing]": [ "change",
				function() {
					var fixed = $('[type=radio][name=edd_bk_pricing]:checked').val() === 'fixed';
					var text = fixed? 'Cost:' : 'Base cost:';
					$("label[for=edd_bk_base_cost]").text( text );
					$('.edd-bk-variable-pricing-section').toggle( !fixed );
					calculateTotalCost();
				}
			],

			// Base Cost and Cost per Slot on change
			"#edd_bk_base_cost, #edd_bk_cost_per_slot": [ "change",
				function() {
					calculateTotalCost();
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

	var calculateTotalCost = function() {
		var base = $('#edd_bk_base_cost').val();
		base = base == ''? '0' : base;

		var per_slot = $('#edd_bk_cost_per_slot').val();
		per_slot = per_slot == ''? '0' : per_slot;

		var text = base + ' + (' + per_slot + ' per slot)';
		$('#edd-bk-total-cost-preview').text( text );
	}

})(jQuery);
