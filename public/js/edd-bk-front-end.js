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
		var today = new Date();
		// Return false ifthe date has passed already
		if (date < today && date.getDate() < today.getDate()) {
			return [false, ''];
		}
		// Use the fill as default availability
		var available = Utils.strToBool( EDD_BK.meta.availability_fill );
		// For each availability
		for ( i in EDD_BK.meta.availability ) {
			// Get the availability
			var av = EDD_BK.meta.availability[i];
			var range = av.type;
			// The checking function to call, and its args
			var fn = null;
			var args = [];

			// If the range type is a weekday
			// Change the range type (from 'monday', 'tuesday', etc...) to 'weekday'
			// And add the weekday as a function arg
			var weekday = Utils.weekdayStrToInt( range );
			if ( weekday !== -1 ) {	
				range = 'weekday';
				args = [weekday];
			}

			// If the checking function exists
			// Set fn to the checking function in rangeCheckers
			// Add the date and availability to the args, at the beginning
			if ( range in rangeCheckers ) { 
				fn = rangeCheckers[range];
				args = [date, av].concat(args);
			}

			/*
			 * Maybe some other functions can be put here?
			 * If so, change `fn` to point to the function and `args` to an array of args
			 */

			// If the function pointer is not null,
			// Run the function with the args. 'null' for no object context
			// If the checking function returns TRUE, then the date matches the current range, and
			// its availability should be deterined by that range's availability flag.
			if ( fn !== null ) {
				var obeys = fn.apply( null, args );
				if ( obeys ) {
					available = Utils.strToBool( av.available );
				}
			}
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
		$('p#edd-bk-price span').text( text );
	}
	// If the duration type is variable, run the updateCost function whnever the number of sessions is modified
	if ( EDD_BK.meta.duration_type == 'variable' ) {
		$(document).ready(function(){
			$('[name="edd_bk_num_slots"]').on('change', updateCost);
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

	function addTimeArrays(time1, time2) {
		// Add the hours and minutes
		var h = parseInt(time1[0]) + parseInt(time2[0]);
		var m = parseInt(time1[1]) + parseInt(time2[1]);
		// Parse the minutes into a time array (which can result in more hours)
		var newTime = numUnitToTimeArray(m, 'minutes');
		// New time is the added hours + carried hours, and the added minutes
		return [h + newTime[0], newTime[1]];
	}

	function numUnitToTimeArray(num, unit) {
		num = parseInt( num );
		var h = ( (unit == "hours")? num: 0 );
		var m = ( unit == "minutes" )? num % 60 : 0;
		if ( unit == "minutes" ) h += Math.floor( num / 60 );
		return [ h, m ];
	}

	function timeSliceFromUnit(time, unit) {
		var i = unit == 'hours'? 0 : 1;
		return time[i];
	}

	function timeArrayTo(unit, time) {
		if ( unit == 'hours' ) {
			return parseInt( time[0] );
		}
		else return ( parseInt( time[0] ) * 60 ) + parseInt( time[1] );
	}

	function diffTimeArray(time1, time2, format) {
		format = format || 'array';
		var t1 = timeArrayTo('minutes', time1);
		var t2 = timeArrayTo('minutes', time2);
		var diff = t1 - t2;
		if ( format === 'minutes' ) {
			return diff;
		} else if ( format == 'hours' ) {
			return diff / 60;
		} else {
			return numUnitToTimeArray(diff, 'minutes');
		}
	}


	/**
	 * The range checking functions.
	 */
	var rangeCheckers = {
		days: function( date, av ) {
			var day = date.getDay();
			var from = Utils.weekdayStrToInt( av.from );
			var to = Utils.weekdayStrToInt( av.to );
			return day >= from && day <= to;
		},

		weeks: function( date, av ) {
			var week = date.getWeekNumber();
			var from = parseInt( av.from );
			var to = parseInt( av.to );
			return week >= from && week <= to;
		},

		months: function ( date, av ) {
			var month = date.getMonth();
			var from = Utils.monthStrToInt( av.from );
			var to = Utils.monthStrToInt( av.to );
			return month >= from && month <= to;
		},

		custom: function( date, av ){
			var from = Utils.parseDateFromServer( av.from );
			var to = Utils.parseDateFromServer( av.to );
			return date >= from && date <= to;
		},

		all_week: function( date, av ) {
			return Utils.strToBool(av.available) || (av.from.length == 0 && av.to.length == 0);
		},

		/**
		 * Matches a particulat WHOLE weekday.
		 * Only matches, say 10:00 - 17:00 Wednesday, if it is marked as available,
		 * or if the times have been left out (indicating a full day match).
		 */
		weekday: function( date, av, weekday ) {
			return (
				( date.getDay() == weekday ) &&
				( Utils.strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
			);
		},

		/**
		 * Matches any WHOLE weekday.
		 * Only matches, 15:00 - 18:00 on weekdays, if it is marked as available,
		 * or if the times have been left out (indicating full weekdays).
		 */
		weekdays: function( date, av ) {
			return (
				( date.getDay() > 0 && date.getDay() < 6 ) &&
				( Utils.strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
			);
		},

		/**
		 * Matches any WHOLE weekend day.
		 * Only matches, say 12:00 - 15:00 on weekends, if it is marked as available,
		 * or if the times have been left out (indicating a full weekend days).
		 */
		weekend: function( date, av ) {
			return (
				( date.getDay() == 0 || date.getDay() == 6 ) &&
				( Utils.strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
			);
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
