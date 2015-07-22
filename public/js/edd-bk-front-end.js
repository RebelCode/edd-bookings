;(function($, EDD_BK, Utils) {

	// Element pointers
	var datepicker_element = null,
		datepicker_refresh = null,
		timepicker_element = null,
		timepicker_loading = null,
		timepicker_timeselect = null,
		edd_submit_wrapper = null;

	// On document ready
	$(document).ready( function() {
		// Init element pointers
		datepicker_element = $('#edd-bk-datepicker');
		datepicker_refresh = $('#edd-bk-datepicker-refresh');
		timepicker_element = $('#edd-bk-timepicker');
		timepicker_loading = $('#edd-bk-timepicker-loading');
		timepicker_timeselect = $('#edd-bk-timepicker select[name="edd_bk_time"]');
		edd_submit_wrapper = $('.edd_purchase_submit_wrapper');

		// Init the datepicker
		initDatePicker();

		datepicker_refresh.click( function() {
			datepicker_element.parent().addClass('loading');
			$.ajax({
				type: 'POST',
				url: EDD_BK.ajaxurl,
				data: {
					action: 'get_download_availability',
					post_id: EDD_BK.post_id
				},
				success: function( response, status, jqXHR ) {
					EDD_BK.meta.availability = response;
					datepicker_element.datepicker( 'refresh' )
					.parent().removeClass('loading');
				},
				dataType: 'json'
			});
		});

		$('body.single-download .edd-add-to-cart-label').text("Purchase");
		edd_submit_wrapper.hide();
	});

	/**
	 * Initializes the datepicker.
	 *
	 * @param range The range param to be handed to multiDatesPicker. Optional.
	 */
	var initDatePicker = function(range) {
		// Get the session duration unit
		var unit = EDD_BK.meta.slot_duration_unit.toLowerCase();
		// Check which datepicker function to use, depending on the unit
		var pickerFn = getDatePickerFunction( unit );
		// Stop if the datepicker function returned is null
		if ( pickerFn === null ) return;
		// Check if the range has been given. Default to the session duration
		if ( _.isUndefined(range) ) range =	EDD_BK.meta.slot_duration;
		// Set range to days, if the unit is weeks
		if ( unit === 'weeks' ) range *= 7;

		var options = {
			// Hide the Button Panel
			showButtonPanel: false,
			// Options for multiDatePicker. These are ignored by the vanilla jQuery UI datepicker
			mode: 'daysRange',
			autoselectRange: [0, range],
			adjustRangeToDisabled: true,
			altField: '#edd-bk-datepicker-value',
			// Prepares the dates for availability
			beforeShowDay: datepickerIsDateAvailable,
			// When a date is selected by the user
			onSelect: datepickerOnSelectDate,
		};

		// Apply the datepicker function on the HTML datepicker element
		$.fn[ pickerFn ].apply( datepicker_element, [options]);
	};

	var datepickerIsDateAvailable = function( date ) {
		var year = date.getFullYear();
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var dotw = ( ( date.getDay() + 6 ) % 7 ) + 1;
		var week = date.getWeekNumber();
		var available = Utils.strToBool( EDD_BK.meta.availability_fill );

		var finished = false;
		for (var unit in EDD_BK.availability) {
			var rules = EDD_BK.availability[ unit ];
			switch (unit) {
				case 'month':
					if ( _.has(rules, month) ) {
						available = rules[month];
						finished = true;
					}
					break;
				case 'week':
					if ( _.has(rules, week) ) {
						available = rules[week];
						finished = true;
					}
					break;
				case 'day':
					if ( _.has(rules, dotw) ) {
						available = rules[ dotw ];
						finished = true;
					}
					break;
				case 'custom':
					if ( _.has(rules, [year, month, day]) ) {
						available = _.get(rules, [year, month, day]);
						finished = true;
					}
					break;
			}
			if ( finished ) break;
		}

		return [available, ''];
	};


	var datepickerOnSelectDate = function( dateStr, inst ) {
		// If the element has the click-event suppression flag,
		if ( datepicker_element.data('suppress-click-event') === true ) {
			// Remove it and return
			datepicker_element.data('suppress-click-event', null);
			return;
		}
		// Show the loading and hide the timepicker
		timepicker_loading.show();
		timepicker_element.hide();
		// Refresh the timepicker via AJAX
		$.ajax({
			type: 'POST',
			url: EDD_BK.ajaxurl,
			data: {
				action: 'get_times_for_date',
				post_id: EDD_BK.post_id,
				date: dateStr
			},
			success: function( response, status, jqXHR ) {
				if ( ! ( response instanceof Array ) && ! ( response instanceof Object ) ) return;
				timepicker_timeselect.empty();
				for ( i in response ) {
					var parsed = response[i].split('|');
					var max = parsed[1];
					var rpi = parseInt( parsed[0] );
					var hrs = rpi / 3600;
					var mins = (rpi / 60) % hrs;
					var text = ('0' + hrs).slice(-2) + ":" + ('0' + mins).slice(-2);
					$( document.createElement('option') )
					.text(text)
					.data('val', rpi)
					.data('max', max)
					.appendTo(timepicker_timeselect);
				}
				timepicker_loading.hide();
				timepicker_element.show();
				edd_submit_wrapper.show();
				updateCalendarForVariableMultiDates();
			},
			dataType: 'json'
		});
	};

	/**
	 * Function that updates the cost of the booking.
	 */
	var updateCost = function() {
		var text = '';
		if ( EDD_BK.meta.duration_type == 'fixed' ) {
			text = parseFloat( EDD_BK.meta.cost_per_slot );
		} else {
			var num_slots = ( parseInt( $('[name="edd_bk_num_slots"]').val() ) || 1 ) / parseInt( EDD_BK.meta.slot_duration );
			text = parseFloat( EDD_BK.meta.cost_per_slot ) * num_slots;
		}
		$('p#edd-bk-price span').text( EDD_BK.currency + text );
	}
	// If the duration type is variable, run the updateCost function whnever the number of sessions is modified
	if ( EDD_BK.meta.duration_type == 'variable' ) {
		$(document).ready(function(){
			$('[name="edd_bk_num_slots"]').bind('change', function() {
				var val = parseInt( $(this).val() );
				var min = parseInt( $(this).attr('min') );
				var max = parseInt( $(this).attr('max') );
				$(this).val( Math.max( min, Math.min( max, val ) ) );
				updateCost();
			});
		});
	}
	// Run the function once on load
	updateCost();


	// For variable sessions
	var updateCalendarForVariableMultiDates = function() {
		if ( EDD_BK.meta.duration_type == 'variable' ) {
			// When the time changes, adjust the maximum number of sessions allowed
			timepicker_timeselect.unbind('change').on('change', function() {
				// Get the selected option's max data value
				var max_sessions = parseInt( $(this).find('option:selected').data('max') );
				// Get the field where the user enters the number of sessions, and set the max
				// attribute to the selected option's max data value
				var num_slots_input = $('input[name="edd_bk_num_slots"]').attr('max', max_sessions);
				// Value entered in the number roller
				var num_sessions = parseInt( num_slots_input.val() );
				// If the value is greater than the max
				if ( num_sessions > max_sessions ) {
					// Set it to the max
					num_slots_input.val( max_sessions );
					// Triger the change event
					num_slots_input.trigger('change');
				}
			});

			if ( EDD_BK.meta.slot_duration_unit == 'weeks' || EDD_BK.meta.slot_duration_unit == 'days' ) {
				$('input[name="edd_bk_num_slots"]').on('change', function() {
					// Get the number of weeks
					var range = parseInt( $(this).val() );
					// Re-init the datepicker
					initDatePicker(range);
					// Simulate user click on the selected date, to refresh the auto selected range
					$('#edd-bk-datepicker').data('suppress-click-event', true).find('.ui-datepicker-current-day').first().find('>a').click();
				});
			}
		}
	};

	/**
	 * Returns the datepicker jQuery function to use depending on the
	 * given session unit.
	 * 
	 * @param  {string} unit The session unit.
	 * @return {string}      The name of the jQuery UI Datepicker function to use for the unit,
	 *                       or null if the unit is an unknown unit.
	 */
	function getDatePickerFunction( unit ) {
		switch ( unit ) {
			case 'minutes':
			case 'hours':
				return 'datepicker';
			case 'days':
			case 'weeks':
				return 'multiDatesPicker';
			default:
				return null;
		}
	}

})(jQuery, edd_bk, edd_bk_utils);
