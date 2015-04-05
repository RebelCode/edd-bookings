(function($, EDD_BK) {

	/**
	 * Initializes the datepicker.
	 *
	 * @oaram range The range param to be handed to multiDatesPicker. Optional.
	 */
	var initDatePicker = function(range) {
		// Check which datepicker function to use, depending on the unit
		var pickerFn = getDatePickerFunction( unit );
		if ( pickerFn === null ) return;

		// Check if the range has been given. Default to the session duration
		if (typeof range === 'undefined') {
			range =	EDD_BK.meta.slot_duration;
		}

		// Get the session duration unit
		var unit = EDD_BK.meta.slot_duration_unit.toLowerCase();
		if ( unit === 'weeks' ) {
			range *= 7;
		}

		// Apply the datepicker function on the HTML datepicker element
		$.fn[ pickerFn ].apply( $('#edd-bk-datepicker'), [{
			// Hide the Button Panel
			showButtonPanel: false,
			// Options for multiDatePicker. These are ignored by the vanilla jQuery UI datepicker
			mode: 'daysRange',
			autoselectRange: [0, range],
			adjustRangeToDisabled: true,
			altField: '#edd-bk-datepicker-value',

			// Prepares the dates for availability
			beforeShowDay: function( date ) {
				var today = new Date();
				if (date < today && date.getDate() < today.getDate()) {
					return [false, ''];
				}
				// Use the fill as default availability
				var available = strToBool( EDD_BK.meta.availability_fill );
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
					var weekday = weekdayStrToInt( range );
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
							available = strToBool( av.available );
						}
					}
				}
				return [available, ''];
			}, // End of datepicker beforeShowDay

			// When a date is selected by the user
			onSelect: function( dateStr, inst ) {
				// If the element has the click-event suppression flag,
				if ( $('#edd-bk-datepicker').data('suppress-click-event') === true ) {
					// Remove it and return
					$('#edd-bk-datepicker').data('suppress-click-event', null);
					return;
				}
				// Show the loading and hide the timepicker
				$( '#edd-bk-timepicker-loading' ).show();
				$( '#edd-bk-timepicker' ).hide();
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
						var select = $('#edd-bk-timepicker select[name="edd_bk_time"]');
						select.empty();
						for ( i in response ) {
							$('<option>').text( response[i] ).appendTo( select );
						}
						$( '#edd-bk-timepicker-loading' ).hide();
						$( '#edd-bk-timepicker' ).show();
						$( '.edd_purchase_submit_wrapper' ).show();
						updateCalendarForVariableMultiDates();
					},
					dataType: 'json'
				});
			},

		}]); // End of datepicker initialization

	}

	// On document ready
	$(document).ready( function(){
		initDatePicker();
		$('.edd-bk-datepicker-refresh').click( function() {
			$('#edd-bk-datepicker').parent().addClass('loading');
			$.ajax({
				type: 'POST',
				url: EDD_BK.ajaxurl,
				data: {
					action: 'get_download_availability',
					post_id: EDD_BK.post_id
				},
				success: function( response, status, jqXHR ) {
					EDD_BK.meta.availability = response;
					$('#edd-bk-datepicker').datepicker( 'refresh' );
					$('#edd-bk-datepicker').parent().removeClass('loading');
				},
				dataType: 'json'
			});
		});

		$('body.single-download .edd-add-to-cart-label').text("Purchase");
		$('.edd_purchase_submit_wrapper').hide();

	}); // End of document on ready


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
			$('select[name="edd_bk_time"]').unbind('change').on('change', function() {
				var unit = EDD_BK.meta.slot_duration_unit;
				// The last option in this dropdown
				var last_option = $(this).find('option:last-child');
				// The selected option
				var selected_option = $(this).find('option:selected');
				// The num slots number roller
				var num_slots_input = $('input[name="edd_bk_num_slots"]');

				// Calculate the final boundary time (this is the time entry not shown in the dropdown)
				var session_dur = numUnitToTimeArray( EDD_BK.meta.slot_duration, unit );
				var final_time = addTimeArrays( last_option.text().split(':'), session_dur );

				// Selected time, as a time array
				var selected_time = selected_option.text().split(':');

				// Calculate the difference between the boundary time and the selected time
				var diff = diffTimeArray(final_time, selected_time, unit);

				// Get the actual max attribute of the number roller
				var actual_max = parseInt( num_slots_input.data('actual-max') );
				// The new maximum is the smaller between the actual-max attr and the calculated diff
				var new_max = actual_max < diff ? actual_max : diff;
				num_slots_input.attr( 'max', new_max );
				
				// Value entered in the number roller
				var num_sessions = parseInt( num_slots_input.val() );
				// The max sessions allowed in the number roller
				var num_sessions_max = parseInt( num_slots_input.attr('max') );
				// If the value is greater than the max
				if ( num_sessions > num_sessions_max ) {
					// Set it to the max
					num_slots_input.val( num_sessions_max );
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
			var from = weekdayStrToInt( av.from );
			var to = weekdayStrToInt( av.to );
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
			var from = monthStrToInt( av.from );
			var to = monthStrToInt( av.to );
			return month >= from && month <= to;
		},

		custom: function( date, av ){
			var from = parseDateFromServer( av.from );
			var to = parseDateFromServer( av.to );
			return date >= from && date <= to;
		},

		allweek: function( date, av ) {
			return strToBool(av.available) || (av.from.length == 0 && av.to.length == 0);
		},

		/**
		 * Matches a particulat WHOLE weekday.
		 * Only matches, say 10:00 - 17:00 Wednesday, if it is marked as available,
		 * or if the times have been left out (indicating a full day match).
		 */
		weekday: function( date, av, weekday ) {
			return (
				( date.getDay() == weekday ) &&
				( strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
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
				( strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
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
				( strToBool(av.available) || (av.from.length == 0 && av.to.length == 0) )
			);
		}
	};


	var timeCheckers = {

	};


	function getDatePickerFunction( unit ) {
		switch ( unit ) {
			case 'minutes':
			case 'hours':
				return 'datepicker';
			case 'days':
			case 'weeks':
				return 'multiDatesPicker';
			case 'months':
				return 'monthPicker';
			default:
				return null;
		}
	}


	/**
	 * .--------------------------------------------------
	 * |  Utility Functions
	 * '--------------------------------------------------
	 */

	// Weekdays and Months - used for string to index conversions
	var weekdays = [ "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" ];
	var months = [ "january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december" ];
	
	/**
	 * Adds the method 'getWeekNumber' to the JavaScript Date object.
	 * @return {integer} The week number, from 1 - 52
	 */
	Date.prototype.getWeekNumber = function() {
		var d = new Date( +this );
		d.setHours( 0, 0, 0 );
		d.setDate( d.getDate() + 4 - ( d.getDay() /* || 7 */ ) ); // Uncomment '|| 7' to make weeks start from Sunday
		return Math.ceil( ( ( ( d - new Date( d.getFullYear(), 0, 1 ) ) / 8.64e7 ) + 1 ) / 7 );
	};

	/**
	 * Returns the date of the week as an integer, from 0-6, for the given day name.
	 * 
	 * @param   {string} str The string for the day of the week.
	 * @return {integer}     An integer, from 0-6 for the day of the week, or -1 if the string is not a weekday.
	 */
	function weekdayStrToInt( str ) {
		return weekdays.indexOf( str.toLowerCase() );
	}

	/**
	 * Returns the month integer, from 0-11, for the given month name.
	 * 
	 * @param   {string} str The string for the month name
	 * @return {integer}     An integer, from 0-11 for the month number, or -1 if the string is not a month name.
	 */
	function monthStrToInt( str ) {
		return months.indexOf( str.toLowerCase() );
	}

	/**
	 * Converts the given string into a boolean.
	 * 
	 * @param   {string} str The string to convert. Must be either 'true' or 'false'.
	 * @return {boolean}     Returns true if str is 'true', and false otherwise.
	 */
	function strToBool( str ) {
		return str.toLowerCase() === 'true' ? true : false;
	}

	/**
	 * Converts the given string date, recieved from the server (format: mm/dd/yyyy),
	 * into a JavaScript Date Object.
	 * 
	 * @param  {string} sDate The date string in the format mm/dd/yyyy
	 * @return   {Date}       The parsed date object.
	 */
	function parseDateFromServer( sDate ) {
		var split = sDate.split('/');
		// Months need -1 because they start from 0
		return new Date( split[2], split[0] - 1, split[1] );
	}


	/**
	 * Array.indexOf Polyfill - for browsers that do not have the indexOf method for Array types.
	 * 
	 * Production steps of ECMA-262, Edition 5, 15.4.4.14
	 * Reference: http://es5.github.io/#x15.4.4.14
	 */
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function(searchElement, fromIndex) {
			var k;

			// 1. Let O be the result of calling ToObject passing
			//    the this value as the argument.
			if (this == null) {
				throw new TypeError('"this" is null or not defined');
			}

			var O = Object(this);

			// 2. Let lenValue be the result of calling the Get
			//    internal method of O with the argument "length".
			// 3. Let len be ToUint32(lenValue).
			var len = O.length >>> 0;

			// 4. If len is 0, return -1.
			if (len === 0) {
				return -1;
			}

			// 5. If argument fromIndex was passed let n be
			//    ToInteger(fromIndex); else let n be 0.
			var n = +fromIndex || 0;

			if (Math.abs(n) === Infinity) {
				n = 0;
			}

			// 6. If n >= len, return -1.
			if (n >= len) {
				return -1;
			}

			// 7. If n >= 0, then Let k be n.
			// 8. Else, n<0, Let k be len - abs(n).
			//    If k is less than 0, then let k be 0.
			k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

			// 9. Repeat, while k < len
			while (k < len) {
				var kValue;
				// a. Let Pk be ToString(k).
				//   This is implicit for LHS operands of the in operator
				// b. Let kPresent be the result of calling the
				//    HasProperty internal method of O with argument Pk.
				//   This step can be combined with c
				// c. If kPresent is true, then
				//    i.  Let elementK be the result of calling the Get
				//        internal method of O with the argument ToString(k).
				//   ii.  Let same be the result of applying the
				//        Strict Equality Comparison Algorithm to
				//        searchElement and elementK.
				//  iii.  If same is true, return k.
				if (k in O && O[k] === searchElement) {
					return k;
				}
				k++;
			}
			return -1;
		};
	}

})(jQuery, edd_bk);
