;(function($, window, document, undefined) {

    EddBk.newClass('EddBk.Widget.DatePicker', EddBk.Widget, {
        // Constructor
        init: function(element, options, selectRange) {
            this._super(element, 'Widget.DatePicker');
            this.addData({
                options: options,
                selectRange: selectRange
            });
        },
        // Initializes element pointers
        initElements: function() {
            this.addData({
                datePickerElem: this.l.find('div.edd-bk-datepicker'),
                altFieldElem: this.l.find('input.edd-bk-datepicker-value')
            });

            this.l.trigger('init_elements');
        },
        // Initializes events
        initEvents: function() {
            // @todo events

            this.l.trigger('init_events');
        },
        // When view is loaded
        onContentLoaded: function() {
            this.initElements();
            this.initEvents();
            this.update();
        },

        // Updates the datepicker
        updateDatePicker: function() {
            var date = this.getSelectedDate();

            var options = this.getComputedDatePickerOptions();
            this.getDatePickerElem().multiDatesPicker(options);

            if (date !== null && date !== undefined) {
                this.setSelectedDate(date);
            } else {
                date = new Date();
            }

            // Trigger on change month year the first time
            this._onChangeMonthYear(date.getFullYear(), date.getMonth() + 1);

            this.l.trigger('init_datepicker');
            return this;
        },
        // Updates the widget
        update: function() {
            this.updateDatePicker();

            this.l.trigger('update');
            return this;
        },
        // Refreshes the datepicker
        refresh: function() {
            this.getDatePickerElem().datepicker('refresh');
            return this;
        },

        // Called before a day is shown
        // This one is the actual callback. The one after is the one used for extension by sub-classes
        _beforeShowDay: function(date) {
            var ret = this.beforeShowDay(date);
            (ret === undefined) && (ret = true);
            return [ret, ''];
        },
        // Called when a date is selected
        // This one is the actual callback. The one after is the one used for extension by sub-classes
        _onDateSelected: function(dateStr) {
            var date = this.parseDate(dateStr);

            // Fixes null date on first few date selections
            this.setSelectedDate(date);

            return this.onDateSelected(date);
        },
        // Called when the month or year changes
        // This one is the actual callback. The one after is the one used for extension by sub-classes
        _onChangeMonthYear: function(year, month) {
            return this.onChangeMonthYear(year, month);
        },

        // Called before a day is shown. Should return a boolean that determines if the date is selectable or not.
        beforeShowDay: function(date) {
            var e = new $.Event('before_show_day');
            this.l.trigger(e, [date]);

            return e.result;
        },
        // Called when a date is selected
        onDateSelected: function(date) {
            var e = new $.Event('date_selected');
            this.l.trigger(e, [date]);

            return e.result;
        },
        // Called when the month or year changes
        onChangeMonthYear: function(year, month) {
            var e = new $.Event('change_month_year');
            this.l.trigger(e, [year, month]);

            return e.result;
        },

        // Gets the datepicker element
        getDatePickerElem: function() {
            return this.getData('datePickerElem');
        },
        // Gets the alt field element
        getAltFieldElem: function() {
            return this.getData('altFieldElem');
        },
        // Gets the selected date
        getSelectedDate: function() {
            return this.getDatePickerElem().datepicker('getDate');
        },
        /**
         * Gets the selected date timestamp.
         *
         * This method also re-offsets the timezone of the selected date, since the `getTime()` method returns the UTC
         * timestamp for a date object.
         *
         * This means that if the selected date is the 24/Oct/2016, the `getTime()` method, for a timezone offset of
         * UTC+2, will return 23/Oct/2016 at 22:000:00.
         *
         * So the timezone offset is re-added to the date to get the desired 24/Oct/2016 at 00:00:00.
         *
         * @returns {Number}
         */
        getSelectedTimestamp: function() {
            var selected = this.getSelectedDate();
            return (selected === null)
                ? null
                : EddBk.Utils.msToSeconds(selected.getTime()) + EddBk.Utils.timezone(selected);
        },
        setSelectedDate: function(date, simClick) {
            simClick = (simClick === undefined)? false : simClick;

            this.getDatePickerElem().datepicker('setDate', date);

            if (simClick && date !== null) {
                // Simulate user click on the selected date, to refresh the auto selected range
                this.simulateClick();
            }

            if (date === null) {
                this.getDatePickerElem().multiDatesPicker('resetDates');
            }

            return this;
        },

        // Simulates a click event on the currently selected date
        simulateClick: function() {
            this.getDatePickerElem()
                .find('.ui-datepicker-current-day').first()
                .find('> a').click();

            return this;
        },

        // Gets the datepicker options
        getOptions: function() {
            return this.getData('options');
        },
        // Sets the datepicker options
        setOptions: function(options) {
            this.setData('options', options);
        },
        // Gets the datepicker options merged with the defaults
        getComputedDatePickerOptions: function() {
            return $.extend(this.getDefaultDatePickerOptions(), this.getOptions());
        },

        // Gets the select range
        getSelectRange: function() {
            return this.getData('selectRange');
        },
        // Sets the select range
        setSelectRange: function(selectRange) {
            this.setData('selectRange', selectRange);
            return this;
        },

        /**
         * Parses a datepicker date string into a Date object.
         *
         * @param {string} dateStr The date string.
         * @returns {Date} The parsed date.
         */
        parseDate: function(dateStr) {
            // Do not continue if already a date object
            if (typeof dateStr === 'object') {
                return dateStr;
            }
            // Parse given date string
            var dateParts = dateStr.split('/'),
                date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]);

            return date;
        },

        // Gets the default options for the datepicker
        getDefaultDatePickerOptions: function() {
            return {
                // Hide the Button Panel
                showButtonPanel: false,
                // Options for multiDatePicker. These are ignored by the vanilla jQuery UI datepicker
                mode: 'daysRange',
                autoselectRange: [0, this.getSelectRange()],
                adjustRangeToDisabled: true,
                // Format
                altFormat: 'mm/dd/yy',
                altField: this.getAltFieldElem(),
                // multiDatesPicker format
                dateFormat: 'mm/dd/yy',
                // Show dates from other months and allow selection
                showOtherMonths: true,
                selectOtherMonths: true,
                // events
                // Prepares the dates for availability
                beforeShowDay: this._beforeShowDay.bind(this),
                // When a date is selected by the user
                onSelect: this._onDateSelected.bind(this),
                // When the month of year changes
                onChangeMonthYear: this._onChangeMonthYear.bind(this)
            };
        },

        getWidgetContent: function() {
            return ''
                + '<div class="edd-bk-datepicker-skin">'
                + '<div class="edd-bk-datepicker"></div>'
                + '</div>'
                + '<input type="hidden" class="edd-bk-datepicker-value" value="" />';

        }
    });

})(jQuery, top, document);
