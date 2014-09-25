(function($){

	$(document).ready( function() {

		$('table.edd-bk-avail-table select').chosen({ width: '100%' });
		$('#edd_bk_box .inside select').chosen();

		// TOGGLERS
		var togglers = {
			// Main toggler
			"#edd_bk_enabled": [ "click",
				function(){
					$("fieldset.edd-bk-option-section").toggle( $("#edd_bk_enabled").is(":checked") );
				}
			],

			// Slot Duration Type
			"[type=radio][name=edd_bk_duration_type]": [ "change",
				function() {
					var fixed = $('[type=radio][name=edd_bk_duration_type]:checked').val() == 'fixed';
					$(".edd_bk_variable_slots_section").toggle( !fixed );
					$("label[for=edd_bk_slot_duration]").text( fixed? 'Duration' : 'Where each slot is' );
					// Costing options
					$('#edd_bk_variable_pricing').prop( 'disabled', fixed );
					if ( fixed && $('#edd_bk_variable_pricing').is(':checked') ) {
						$('#edd_bk_fixed_pricing').prop( 'checked', true );
						$('#edd_bk_variable_pricing').prop( 'checked', false );
					}
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
			"[type=radio][name=edd_bk_price_type]": [ "change",
				function() {
					var fixed = $('[type=radio][name=edd_bk_price_type]:checked').val() === 'fixed';
					var text = fixed? 'Cost' : 'Base cost';
					$("label[for=edd_bk_base_cost]").text( text );
					$('.edd-bk-variable-pricing-section').toggle( !fixed );
					calculateTotalCost();
				}
			],

			// Base Cost and Cost per Slot on change
			"#edd_bk_base_cost, #edd_bk_cost_per_slot": [ "keyup",
				function() {
					calculateTotalCost();
				}
			],

		}; // End of Togglers

		// Initialize togglers
		for( selector in togglers ) {
			var toggler = togglers[selector];
			var event = toggler[0];
			var fn = toggler[1];
			$(selector).on( event, fn );
			fn();
		}

		// Availability table
		$('button#edd-bk-avail-add-btn').click(function(){
			var tr = $(availabilityTableRow);
			edd_bk_init_new_row(tr);
			$('table.edd-bk-avail-table tbody').append( tr );
		});

		$('table.edd-bk-avail-table tbody tr').each( function(){
			edd_bk_init_new_row( $(this) );
		});

		$('table.edd-bk-avail-table tbody').sortable({
			helper: function(e, tr) {
				var originals = tr.children();
				var helper = tr.clone();
				helper.children().each( function(i) {
					$(this).width(originals.eq(i).width());
				});
				helper.css('box-shadow', '0 0 8px rgba(0,0,0,0.4)');
				return helper;
			},
			handle: 'td.edd-bk-sort-td',
			distance: 5,
			containment: '#edd-bk-availability-section tbody',
			axis: 'y',
			opacity: 0.8,
			revert: 200
		}).disableSelection();


		$('form#post').submit( function(){
			$('.edd_bk_availability_checkbox').each( function(){
				var b = $(this).is(':checked');
				var b = b? 'true' : 'false';
				$(this).next().val(b);
				console.log('Assigned value ', b);
			});
		});


	}); // End of $(document).ready()
	

	var calculateTotalCost = function() {
		var base = $('#edd_bk_base_cost').val();
		base = base == ''? '0' : base;

		var per_slot = $('#edd_bk_cost_per_slot').val();
		per_slot = per_slot == ''? '0' : per_slot;

		var text = base + ' + (' + per_slot + ' per slot)';
		$('#edd-bk-total-cost-preview').text( text );
	};

	function edd_bk_init_new_row( tr ) {
		// On Range Type change
		tr.find('.edd-bk-range-type').change( function(){
			edd_bk_update_tr_range_type( tr );
		});
		tr.find('select').chosen({ width: '100%' });
		edd_bk_update_tr_range_type( tr );
		tr.find('.edd-bk-remove-td').click(function(){ tr.remove(); });
	}

	function edd_bk_update_tr_range_type( tr ) {
		// Get the range selected
		var rangeType = tr.find('select.edd-bk-range-type').val();
		// Get the optgroup parent of the range
		var groupType = '[' + tr.find('select.edd-bk-range-type option:selected').parent().attr('label') + ']';
		// Get the 'from' and 'to' cells
		var from_to_tds = tr.find('td.edd-bk-from-td, td.edd-bk-to-td');
		// For each div in both
		from_to_tds.find('> div').each( function(){
			// Get the data-if attr of the div
			var ifs = $(this).data('if').split('|');
			// Check if the selected range and group are in the attribute
			var range_in_ifs = $.inArray(rangeType,ifs) > -1;
			var group_in_ifs = $.inArray(groupType,ifs) > -1;
			// If they are
			if ( range_in_ifs || group_in_ifs ) {
				// Show the div
				$(this).show();
				// Rename any data-name attrs to name
				renameAttr( $(this).find('[data-name]'), 'data-name', 'name' );
			} else {
				// Hide it
				$(this).hide();
				// Rename all name attrs to data-name
				renameAttr( $(this).find('[name]'), 'name', 'data-name' );
			}
		});
	}

	function renameAttr(elems, oldName, newName) {
		elems.each( function(){
			var t = $(this);
			t.attr(newName, t.attr(oldName)).removeAttr(oldName);
		});
	}

})(jQuery);
