;(function($, window, document, undefined) {
    
    EddBk.newClass('EddBk.Ui.Widget.TimePicker', EddBk.Ui.Widget, {
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
            this.update();
            
            this.l.trigger('content_load');
        },
        
        // Updates the elements
        update: function() {
            var selectElem = this.getSelectElement().empty();
            var times = this.getData('times');
            for (var i in times) {
                $(document.createElement('option'))
                .attr('value', i)
                .text(this.formatTime(times[i]))
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
        onChange: function() {
            this.l.trigger('change');
        }
    });
    
})(jQuery, top, document);
