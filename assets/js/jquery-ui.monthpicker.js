/*
 * MonthPicker v1.0
 * 
 * Copyright 2015, Miguel Muscat <miguelmuscat93@gmail.com>
 * Licensed under GPLv2.
 */
(function( $ ) {

	$.extend( $.ui.multiDatesPicker, {
		monthPicker: {
			version: "1.0"
		}
	});

	$.fn.monthPicker = function( method ) {
		var mp_arguments = arguments;
		var ret = this;
		var today_date = new Date();
		var day_zero = new Date(0);
		var mp_events = {};

		var methods = {
			init : function( options ) {
				$(this).multiDatesPicker( 'init', options );
			}
		}

		if(methods[method]) {
			var exec_result = methods[method].apply(this, Array.prototype.slice.call(mp_arguments, 1));
			// check method
			return exec_result;
		} else if( typeof method === 'object' || ! method ) {
			return methods.init.apply(this, mp_arguments);
		} else {
			$.error('Method ' +  method + ' does not exist on jQuery.multiDatesPicker');
		}
		return false;
	}

})( jQuery );