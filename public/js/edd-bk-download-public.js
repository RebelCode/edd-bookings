(function($, EDD_BK){

	if ( typeof EDD_BK === 'undefined' ) {
		var EDD_BK = {
			availability: []
		};
	} else {
		$('<pre>')
		.text(
			'Got availability localized into script from server!\n\n'
			+ JSON.stringify( EDD_BK.availabilities, null, "\t" )
		)
		.insertAfter( $('#edd-bk-datepicker-container + hr') );
		console.log( EDD_BK );
	}

	// On document ready
	$(document).ready( function(){

		$('#edd-bk-datepicker').datepicker({
			showButtonPanel: false,
			beforeShowDay: function( date ){
				var available = true;
				// ...
				// run available tru the array of fns
				// ...
				return [available, ''];
			}
		});

	});


})(jQuery, edd_bk);