;(function($) {

	$(document).ready( function() {

		$('table.edd-bk-avail-table select').chosen({ width: '100%' });
		$('#edd_bk_box .inside select').chosen();
		$('#edd-bk-avail-checker').click( function() {
			availChecker();
		});

		// TOGGLERS
		var togglers = {
			// Main toggler
			"#edd_bk_enabled": [ "click",
				function() {
					var enabled = $("#edd_bk_enabled").is(":checked");
					$("fieldset.edd-bk-option-section").toggle( enabled );

					var edd_metaboxes_to_hide = [
						'#edd_product_prices',
						'#edd_product_files',
						'#edd_product_settings'
					];
					$(edd_metaboxes_to_hide.join(',')).toggle( !enabled );
				}
			],

			// Session Duration Type
			"[type=radio][name=edd_bk_session_type]": [ "change",
				function() {
					var fixed = $('[type=radio][name=edd_bk_session_type]:checked').val() == 'fixed';
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

		}; // End of Togglers

		// Initialize togglers
		for( selector in togglers ) {
			var toggler = togglers[selector];
			var events = toggler[0];
			if ( !( events instanceof Array ) ) {
				events = [ events ];
			}
			var fn = toggler[1];
			for ( i in events ) {
				$(selector).on( events[i], fn );
			}
			fn();
		}

		// Availability table
		$('button#edd-bk-avail-add-btn').click(function() {
			// variable 'eddBkAvailabilityTableRow' brought in from PHP script localization
			var tr = $(eddBkAvailabilityTableRow);
			$('table.edd-bk-avail-table tbody').append( tr );
			edd_bk_init_new_row(tr);
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
			containment: '#edd-bk-availability-section table',
			axis: 'y',
			opacity: 0.8,
			revert: 200
		}).disableSelection();

		// On submission, change the hidden field after each availability entry's 'available' checkbox to
		// the correct value. This resolves double submission of checkbox values
		$('form#post').submit( function() {
			$('.edd_bk_availability_checkbox').each( function() {
				var b = $(this).is(':checked')? '1' : '0';
				$(this).next().val(b);
				console.log('Assigned value ', b);
			});
		});
		
	}); // End of $(document).ready()
	

	function edd_bk_init_new_row( tr ) {
		// On Range Type change
		tr.find('.edd-bk-range-type').change( function(){
			edd_bk_update_tr_range_type( tr );
		});
		tr.find('select').chosen({ width: '100%' });

		edd_bk_update_tr_range_type( tr );
		tr.find('.edd-bk-remove-td').click(function(){ tr.remove(); });
		initDatePickers( tr );
	}

	function edd_bk_update_tr_range_type( tr ) {
		// Get the range selected
		var rangeType = tr.find('select.edd-bk-range-type').val();
		// Get the optgroup parent of the range
		var groupType = '[' + tr.find('select.edd-bk-range-type option:selected').parent().attr('label') + ']';
		// Get the 'from' and 'to' cells
		var from_to_tds = tr.find('td.edd-bk-from-to');
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
		// Help updater - on tr input changes
		tr.find('.edd-bk-range-type, td.edd-bk-from-to div:visible .edd-bk-avail-input, input.edd_bk_availability_checkbox').on( 'change', function(){
			updateHelp( tr );
		});
		updateHelp(tr);
	}

	function initDatePickers( tr ) {
		var t = typeof tr === 'undefined' ? $(document) : $(tr);
		t.find('input.edd-bk-datepicker').datepicker();
		t.find('input.edd-bk-datepicker + i.fa').click( function(){
			$(this).prev().datepicker( 'show' );
		});
	}

	function renameAttr(elems, oldName, newName) {
		elems.each( function(){
			var t = $(this);
			t.attr(newName, t.attr(oldName)).removeAttr(oldName);
		});
	}

	function updateHelp( tr ) {
		var range_type = tr.find('select.edd-bk-range-type').val();
		var inputs = tr.find('td.edd-bk-from-to div:visible .edd-bk-avail-input');
		var from = $(inputs[0]).val();
		var to = $(inputs[1]).val();
		var available = tr.find('input.edd_bk_availability_checkbox').is(':checked');
		var help_str = range_to_text(range_type, from, to, available);
		tr.find('td.edd-bk-help-td .edd-bk-help div').text( ucfirst(help_str) );
	}

	function range_to_text(range_type, from, to, available) {
		// ensure 'from' and 'to' are strings
		from += '';
		to += '';
		// get the index
		var index = range_type;
		if ( edd_bk_utils.weekdays.indexOf( ucfirst(range_type) ) > -1 ) {
			index = 'dotw';
		}
		return (available? eddBkMsgs.available : eddBkMsgs.unavailable) + ' ' + sprintf( eddBkMsgs.tableHelp[index], ucfirst(from), ucfirst(to), ucfirst(range_type) );
	}

	function ucfirst(str) {
		return (str.length < 2)? str : str[0].toUpperCase() + str.substr(1);
	}

	function availChecker() {
		var mood = null;
		var buffer = 'I am ';
		$('table.edd-bk-avail-table tbody tr').each( function() {
			var range_type = $(this).find('select.edd-bk-range-type').val();
			var inputs = $(this).find('td.edd-bk-from-to div:visible .edd-bk-avail-input');
			var from = $(inputs[0]).val();
			var to = $(inputs[1]).val();
			var available = $(this).find('input.edd_bk_availability_checkbox').is(':checked');
			var text = range_to_text(range_type, from, to, available);
			if ( mood === null ) {
				buffer += text;
			} else if ( available === mood ) {
				buffer += '<br/>and ' + text;
			} else {
				buffer += '<br/>but ' + text;
			}
			mood = available;
		});
		buffer = '<i class="fa fa-quote-left"></i> ' + buffer + ' <i class="fa fa-quote-right"></i>';
		$('#edd-avail-checker-text').remove();
		$(document.createElement('div')).attr('id', 'edd-avail-checker-text').html(buffer).insertAfter(
			'#edd-bk-avail-checker'
		);
	}

})(jQuery);
