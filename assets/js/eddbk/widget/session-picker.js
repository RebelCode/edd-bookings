/* global EddBk, top */

;(function ($, undefined) {

    EddBk.newClass('EddBk.Widget.SessionPicker', EddBk.Widget, {
        /**
         * Constructor.
         *
         * @param {$|Element} element
         * @param {object} options
         */
        init: function (element, options) {
            this._super(element, 'Widget.SessionPicker');
            this.addData($.extend(this.getDefaultOptions(), options));
            this.widgets = {};
            this.widgetsLoaded = 0;
            this.localTzOffset = (new Date()).getTimezoneOffset() * -60;
        },
        /**
         * Default options.
         *
         * @returns {object}
         */
        getDefaultOptions: function() {
            return {
                availability: new EddBk.Availability.Registry(),
                unit: EddBk.Utils.Units.hours,
                sessionLength: 3600,
                minSessions: 1,
                maxSessions: 1,
                stepSessions: 1,
                sessionCost: 0,
                currencySymbol: '$'
            };
        },

        /**
         * Triggered when the widget content has been loaded from AJAX.
         */
        onContentLoaded: function() {
            this.initElements();
            this.initEvents();
        },

        /**
         * Initializes the elements and sets the pointers in data
         */
        initElements: function() {
            this.l.addClass('edd-bk-session-picker');
            this.widgetsLoaded = 0;
            this.widgets = {
                datePicker: new EddBk.Widget.DatePicker(this.find('.edd-bk-date-picker-widget'), {}, this.getDatePickerSelectRangeMultiplier()),
                timePicker: new EddBk.Widget.TimePicker(this.find('.edd-bk-time-picker-widget')),
                durationPicker: new EddBk.Widget.DurationPicker(this.find('.edd-bk-duration-picker-widget'))
            };
            this.sessionOptionsElem = this.find('.edd-bk-session-options');
            this.priceElem = this.find('.edd-bk-price');

            this.msgs = this.find('.edd-bk-session-picker-msgs');
            this.dateErrorElem = this.msgs.find('.edd-bk-session-picker-date-error');
            this.dateErrorElemDate = this.dateErrorElem.find('.edd-bk-invalid-date');
            this.dateErrorElemNumSessions = this.dateErrorElem.find('.edd-bk-invalid-num-sessions');
        },
        /**
         * Initializes the events.
         */
        initEvents: function() {
            // The load order of the widgets
            this.widgetLoadOrder = [
                this.getDatePicker(),
                this.getTimePicker(),
                this.getDurationPicker()
            ];
            // Loads the child widgets
            this.loadChildWidgets(this.widgetLoadOrder, function() {
                this.onLoaded();
                this.trigger('loaded');
            }.bind(this));

            this.getDatePicker().on('before_show_day', this.isDateAvailable.bind(this));
            this.getDatePicker().on('date_selected', this.onDateSelected.bind(this));
            this.getDatePicker().on('change_month_year', this.onChangeMonthYear.bind(this));

            this.getTimePicker().on('change', this.onTimeChange.bind(this));

            this.getDurationPicker().on('input', this.onDurationChange.bind(this));
        },
        /**
         * Loads a list of child widgets recursively.
         *
         * @param {Array} widgets The widget instances.
         * @param {Function} callback The callback to call when all widgets have been loaded.
         */
        loadChildWidgets: function(widgets, callback) {
            if (widgets.length) {
                // Shift next widget from queue
                var widget = widgets.shift();
                // Load widget - setting the callback to recurse
                widget.loadContent(function() {
                    this.loadChildWidgets(widgets, callback);
                }.bind(this));
            } else if (callback) {
                callback();
            }
        },
        /**
         * Triggered when all child widgets have been loaded.
         */
        onLoaded: function () {
            this.update();
        },

        /**
         * Updates the widget and all child widgets.
         */
        update: function() {
            this.updateDatePicker();
            this.updateTimePicker();
            this.updateDurationPicker();
            this.updatePrice();

            this.trigger('update');
        },

        /**
         * Updates the datepicker.
         */
        updateDatePicker: function() {
            this.getDatePicker().update();
        },
        /**
         * Updates the time picker.
         */
        updateTimePicker: function() {
            var timeUnit = EddBk.Utils.isTimeUnit(this.getData('unit'));
            // If using a time unit, update the timepicker
            if (timeUnit) this.getTimePicker().update();
            // Show if using a time unit, hide otherwise
            this.getTimePicker().l.toggle(timeUnit);
        },
        /**
         * Updates the duration picker.
         */
        updateDurationPicker: function() {
            this.getDurationPicker().addData({
                unit: this.getData('unit'),
                min: this.getData('minSessions'),
                max: this.calculateMaxDuration(),
                step: this.getData('stepSessions')
            });
            this.getDurationPicker().update();
        },

        /**
         * Updates the price element.
         */
        updatePrice: function() {
            var currencySymbol = this.getData('currencySymbol'),
                price = this.calculatePrice(),
                text = currencySymbol + price;
            this.getPriceElem().find('span').html(text);
        },

        /**
         * Checks if a date is available.
         *
         * @param {Date} date The date object.
         * @returns {boolean}
         */
        isDateAvailable: function(e, date) {
            return this.getAvailability().hasSessions(date);
        },

        /**
         * @override
         * Triggered on date selection. Updates the timepicker with the sessions for the selected date.
         */
        onDateSelected: function(e, date) {
            // Get the date sessions and forward them to the time picker
            var sessions = this.getAvailability().getSessions(date);
            this.getTimePicker().setData('times', sessions);
            // Update all three widgets
            this.update();
        },

        /**
         * Triggered when the time picker's selected time has changed.
         */
        onTimeChange: function() {
            this.updateDurationPicker();
            this.updatePrice();
        },

        /**
         * Triggered when the duration picker's duration value has changed.
         */
        onDurationChange: function() {
            this.updatePrice();
            this.updateDatePickerSelectRange();
        },

        /**
         * Updates the datepicker's day selection range (number of days highlighted after the selected date)
         */
        updateDatePickerSelectRange: function() {
            var unit = this.getData('unit');
            if (!EddBk.Utils.isTimeUnit(unit)) {
                var duration = this.getDurationPicker().getDuration(),
                    mult = this.getDatePickerSelectRangeMultiplier();
                this.getDatePicker()
                    .setSelectRange(duration * mult)
                    .update()
                    .simulateClick();
            }
        },

        /**
         * Gets the selection range multiplier.
         *
         * @returns {Number} 7 for week unit, 1 for day unit
         */
        getDatePickerSelectRangeMultiplier: function() {
            var unit = this.getData('unit');
            return (unit === EddBk.Utils.Units.weeks)? 7 : 1;
        },

        /**
         * Calculates the maximum duration for the duration picker.
         *
         * @return {integer}
         */
        calculateMaxDuration: function() {
            var unit = this.getData('unit');

            return EddBk.Utils.isTimeUnit(unit)
                ? this.calculateMaxTimeDuration()
                : this.calculateMaxDayDuration();
        },

        /**
         * Calculates the maximum duration allowed to be entered in the duration picker, depending on which time is
         * selected in the time picker.
         *
         * @return {integer}
         */
        calculateMaxTimeDuration: function() {
            var sessionLength = parseInt(this.getData('sessionLength')),
                selected = this.getTimePicker().getSelectedItem(),
                current = parseInt(this.getTimePicker().getSelectedValue()),
                max = parseInt(this.getData('maxSessions')),
                maxCalculated = 1,
                next = selected;
            // Iterate while siblings exist
            while (next.next().length !== 0 && maxCalculated < max) {
                // Get next option element
                var next = next.next();
                // Get it's value
                var nextValue = parseInt(next.val());
                // Add a session's length to the current timestamp
                current += sessionLength;
                // Calulcate difference, to see if the two option's timestamps touch
                var diff = current - nextValue;
                if (diff === 0) {
                    maxCalculated++;
                } else {
                    break;
                }
            };
            return maxCalculated;
        },

        /**
         * Calculates the maximum duration allowed to be entered in the duration picker, depending on how many days
         * proceed the current selected date are available.
         *
         * @return {integer}
         */
        calculateMaxDayDuration: function() {
            var step = this.getData('stepSessions') * this.getDatePickerSelectRangeMultiplier(),
                minSessions = this.getData('minSessions'),
                minNumDays = minSessions * step,
                maxSessions = this.getData('maxSessions'),
                maxNumDays = maxSessions * step,
                date = this.getDatePicker().getSelectedDate();
            // Don't continue if no selected date
            if (date === null) {
                return minSessions;
            }
            // Get the number of available days after hte currently selected one
            var numDays = this.getNumDaysAfter(date, maxNumDays) + 1, // plus 1: this date and the number of dates after it
                numSessions = Math.floor(numDays / step);
            // If the number of day sessions is less than the minimum
            if (numSessions < minSessions) {
                // Move the date back to make it fit
                var newDate = this.moveDateToFit(date, minNumDays - 1, maxNumDays);
                // If the new date did not change or is null, then it could not fit.
                if (newDate === date || newDate === null) {
                    // Unselect the date
                    this.getDatePicker().setSelectedDate(null);
                    // Show the date error message
                    this.setDateError(date);
                    this.toggleDateError(true);
                    // Hide the session options
                    this.toggleSessionOptions(false);

                    return 0;
                } else {
                    // Set the new date
                    this.getDatePicker().setSelectedDate(newDate, true);
                    numSessions = minSessions;
                }
            }
            // Hide any errors previously shown
            this.toggleDateError(false);
            // Show the time picker, duration picker and price
            this.toggleSessionOptions(true);

            return numSessions;
        },

        /**
         * Gets the number of available days after the given date.
         *
         * @param {Date} startDate The date.
         * @param {integer} max The maximum number of days to check.
         * @returns {Number} The number of days, not including the one given, that are available after the given date.
         */
        getNumDaysAfter: function(startDate, max) {
            var date = startDate,
                numDays = 0;
            while (date !== null && this.isDateAvailable(null, date) && numDays < max) {
                date = EddBk.Utils.tomorrow(date);
                numDays++;
            }
            return numDays - 1;
        },

        /**
         * Moves a given date such that it fits the availability given the minimum number of days that must proceed it.
         *
         * @param {Date} dateToFix The date to fix.
         * @param {integer} minDays The minimum number of days that must be available after the given date. Must be > 1.
         * @param {integer} maxDays The "lookahead" number of days.
         * @returns {Date} The moved date - or the same date if unmoved. Null if the date could not be moved to fit.
         */
        moveDateToFit: function(dateToFix, minDays, maxDays) {
            var date = dateToFix;
            do {
                // Move date back 1 day
                date = new Date(date.getTime() - EddBk.Utils.UnitLengths.days * 1000);
                // If date not available, return
                if (!this.isDateAvailable(null, date)) {
                    return null;
                }
            } while (date !== null && this.getNumDaysAfter(date, maxDays) < minDays);

            return date;
        },

        /**
         * Calculates the price for the currently selected session.
         *
         * @returns {float}
         */
        calculatePrice: function() {
            var sessionCost = this.getData('sessionCost'),
                numSessions = this.getDurationPicker().getNumSessions(),
                cost = sessionCost * numSessions;
            return cost;
        },

        /**
         * Toggles the visibility of the session options container.
         *
         * @param {boolean} toggle
         */
        toggleSessionOptions: function(toggle) {
            var divs = this.sessionOptionsElem.find('> div'),
                timeOnlyDivs = divs.filter('.edd-bk-if-time-unit'),
                isTimeUnit = EddBk.Utils.isTimeUnit(this.getData('unit'));

            if (toggle) {
                this.sessionOptionsElem.find('> div').width(this.getWidgetWidth());
            }

            timeOnlyDivs.toggle(isTimeUnit);
            this.sessionOptionsElem.toggle(toggle);
        },

        /**
         * Sets the content of the date error message.
         *
         * @param {Date} date
         * @returns {EddBk.Widget.SessionPicker}
         */
        setDateError: function(date) {
            var dayOfMonth = date.getDate(),
                month = date.getMonth(),
                dateStr = dayOfMonth + EddBk.Utils.ordSuffix(dayOfMonth) + ' ' + EddBk.Utils.months[month],
                unit = this.getData('unit'),
                minSessions = this.getData('minSessions') * this.getData('stepSessions'),
                numSessionsStr = EddBk.Utils.pluralize(unit, minSessions);

            this.dateErrorElemDate.text(dateStr);
            this.dateErrorElemNumSessions.text(numSessionsStr);

            return this;
        },

        /**
         * Toggles the date error message.
         *
         * @param {boolean} toggle
         * @returns {EddBk.Widget.SessionPicker}
         */
        toggleDateError: function(toggle) {
            if (toggle) {
                this.dateErrorElem.width(this.getWidgetWidth());
            }
            this.dateErrorElem.toggle(toggle);

            return this;
        },

        /**
         * Gets the width of this widget.
         *
         * @returns {integer}
         */
        getWidgetWidth: function() {
            return this.getDatePicker().find('.edd-bk-datepicker-skin').outerWidth();
        },

        /**
         * @override
         */
        onChangeMonthYear: function(e, year, month) {},

        /**
         * Gets the widgets.
         *
         * @returns {Array}
         */
        getWidgets: function() {
            return this.widgets;
        },
        /**
         * Gets the date picker widget instance.
         *
         * @returns {EddBk.Widget.DatePicker}
         */
        getDatePicker: function() {
            return this.getWidgets().datePicker;
        },
        /**
         * Gets the timer picker widget instance.
         *
         * @returns {EddBk.Widget.TimePicker}
         */
        getTimePicker: function() {
            return this.getWidgets().timePicker;
        },
        /**
         * Gets the duration picker widget instance.
         *
         * @returns {EddBk.Widget.DurationPicker}
         */
        getDurationPicker: function() {
            return this.getWidgets().durationPicker;
        },

        /**
         * Gets the price element.
         *
         * @returns {jQuery}
         */
        getPriceElem: function() {
            return this.priceElem;
        },

        /**
         * Gets the availability controller.
         *
         * @returns {EddBk.Availability}
         */
        getAvailability: function() {
            return this.getData('availability');
        },

        /**
         * Gets the selected session.
         *
         * The returned object will be in the form:
         * {
         *   start: <the start UTC timestamp>
         *   duration: <the duration in seconds>
         *   numUnit: <the number of units (minutes, hours, days, weeks)>
         *   numSessions: <the number of sessions>
         * }
         *
         * @returns {Object} The session object.
         */
        getSelectedSession: function() {
            // Get the selected date timestamp
            var date = this.getDatePicker().getSelectedTimestamp()
            // Stop if no date is selected
            if (date === null) {
                return null;
            }
            var isTimeUnit = EddBk.Utils.isTimeUnit(this.getData('unit')),
                time = (isTimeUnit)
                    ? this.getTimePicker().getSelectedValue()
                    : null,
                numUnit = this.getDurationPicker().getDuration(),
                numSessions = this.getDurationPicker().getNumSessions(),
                duration = numSessions * this.getData('sessionLength');

            // Return the session
            return {
                start: (isTimeUnit)? time : date,
                duration: duration,
                numUnit: numUnit,
                numSessions: numSessions
            };
        }
    });

})(jQuery);
