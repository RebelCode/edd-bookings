;(function($, Utils) {

	/**
	 * Class for bookable download.
	 */
	var BookableDownload = (function() {

		function BookableDownloadClass(element) {
			this.element = $(element);
			this.availability = {};
			this.initScope();
			this.getData( this.initElements.bind(this) );
		}

		BookableDownloadClass.prototype.getData = function(callback) {
			$.ajax({
				type: 'POST',
				url: window.ajaxurl,
				data: {
					action: 'get_edd_bk_data',
					post_id: this.postId
				},
				success: function( response, status, jqXHR ) {
					this.data = response;
					this.data.meta.session_length = parseInt(this.data.meta.session_length);
					if (typeof callback !== 'undefined') {
						callback();
					}
				}.bind(this),
				dataType: 'json'
			});
		}

		BookableDownloadClass.prototype.initScope = function() {
			// Element pointers
			if (this.element.parents('.edd_download').length > 0) {
				// Look for EDD containers. Case for multiple downloads in one page
				this.container = this.element.parents('.edd_download');
				this.postId = this.container.attr('id').substr(this.container.attr('id').lastIndexOf('_') + 1);
			} else {
				// Look for article tag. Case for a single download page
				this.container = this.element.parents('article.edd-download');
				this.postId = this.container.attr('id').substr(this.container.attr('id').lastIndexOf('-') + 1);
			}
		}

		BookableDownloadClass.prototype.initElements = function() {
			this.datepickerElement = this.element.find('.edd-bk-datepicker');
			this.datepickerAltField = this.element.find('.edd-bk-datepicker-value');
			this.timepickerElement = this.element.find('.edd-bk-timepicker');
			this.timepickerLoading = this.element.find('.edd-bk-timepicker-loading');
			this.timepickerSelect = this.timepickerElement.find('select[name="edd_bk_time"]');
			this.eddSubmitWrapper = this.element.find('.edd_purchase_submit_wrapper');
			this.noTimesForDateElement = this.element.find('.edd-bk-no-times-for-date');
			this.timepickerDuration = this.element.find('.edd_bk_duration');
			this.datefixElement = this.element.find('.edd-bk-datefix-msg');
			this.invalidDateElement = this.element.find('.edd-bk-invalid-date-msg');
			this.priceElement = this.element.find('p.edd-bk-price span');

			// Init the datepicker with availability for the current month (default shown month on datepicker)
			var today = new Date();
			this.getMonthAvailability( today.getFullYear(), today.getMonth() + 1, this.initDatePicker.bind(this));

			// Hide EDD quantity field
			this.element.find('div.edd_download_quantity_wrapper').hide();
			// Change EDD cart button text
			this.element.find('body.single-download .edd-add-to-cart-label').text("Purchase");
			// Hide the submit button
			this.eddSubmitWrapper.hide();

			// If the duration type is variable, run the updateCost function whnever the number of sessions is modified
			if ( this.data.meta.session_type == 'variable' ) {
				this.timepickerDuration.bind('change', function() {
					var val = parseInt( this.timepickerDuration.val() );
					var min = parseInt( this.timepickerDuration.attr('min') );
					var max = parseInt( this.timepickerDuration.attr('max') );
					this.timepickerDuration.val( Math.max( min, Math.min( max, val ) ) );
					this.updateCost();
				}.bind(this));
			}
			this.updateCost();
		};

		BookableDownloadClass.prototype.getMonthAvailability = function(year, month, callback) {
			var data = {
				action: 'get_download_availability',
				post_id: this.postId
			};
			if (typeof year !== 'undefined') data.year = year;
			if (typeof month !== 'undefined') data.month = month;
			$.ajax({
				type: 'POST',
				url: window.ajaxurl,
				data: data,
				success: function( response, status, jqXHR ) {
					this.availability = response;
					if (typeof callback !== 'undefined') {
						callback.apply(null, []);
					}
				}.bind(this),
				dataType: 'json'
			});
		};

		BookableDownloadClass.prototype.initDatePicker = function(range) {
			// Check if the range has been given. Default to the session duration
			if ( _.isUndefined(range) ) {
				range =	this.data.meta.session_length;
			}
			// Get the session duration unit
			var unit = this.data.meta.session_unit.toLowerCase();
			// Check which datepicker function to use, depending on the unit
			var pickerFn = Utils.getDatePickerFunction( unit );
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
				altField: this.datepickerAltField,
				// Prepares the dates for availability
				beforeShowDay: this.datepickerIsDateAvailable.bind(this),
				// When a date is selected by the user
				onSelect: this.datepickerOnSelectDate.bind(this),
				// When the month of year changes
				onChangeMonthYear: this.datepickerOnChangeMonthYear.bind(this)
			};

			// Apply the datepicker function on the HTML datepicker element
			$.fn[ pickerFn ].apply(this.datepickerElement, [options]);
		};

		/**
		 * Re-initializes the datepicker.
		 */
		BookableDownloadClass.prototype.reInitDatePicker = function() {
			// Get the range
			var range = parseInt( this.timepickerDuration.val() );
			// Re-init the datepicker
			this.initDatePicker(range);
			// Simulate user click on the selected date, to refresh the auto selected range
			this.datepickerElement.data('suppress-click-event', true)
				.find('.ui-datepicker-current-day').first()
					.find('>a').click();
		}

		BookableDownloadClass.prototype.datepickerOnChangeMonthYear = function(year, month, widget) {
			this.datePickerRefresh( year, month );
		};

		BookableDownloadClass.prototype.datePickerRefresh = function(year, month, doAnimation) {
			doAnimation = typeof doAnimation === 'undefined'? true : doAnimation;
			this.datepickerElement.parent().addClass('loading');
			this.getMonthAvailability(year, month, function() {
				if (doAnimation === true) {
					this.datepickerElement.datepicker( 'refresh' )
					.parent()
						.removeClass('loading');
				}
			}.bind(this));
		};

		/**
		 * Checks if a given date is available.
		 * 
		 * @param  {Date} date The date to check, as a JS Date object.
		 * @return array       An array with two element. The first contains the boolean that is true if
		 *                     the date is available, the second is an empty string, used to make this date
		 *                     compatible with jQuery's datepicker beforeShowDay callback.
		 */
		BookableDownloadClass.prototype.datepickerIsDateAvailable = function( date ) {
			// If the date is in the past, return false
			if ( date < Date.now() ) {
				return [false, ''];
			} else {
				return [this.availability[date.getDate()], ''];
			}

		};

		/**
		 * Callback used when a date was selected on the datepicker.
		 * 
		 * @param  {string} dateStr The string that represents the date, in the format mm/dd/yyyy
		 */
		BookableDownloadClass.prototype.datepickerOnSelectDate = function( dateStr ) {
			// If the element has the click-event suppression flag,
			if ( this.datepickerElement.data('suppress-click-event') === true ) {
				// Remove it and return
				this.datepickerElement.data('suppress-click-event', null);
				return;
			}

			// Hide the purchase button and datefix element
			this.eddSubmitWrapper.hide();
			this.datefixElement.hide();
			this.invalidDateElement.hide();

			// parse the date
			var dateParts = dateStr.split('/');
			var date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]);
			var dateValid = this.checkDateForInvalidDatesFix(date);
			if ( !dateValid ) return;

			// Show the loading
			this.timepickerElement.hide();
			this.timepickerLoading.show();

			// Also hide the msg for when no times are available for a date, in case it was
			// previously shown
			this.noTimesForDateElement.hide();
			// Refresh the timepicker via AJAX
			$.ajax({
				type: 'POST',
				url: window.ajaxurl,
				data: {
					action: 'get_times_for_date',
					post_id: this.postId,
					date: dateStr
				},
				success: function( response, status, jqXHR ) {
					if ( ( response instanceof Array ) || ( response instanceof Object ) ) {
						this.timepickerSelect.empty();
						if ( response.length > 0 ) {
							for ( i in response ) {
								var parsed = response[i].split('|');
								var max = parseInt(parsed[1]) * this.data.meta.session_length;
								var seconds = parseInt(parsed[0]);
								var text = moment().startOf('day').add(seconds, 'seconds').format('HH:mm');
								$( document.createElement('option') )
								.text(text)
								.data('val', seconds)
								.data('max', max)
								.appendTo(this.timepickerSelect);
							}
							this.timepickerElement.show();
							this.eddSubmitWrapper.show();
							this.updateCalendarForVariableMultiDates();
						} else {
							if ( this.data.meta.session_unit == 'weeks' || this.data.meta.session_unit == 'days' ) {
								this.timepickerElement.show();
								this.eddSubmitWrapper.show();
								this.updateCalendarForVariableMultiDates();
							} else {
								this.noTimesForDateElement.show();
							}
						}
					}
					this.timepickerLoading.hide();
				}.bind(this),
				dataType: 'json'
			});
		};

		/**
		 * Checks if the given date requires the date fix.
		 * 
		 * @param  {Date}    date The Date object to check
		 * @return {boolean}      True if the date was fixed, false if not.
		 */
		BookableDownloadClass.prototype.checkDateForInvalidDatesFix = function(date) {
			var originalDate = new Date(date.getTime());
			var newDate = new Date(date.getTime());
			if ( this.data.meta.session_unit === 'weeks' || this.data.meta.session_unit === 'days' ) {
				var newDate = this.invalidDayFix(date);
				if ( newDate === null ) {
					if ( Utils.getDatePickerFunction(this.data.meta.session_unit) === 'multiDatesPicker' ) {
						this.datepickerElement.multiDatesPicker('resetDates');
					}
					this.showInvalidDateMessage(originalDate);
					return false;
				}
				if ( originalDate.getTime() !== newDate.getTime() ) {
					this.showDateFixMessage(newDate);
				}
				this.datepickerElement.datepicker('setDate', newDate);
				this.reInitDatePicker();
			}
			return true;
		};

		/**
		 * Performs the date fix for the given date.
		 * 
		 * @param  {Date}      date The date to be fixed.
		 * @return {Date|null}       The fixed date, or null if the given date is invalid and cannot
		 *                           be selected or fixed.
		 */
		BookableDownloadClass.prototype.invalidDayFix = function(date) {
			var days = parseInt(this.timepickerDuration.val());
			if (this.data.meta.session_unit === 'weeks') {
				days *= 7;
			}
			for (var u = 0; u < days; u++) {
				var tempDate = new Date(date.getTime());
				var allAvailable = true;
				for(var i = 1; i < days; i++) {
					tempDate.setDate(tempDate.getDate() + 1);
					var available = this.datepickerIsDateAvailable(tempDate);
					if ( !available[0] ) {
						allAvailable = false;
						break;
					}
				}
				if (allAvailable) return date;
				date.setDate(date.getDate() - 1);
				if ( ! this.datepickerIsDateAvailable(date)[0] ) {
					return null;
				}
			}
			return null;
		};

		/**
		 * Shows the invalid date message.
		 * 
		 * @param  {Date} date The JS date object for the user's selection.
		 */
		BookableDownloadClass.prototype.showInvalidDateMessage = function (date) {
			var date_date = date.getDate();
			var date_month = date.getMonth();
			var dateStr = date_date + Utils.numberOrdinalSuffix(date_date) + ' ' + Utils.ucfirst( Utils.months[date_month] );
			this.invalidDateElement.find('.edd-bk-invalid-date').text( dateStr );
			var duration = parseInt(this.timepickerDuration.val());
			var sessionsStr = Utils.pluralize(this.data.meta.session_unit, duration);
			this.invalidDateElement.find('.edd-bk-invalid-length').text( sessionsStr );
			this.invalidDateElement.show();
		};

		/**
		 * Shows the date fix message.
		 * 
		 * @param  {Date} date The JS date object that was used instead of the user's selection.
		 */
		BookableDownloadClass.prototype.showDateFixMessage = function (date) {
			var date_date = date.getDate();
			var date_month = date.getMonth();
			var dateStr = date_date + Utils.numberOrdinalSuffix(date_date) + ' ' + Utils.ucfirst( Utils.months[date_month] );
			this.datefixElement.find('.edd-bk-datefix-date').text( dateStr );
			var duration = parseInt(this.timepickerDuration.val());
			var sessionsStr = Utils.pluralize(this.data.meta.session_unit, duration);
			this.datefixElement.find('.edd-bk-datefix-length').text( sessionsStr );
			this.datefixElement.show();
		};

		// For variable sessions
		BookableDownloadClass.prototype.updateCalendarForVariableMultiDates = function() {
			if ( this.data.meta.session_type == 'variable' ) {
				// When the time changes, adjust the maximum number of sessions allowed
				this.timepickerSelect.unbind('change').on('change', function() {
					// Get the selected option's max data value
					var maxDuration = parseInt( this.timepickerSelect.find('option:selected').data('max') );
					// Get the field where the user enters the number of sessions, and set the max
					// attribute to the selected option's max data value
					this.timepickerDuration.attr('max', maxDuration);
					// Value entered in the number roller
					var duration = parseInt( this.timepickerDuration.val() );
					// If the value is greater than the max
					if ( duration > maxDuration ) {
						// Set it to the max
						this.timepickerDuration.val( maxDuration );
						// Triger the change event
						this.timepickerDuration.trigger('change');
					}
				}.bind(this));

				if ( this.data.meta.session_unit == 'weeks' || this.data.meta.session_unit == 'days' ) {
					this.timepickerDuration.on('change', function() {
						this.eddSubmitWrapper.hide();
						this.datefixElement.hide();
						this.invalidDateElement.hide();
						var date = this.datepickerElement.datepicker('getDate');
						var valid = this.checkDateForInvalidDatesFix(date);
						if (valid) this.eddSubmitWrapper.show();
					});
				}
			}
		}

		/**
		 * Function that updates the cost of the booking.
		 */
		BookableDownloadClass.prototype.updateCost = function() {
			var text = '';
			if ( this.data.meta.session_type == 'fixed' ) {
				text = parseFloat( this.data.meta.session_cost );
			} else {
				var num_sessions = ( parseInt( this.timepickerDuration.val() ) || 1 ) / this.data.meta.session_length;
				text = parseFloat( this.data.meta.session_cost ) * num_sessions;
			}
			this.priceElement.html( this.data.currency + text );
		}

		// Return class
		return BookableDownloadClass;
	})();

	$(document).ready( function() {
		window.edd_bk = {};
		// Instances array
		window.edd_bk.instances = [];
		// Go through each download and init instance
		$('form.edd_download_purchase_form').each( function() {
			if ($(this).find('.edd-bk-datepicker-container').length > 0) {
				var instance = new BookableDownload(this);
				window.edd_bk.instances[instance.postId] = instance;
			}
		});
	});

})(jQuery, edd_bk_utils);
