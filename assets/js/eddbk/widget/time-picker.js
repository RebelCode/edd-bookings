;(function($, undefined) {
    
    EddBk.newClass('EddBk.Widget.TimePicker', EddBk.Widget, {
        // Constructor
        init: function(element, times) {
            this._super(element, 'Widget.TimePicker');
            this.addData({
                times: times
            });
        },
        // Initializes element pointers
        initElements: function() {
            this.addData({
                selectElem: this.l.find('select.edd-bk-time-picker-select')
            });
            
            this.l.trigger('init_elements');
        },
        // Initializes events
        initEvents: function() {
            this.getSelectElement().on('change', this.onChange.bind(this));
            
            this.l.trigger('init_events');
        },
        
        getSelectElement: function() {
            return this.getData('selectElem');
        },
        
        // Gets the selected value
        getSelectedValue: function() {
            return this.getSelectElement().val();
        },
        // Sets the selected value
        setSelectedValue: function(value) {
            this.getSelectElement().val(value);
        },
        // Gets the selected option element
        getSelectedItem: function() {
            return this.getSelectElement().find('option:selected');
        },
        
        // When view is loaded
        onContentLoaded: function() {
            this.initElements();
            this.initEvents();
            this.update();
        },
        
        // Updates the elements
        update: function() {
            var selectElem = this.getSelectElement().empty();
            var dates = this.getData('times');
            for (var timestamp in dates) {
                $('<option>')
                .attr('value', timestamp)
                .text(this.formatTime(dates[timestamp]))
                .appendTo(selectElem);
            }
            this.l.trigger('update');
        },
        // Formats a date object into a time string
        formatTime: function(date) {
            var hours = ('0' + date.getHours()).substr(-2),
                mins = ('0' + date.getMinutes()).substr(-2),
                text = hours + ':' + mins;
            return text;
        },
        // Triggered on change
        onChange: function() {},

        getWidgetContent: function() {
            return ''
                + '<span><strong>' + EddBk.SessionPickerI18n.time + ' </strong></span>'
                + '<select class="edd-bk-time-picker-select"></select>';
        }
    });
    
})(jQuery);
