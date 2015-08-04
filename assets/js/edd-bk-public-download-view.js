;(function($, EDD_BK, Utils) {

	// Element pointers
	var datepicker_element = null,
		timepicker_element = null,
		timepicker_loading = null,
		timepicker_timeselect = null,
		edd_submit_wrapper = null,
		no_times_for_date_element = null,
		timepicker_num_session = null,
		datefix_element = null,
		invalid_date_element = null;

	// On document ready
	$(document).ready( function() {
		// Init element pointers
		datepicker_element = $('#edd-bk-datepicker');
		timepicker_element = $('#edd-bk-timepicker');
		timepicker_loading = $('#edd-bk-timepicker-loading');
		timepicker_timeselect = $('#edd-bk-timepicker select[name="edd_bk_time"]');
		edd_submit_wrapper = $('.edd_purchase_submit_wrapper');
		no_times_for_date_element = $('#edd-bk-no-times-for-date');
		timepicker_num_session = $('#edd_bk_num_sessions');
		datefix_element = $('#edd-bk-datefix-msg');
		invalid_date_element = $('#edd-bk-invalid-date-msg');

		EDD_BK.meta.session_length = parseInt(EDD_BK.meta.session_length);

		// Init the datepicker
		initDatePicker();

		// Change EDD cart button text
		$('body.single-download .edd-add-to-cart-label').text("Purchase");
		// Hide the submit button
		edd_submit_wrapper.hide();
	});

	/**
	 * Initializes the datepicker.
	 *
	 * @param range The range param to be handed to multiDatesPicker. Optional.
	 */
	var initDatePicker = function(range) {
		// Check if the range has been given. Default to the session duration
		if ( _.isUndefined(range) ) range =	EDD_BK.meta.session_length;
		// Get the session duration unit
		var unit = EDD_BK.meta.session_unit.toLowerCase();
		// Check which datepicker function to use, depending on the unit
		var pickerFn = getDatePickerFunction( unit );
		// Stop if the datepicker function returned is null
		if ( pickerFn === null ) return;
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
			// When the month of year changes
			onChangeMonthYear: datepickerOnChangeMonthYear
		};

		// Apply the datepicker function on the HTML datepicker element
		$.fn[ pickerFn ].apply( datepicker_element, [options]);
	};

	// Deprecated
	var datePickerRefresh = function(year, month) {
		var data = {
			action: 'get_download_availability',
			post_id: EDD_BK.post_id
		};
		if (typeof year !== 'undefined') data.year = year;
		if (typeof month !== 'undefined') data.month = month;

		datepicker_element.parent().addClass('loading');
		$.ajax({
			type: 'POST',
			url: EDD_BK.ajaxurl,
			data: data,
			success: function( response, status, jqXHR ) {
				EDD_BK.availability = response;
				datepicker_element.datepicker( 'refresh' )
				.parent().removeClass('loading');
			},
			dataType: 'json'
		});
	};

	/**
	 * Checks if a given date is available.
	 * 
	 * @param  {Date} date The date to check, as a JS Date object.
	 * @return array       An array with two element. The first contains the boolean that is true if
	 *                     the date is available, the second is an empty string, used to make this date
	 *                     compatible with jQuery's datepicker beforeShowDay callback.
	 */
	var datepickerIsDateAvailable = function( date ) {
		if ( date < Date.now() ) return [false, ''];
		var year = date.getFullYear();
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var dotw = ( ( date.getDay() + 6 ) % 7 ) + 1;
		var week = date.getWeekNumber();
		var available = Utils.strToBool( EDD_BK.meta.availability.fill );

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

	/**
	 * Re-initializes the datepicker.
	 */
	var reInitDatePicker = function() {
		// Get the range
		var range = parseInt( timepicker_num_session.val() );
		// Re-init the datepicker
		initDatePicker(range);
		// Simulate user click on the selected date, to refresh the auto selected range
		$('#edd-bk-datepicker').data('suppress-click-event', true).find('.ui-datepicker-current-day').first().find('>a').click();
	}

	/**
	 * Shows the date fix message.
	 * 
	 * @param  {Date} date The JS date object that was used instead of the user's selection.
	 */
	var showDateFixMessage = function (date) {
		var date_date = date.getDate();
		var date_month = date.getMonth() + 1;
		var dateStr = date_date + Utils.numberOrdinalSuffix(date_date) + ' ' + Utils.ucfirst( Utils.months[date_month] );
		datefix_element.find('#edd-bk-datefix-date').text( dateStr );
		var num_sessions = parseInt(timepicker_num_session.val()) * EDD_BK.meta.session_length;
		var sessionsStr = Utils.pluralize(EDD_BK.meta.session_unit, num_sessions);
		datefix_element.find('#edd-bk-datefix-length').text( sessionsStr );
		datefix_element.show();
	};

	/**
	 * Shows the invalid date message.
	 * 
	 * @param  {Date} date The JS date object for the user's selection.
	 */
	var showInvalidDateMessage = function (date) {
		var date_date = date.getDate();
		var date_month = date.getMonth() + 1;
		var dateStr = date_date + Utils.numberOrdinalSuffix(date_date) + ' ' + Utils.ucfirst( Utils.months[date_month] );
		invalid_date_element.find('#edd-bk-invalid-date').text( dateStr );
		var num_sessions = parseInt(timepicker_num_session.val()) * EDD_BK.meta.session_length;
		var sessionsStr = Utils.pluralize(EDD_BK.meta.session_unit, num_sessions);
		invalid_date_element.find('#edd-bk-invalid-length').text( sessionsStr );
		invalid_date_element.show();
	};

	/**
	 * Performs the date fix for the given date.
	 * 
	 * @param  {Date}      date The date to be fixed.
	 * @return {Date|null}       The fixed date, or null if the given date is invalid and cannot
	 *                           be selected or fixed.
	 */
	var invalidDayFix = function(date) {
		var days = parseInt(timepicker_num_session.val());
		if (EDD_BK.meta.session_unit === 'weeks') {
			days *= 7;
		}
		for (var u = 0; u < days; u++) {
			var tempDate = new Date(date.getTime());
			var allAvailable = true;
			for(var i = 1; i < days; i++) {
				tempDate.setDate(tempDate.getDate() + 1);
				var available = datepickerIsDateAvailable(tempDate);
				if ( !available[0] ) {
					allAvailable = false;
					break;
				}
			}
			if (allAvailable) return date;
			date.setDate(date.getDate() - 1);
			if ( ! datepickerIsDateAvailable(date)[0] ) {
				return null;
			}
		}
		return null;
	};

	/**
	 * Checks if the given date requires the date fix.
	 * 
	 * @param  {Date}    date The Date object to check
	 * @return {boolean}      True if the date was fixed, false if not.
	 */
	var checkDateForInvalidDatesFix = function(date) {
		var originalDate = new Date(date.getTime());
		var newDate = new Date(date.getTime());
		if ( EDD_BK.meta.session_unit === 'weeks' || EDD_BK.meta.session_unit === 'days' ) {
			var newDate = invalidDayFix(date);
			if ( newDate === null ) {
				if ( getDatePickerFunction(EDD_BK.meta.session_unit) === 'multiDatesPicker' ) {
					datepicker_element.multiDatesPicker('resetDates');
				}
				showInvalidDateMessage(originalDate);
				return false;
			}
			if ( originalDate.getTime() !== newDate.getTime() ) showDateFixMessage(newDate);
			datepicker_element.datepicker('setDate', newDate);
			reInitDatePicker();
		}
		return true;
	};


	/**
	 * Callback used when a date was selected on the datepicker.
	 * 
	 * @param  {string} dateStr The string that represents the date, in the format mm/dd/yyyy
	 */
	var datepickerOnSelectDate = function( dateStr ) {
		// If the element has the click-event suppression flag,
		if ( datepicker_element.data('suppress-click-event') === true ) {
			// Remove it and return
			datepicker_element.data('suppress-click-event', null);
			return;
		}
		// Hide the purchase button and datefix element
		edd_submit_wrapper.hide();
		datefix_element.hide();
		invalid_date_element.hide();

		// parse the date
		var dateParts = dateStr.split('/');
		var date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]);
		var dateValid = checkDateForInvalidDatesFix(date);
		if ( !dateValid ) return;

		// Show the loading
		timepicker_element.hide();
		timepicker_loading.show();

		// Also hide the msg for when no times are available for a date, in case it was
		// previously shown
		no_times_for_date_element.hide();
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
				if ( ( response instanceof Array ) || ( response instanceof Object ) ) {
					timepicker_timeselect.empty();
					if ( response.length > 0 ) {
						for ( i in response ) {
							var parsed = response[i].split('|');
							var max = parseInt(parsed[1]) * EDD_BK.meta.session_length;
							var rpi = parseInt( parsed[0] );
							var hrs = parseInt( rpi / 3600 );
							var mins = ((rpi / 3600) % hrs) * 60;
							var text = ('0' + hrs).slice(-2) + ":" + ('0' + mins).slice(-2);
							$( document.createElement('option') )
							.text(text)
							.data('val', rpi)
							.data('max', max)
							.appendTo(timepicker_timeselect);
						}
						timepicker_element.show();
						edd_submit_wrapper.show();
						updateCalendarForVariableMultiDates();
					} else {
						if ( EDD_BK.meta.session_unit == 'weeks' || EDD_BK.meta.session_unit == 'days' ) {
							timepicker_element.show();
							edd_submit_wrapper.show();
							updateCalendarForVariableMultiDates();
						} else {
							no_times_for_date_element.show();
						}
					}
				}
				timepicker_loading.hide();
			},
			dataType: 'json'
		});
	};
	
	var datepickerOnChangeMonthYear = function(year, month, widget) {
		datePickerRefresh( year, month );
	};

	// If the duration type is variable, run the updateCost function whnever the number of sessions is modified
	if ( EDD_BK.meta.session_type == 'variable' ) {
		$(document).ready(function(){
			timepicker_num_session.bind('change', function() {
				var val = parseInt( $(this).val() );
				var min = parseInt( $(this).attr('min') );
				var max = parseInt( $(this).attr('max') );
				$(this).val( Math.max( min, Math.min( max, val ) ) );
				updateCost();
			});
		});
	}

	/**
	 * Function that updates the cost of the booking.
	 */
	function updateCost() {
		var text = '';
		if ( EDD_BK.meta.session_type == 'fixed' ) {
			text = parseFloat( EDD_BK.meta.session_cost );
		} else {
			var num_sessions = ( parseInt( timepicker_num_session.val() ) || 1 ) / parseInt( EDD_BK.meta.session_length );
			text = parseFloat( EDD_BK.meta.session_cost ) * num_sessions;
		}
		$('p#edd-bk-price span').text( EDD_BK.currency + text );
	}
	// Run the function once on load
	$(window).load(updateCost);


	// For variable sessions
	function updateCalendarForVariableMultiDates() {
		if ( EDD_BK.meta.session_type == 'variable' ) {
			// When the time changes, adjust the maximum number of sessions allowed
			timepicker_timeselect.unbind('change').on('change', function() {
				// Get the selected option's max data value
				var max_sessions = parseInt( $(this).find('option:selected').data('max') );
				// Get the field where the user enters the number of sessions, and set the max
				// attribute to the selected option's max data value
				timepicker_num_session.attr('max', max_sessions);
				// Value entered in the number roller
				var num_sessions = parseInt( timepicker_num_session.val() );
				// If the value is greater than the max
				if ( num_sessions > max_sessions ) {
					// Set it to the max
					timepicker_num_session.val( max_sessions );
					// Triger the change event
					timepicker_num_session.trigger('change');
				}
			});

			if ( EDD_BK.meta.session_unit == 'weeks' || EDD_BK.meta.session_unit == 'days' ) {
				timepicker_num_session.on('change', function() {
					edd_submit_wrapper.hide();
					datefix_element.hide();
					invalid_date_element.hide();
					var date = datepicker_element.datepicker('getDate');
					var valid = checkDateForInvalidDatesFix(date);
					if (valid) edd_submit_wrapper.show();
				});
			}
		}
	}

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
