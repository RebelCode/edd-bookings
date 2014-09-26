(function($, EDD_BK){

	if ( typeof EDD_BK === 'undefined' ) {
		var EDD_BK = {
			availability: [],
			fill: true,
		};
	}

	// On document ready
	$(document).ready( function(){

		/**
		 * Initializes the datepicker
		 */
		$('#edd-bk-datepicker').datepicker({
			// Hide the Button Panel
			showButtonPanel: false,

			// Prepares the dates for availability
			beforeShowDay: function( date ) {
				// Use the fill as default availability
				var available = strToBool( EDD_BK.fill );
				// For each availability
				for ( i in EDD_BK.availabilities ) {
					// Get the availability
					var av = EDD_BK.availabilities[i];
					var range = av.type;
					// The checking function to call, and its args
					var fn = null;
					var args = [];

					// If the range type is a weekday
					// Change the range type (from 'monday', 'tuesday', etc...) to 'weekday'
					// And add the weekday as a function arg
					var weekday = weekdayStrToInt( range );
					if ( weekday !== -1 ) {0.5
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
				// Return the [{availability}, {class}]
				return [available, ''];
			}

		}); // End of datepicker initialization

	}); // End of document on ready


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
	 * Converts the given string date, recieved from the server (format: yyyy-mm-dd),
	 * into a JavaScript Date Object.
	 * 
	 * @param  {string} sDate The date string in the format yyyy-mm-dd
	 * @return   {Date}       The parsed date object.
	 */
	function parseDateFromServer( sDate ) {
		var split = sDate.split('-');
		// Months need -1 because they start from 0
		return new Date( split[0], split[1] - 1, split[2] );
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