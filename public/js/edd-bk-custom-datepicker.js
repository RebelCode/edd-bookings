function EDD_BK_Datepicker(element, meta) {
	this.element = element;
	this.meta = meta;
}

EDD_BK_Datepicker.prototype = {

	init: function() {

	},

	beforeShowDate: function( date ) {
		var today = new Date();
		// Return false ifthe date has passed already
		if (date < today && date.getDate() < today.getDate()) {
			return [false, ''];
		}
		// Use the fill as default availability
		var available = Utils.strToBool( this.meta.availability_fill );
		// For each availability
		for ( i in this.meta.availability ) {
			// Get the availability
			var av = this.meta.availability[i];
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
	},

	onSelectDate: function( dateStr, inst ) {
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
	}

};

window.edd_bk_datepicker = {

	/**
	 * Returns the datepicker jQuery function to use depending on the
	 * given session unit.
	 * 
	 * @param  {string} unit The session unit.
	 * @return {string}      The name of the jQuery UI Datepicker function to use for the unit,
	 *                       or null if the unit is an unknown unit.
	 */
	datepickerFunctionForUnit: function( unit ) {
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
	},

	/**
	 * The range checking functions.
	 */
	rangeCheckers: {
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
	}
};

