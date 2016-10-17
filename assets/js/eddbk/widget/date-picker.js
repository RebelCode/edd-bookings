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
            var options = this.getComputedDatePickerOptions();
            this.getDatePickerElem().multiDatesPicker(options);

            // Trigger on change month year the first time
            var date = new Date();
            this._onChangeMonthYear(date.getFullYear(), date.getMonth() + 1);

            this.l.trigger('init_datepicker');
        },
        // Updates the widget
        update: function() {
            this.updateDatePicker();
            
            this.l.trigger('update');
        },
        // Refreshes the datepicker
        refresh: function() {
            this.getDatePickerElem().datepicker('refresh');
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
            this.getDatePickerElem().datepicker('setDate', date);

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
        }
    });
    
})(jQuery, top, document);
