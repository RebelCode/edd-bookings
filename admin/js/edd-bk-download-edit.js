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
					//$("label[for=edd_bk_slot_duration]").text( fixed? 'Duration' : 'Where each session is' );
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

			// Base Cost and Cost per Slot on change
			"#edd_bk_base_cost, #edd_bk_cost_per_slot": [ "keyup",
				function() {
					refreshCostPreviewText();
				}

			],

			'#edd-bk-cost-sessions-preview' : [ ['keyup', 'change'],
				function() {
					calculatePreviewCost();
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
			containment: '#edd-bk-availability-section table',
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
	

	var refreshCostPreviewText = function() {
		var base = $('#edd_bk_base_cost').val();
		base = base == ''? '0' : base;

		var per_slot = $('#edd_bk_cost_per_slot').val();
		per_slot = per_slot == ''? '0' : per_slot;

		var text = base + ' + (' + per_slot + ' x ';
		$('#edd-bk-total-cost-preview .cost-static').text( text );

		calculatePreviewCost();
	};

	var calculatePreviewCost = function() {
		var base = $('#edd_bk_base_cost').val();
		base = base == ''? 0 : parseFloat( base );

		var per_slot = $('#edd_bk_cost_per_slot').val();
		per_slot = per_slot == ''? 0 : parseFloat( per_slot );

		var sessions = $('#edd-bk-cost-sessions-preview').val();
		sessions = sessions == '' ? 0 : parseFloat( sessions );

		var total = ( isNaN( base ) || isNaN( per_slot ) || isNaN( sessions ) )?
			0 : total = base + ( per_slot * sessions );

		$('#edd-bk-total-cost-preview .cost-total').text( total );
	};

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
		tr.find('.edd-bk-help div').text(help_str);
	}

	function range_to_text(range_type, from, to, available) {
		var str = available? 'Available' : 'Unavailable';
		// ensure 'from' and 'to' are strings
		from += '';
		to += '';
		switch (range_type) {
			case 'months':
			case 'days':
				str += ' from ' + ucfirst(from) + ' till ' + ucfirst(to);
				break;
			case 'weeks':
				str += ' from week ' + from + ' till week ' + to;
				break;
			case 'custom':
				str += ' from ' + from + ' till ' + to;
				break;
			case 'monday':
			case 'tuesday':
			case 'wednesday':
			case 'thursday':
			case 'friday':
			case 'saturday':
			case 'sunday':
				str += ' on ' + ucfirst( range_type ) + 's from ' + from + ' till ' + to;
				break;
			case 'all_week':
				str += ' all week from ' + from + ' till ' + to;
				break;
			case 'weekend':
				str += ' on weekends from ' + from + ' till ' + to;
				break;
			case 'weekdays':
				str += ' on week days from ' + from + ' till ' + to;
				break;
		}
		return str;
	}

	function ucfirst(str) {
		if (str.length < 2) {
			return str;
		} else {
			return str[0].toUpperCase() + str.substr(1);
		}
	}

})(jQuery);
