;(function($, window, document, undefined) {
    
    EddBk.newClass('EddBk.Ui.Widget.DatePicker', EddBk.Ui.Widget, {
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
            
            this.l.trigger('content_load');
        },
        
        // Updates the datepicker
        updateDatePicker: function() {
            var options = this.getComputedDatePickerOptions();
            this.getDatePickerElem().multiDatesPicker(options);
            
            this.l.trigger('init_datepicker');
        },
        // Updates the widget
        update: function() {
            this.updateDatePicker();
            
            this.l.trigger('update');
        },
        
        // Called before a day is shown
        // This one is the actual callback. The one after is the one used for extension by sub-classes
        _beforeShowDay: function(date) {
            var ret = this.beforeShowDay(date) || true;
            return [ret, ''];
        },
        
        // Called before a day is shown. Should return a boolean that determines if the date is selectable or not.
        beforeShowDay: function(date) {
            this.l.trigger('before_show_day');
        },
        // Called when a date is selected
        onDateSelected: function() {
            this.l.trigger('on_date_selected');
        },
        // Called when the month or year changes
        onChangeMonthYear: function() {
            this.l.trigger('on_change_month_year`');
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
                onSelect: this.onDateSelected.bind(this),
                // When the month of year changes
                onChangeMonthYear: this.onChangeMonthYear.bind(this)
            };
        }
    });
    
})(jQuery, top, document);
