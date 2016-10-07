;(function ($, window, document, undefined) {

    EddBk.newClass('EddBk.Ui.Widget.SessionPicker', EddBk.Ui.Widget, {
        /**
         * Constructor.
         * 
         * @param {$|Element} element
         */
        init: function (element, options) {
            this._super(element, 'Widget.SessionPicker');
            this.addData($.extend(this.getDefaultOptions(), options));
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
            this.setData('widgetsLoaded', 0);
            this.addData({
                widgets: {
                    datePickerWidget: new EddBk.Ui.Widget.DatePicker(this.l.find('div.edd-bk-date-picker-widget')),
                    timePickerWidget: new EddBk.Ui.Widget.TimePicker(this.l.find('div.edd-bk-time-picker-widget')),
                    durationPickerWidget: new EddBk.Ui.Widget.DurationPicker(this.l.find('div.edd-bk-duration-picker-widget'))
                }
            });
        },
        /**
         * Initializes the events.
         */
        initEvents: function() {
            this.getDatePicker().loadContent(this.onChildWidgetLoaded.bind(this));
            this.getTimePicker().loadContent(this.onChildWidgetLoaded.bind(this));
            this.getDurationPicker().loadContent(this.onChildWidgetLoaded.bind(this));
        },
        /**
         * Triggered when a child widget has been loaded
         */
        onChildWidgetLoaded: function() {
            // Update `loaded` data
            var loaded = this.getData('widgetsLoaded');
            this.setData('widgetsLoaded', ++loaded);
            // Check if all child widgets have been loaded
            if (loaded >= Object.keys(this.getWidgets()).length) {
                this.onLoaded();
                this.l.trigger('loaded');
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
        },
        updateDatePicker: function() {
            this.getDatePicker().beforeShowDay = this.isDateAvailable.bind(this);
            this.getDatePicker().update();
        },
        updateTimePicker: function() {
            var timeUnit = ['hours', 'minutes'].indexOf(this.getData('unit')) !== -1;
            if (timeUnit) {
                this.getTimePicker().update();
            }
            this.getTimePicker().l.toggle(timeUnit);
        },
        updateDurationPicker: function() {
            this.getDurationPicker().addData({
                unit: this.getData('unit'),
                min: this.getData('minSessions'),
                max: this.getData('maxSessions'),
                step: this.getData('stepSessions')
            });
            this.getDurationPicker().update();
        },

        
        isDateAvailable: function(date) {
            return date.getDay() !== 0 && date.getDay() !== 6;
        },


        getWidgets: function() {
            return this.getData('widgets');
        },
        /**
         * @returns {EddBk.Ui.Widget.DatePicker}
         */
        getDatePicker: function() {
            return this.getWidgets().datePickerWidget;
        },
        getTimePicker: function() {
            return this.getWidgets().timePickerWidget;
        },
        getDurationPicker: function() {
            return this.getWidgets().durationPickerWidget;
        },
        
        getDefaultOptions: function() {
            return {
                localTzOffset: (new Date()).getTimezoneOffset() * -60,
                widgetsLoaded: 0,
                widgets: {},
                sessions: {},
                unit: 'hours',
                sessionLength: 3600,
                minSessions: 1,
                maxSessions: 1,
                stepSessions: 1,
                sessionCost: 0
            };
        }
    });
    
})(jQuery, top, document);
