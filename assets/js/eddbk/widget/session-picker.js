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
        },
        /**
         * Initializes the events.
         */
        initEvents: function() {
            this.getDatePicker().loadContent(this.onChildWidgetLoaded.bind(this));
            this.getTimePicker().loadContent(this.onChildWidgetLoaded.bind(this));
            this.getDurationPicker().loadContent(this.onChildWidgetLoaded.bind(this));

            this.getDatePicker().on('before_show_day', this.isDateAvailable.bind(this));
            this.getDatePicker().on('date_selected', this.onDateSelected.bind(this));
            this.getDatePicker().on('change_month_year', this.onChangeMonthYear.bind(this));

            this.getTimePicker().on('change', this.onTimeChange.bind(this));

            this.getDurationPicker().on('input', this.onDurationChange.bind(this));
        },
        /**
         * Triggered when a child widget has been loaded
         */
        onChildWidgetLoaded: function() {
            // Update `loaded` data
            this.widgetsLoaded++;
            // Check if all child widgets have been loaded
            if (this.widgetsLoaded >= Object.keys(this.getWidgets()).length) {
                this.onLoaded();
                this.trigger('loaded');
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
            var sessions = this.getAvailability().getSessions(date);
            this.getTimePicker().setData('times', sessions);
            this.updateTimePicker();
            this.toggleSessionOptions(true);
        },

        /**
         * Triggered when the time picker's selected time has changed.
         */
        onTimeChange: function() {
            var max = this.calculateMaxDuration();
            this.getDurationPicker().setData('max', max);
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
            var step = this.getData('stepSessions'),
                max = this.getData('maxSessions') * step,
                startDate = this.getDatePicker().getSelectedDate(),
                date = startDate,
                count = 0;

            while (date !== null && this.isDateAvailable(null, date) && count < max) {
                date = new Date(date.getTime() + EddBk.Utils.UnitLengths.days * 1000);
                count++;
            }

            return Math.floor(count / step);
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

            timeOnlyDivs.toggle(isTimeUnit);
            this.sessionOptionsElem.toggle(toggle);

            this.sessionOptionsElem.find('> div').width(
                this.getDatePicker().find('.edd-bk-datepicker-skin').outerWidth()
            );

            this.onTimeChange();
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
        }
    });

})(jQuery);
