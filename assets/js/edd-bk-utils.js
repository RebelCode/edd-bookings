/**
 * The Utils object.
 */
window.edd_bk_utils = {
	// Weekdays and Months - used for string to index conversions
	weekdays: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	months: [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ],
	
	/**
	 * Returns the ordinal suffix for the given number.
	 * 
	 * @param  {number} n The number
	 * @return {string}   The ordinal suffix
	 */
	numberOrdinalSuffix: function( n ) {
		var u = n % 10;
		switch(u) {
			case 1: return (n == 11)? 'th' : 'st';
			case 2: return (n == 12)? 'th' : 'nd';
			case 3: return (n == 13)? 'th' : 'rd';
		}
		return 'th';
	},

	/**
	 * Uppercases the first letter of the string.
	 * 
	 * @param  {string} str The string
	 * @return {string}
	 */
	ucfirst: function( str ) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	},

	/**
	 * Generates a pluralized string using the given string and number.
	 * The resulting is in the form:
	 * n str(s)
	 * 
	 * @param  {string} str The string to optionally pluralize.
	 * @param  {number} n   The number to use to determine if pluralization is requred.
	 * @return {string}     A string in the form: "n str(s)" where "(s)" denotes an option "s" character.
	 */
	pluralize: function( str, n ) {
		var newStr = str.toLowerCase().charAt(str.length - 1) === 's'? str.slice(0, -1) : str;
		if ( n !== 1) newStr += 's';
		return n + ' ' + newStr;
	},

	/**
	 * Returns the date of the week as an integer, from 0-6, for the given day name.
	 * 
	 * @param   {string} str The string for the day of the week.
	 * @return {integer}     An integer, from 0-6 for the day of the week, or -1 if the string is not a weekday.
	 */
	weekdayStrToInt: function( str ) {
		return edd_bk_utils.weekdays.indexOf( str.toLowerCase() );
	},

	/**
	 * Returns the month integer, from 0-11, for the given month name.
	 * 
	 * @param   {string} str The string for the month name
	 * @return {integer}     An integer, from 0-11 for the month number, or -1 if the string is not a month name.
	 */
	monthStrToInt: function( str ) {
		return edd_bk_utils.months.indexOf( str.toLowerCase() );
	},

	/**
	 * Converts the given string into a boolean.
	 * 
	 * @param   {string} str The string to convert. Must be either 'true' or 'false'.
	 * @return {boolean}     Returns true if str is 'true', and false otherwise.
	 */
	strToBool: function( arg ) {
		if ( typeof arg === 'boolean' ) return arg;
		return arg.toLowerCase() === 'true' ? true : false;
	},

	/**
	 * Converts the given string date, recieved from the server (format: mm/dd/yyyy),
	 * into a JavaScript Date Object.
	 * 
	 * @param  {string} sDate The date string in the format mm/dd/yyyy
	 * @return   {Date}       The parsed date object.
	 */
	parseDateFromServer: function( sDate ) {
		var split = sDate.split('/');
		// Months need -1 because they start from 0
		return new Date( split[2], split[0] - 1, split[1] );
	}

};

/**
 * Adds the method 'getWeekNumber' to the JavaScript Date object.
 * @return {integer} The week number, from 1 - 52
 */
if (!Date.prototype.getWeekNumber) {
	Date.prototype.getWeekNumber = function() {
		var d = new Date( +this );
		d.setHours( 0, 0, 0 );
		d.setDate( d.getDate() + 4 - ( d.getDay() /* || 7 */ ) ); // Uncomment '|| 7' to make weeks start from Sunday
		return Math.ceil( ( ( ( d - new Date( d.getFullYear(), 0, 1 ) ) / 8.64e7 ) + 1 ) / 7 );
	};
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
