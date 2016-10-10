/* global EddBk, top */

;(function($, window, document, undefined) {
    
    EddBk.newClass('EddBk.Ui.Widget.DurationPicker', EddBk.Ui.Widget, {
        // Constructor
        init: function(element, min, max, step, unit) {
            this._super(element, 'Widget.DurationPicker');
            this.addData({
                min: min,
                max: max,
                step: step,
                unit: unit? unit : 'hours'
            });
        },
        
        // Initializes element pointers
        initElements: function() {
            this.addData({
                durationElem: this.l.find('input.edd-bk-duration-picker-field'),
                staticAltElem: this.l.find('span.edd-bk-duration-picker-static-alt'),
                unitElem: this.l.find('span.edd-bk-duration-picker-unit')
            });
            
            this.l.trigger('init_elements');
        },
        // Initializes events
        initEvents: function() {
            this.getData('field').on('change', this.onChange.bind(this));
            
            this.l.trigger('init_events');
        },
        
        // Gets the duration element
        getDurationElement: function() {
            return this.getData('durationElem');
        },
        getStaticAltElement: function() {
            return this.getData('staticAltElem');
        },
        // Gets the unit element
        getUnitElement: function() {
            return this.getData('unitElem');
        },
        
        // Gets the duration
        getDuration: function() {
            return parseInt(this.getDurationElement().val());
        },
        // Sets the duration
        setDuration: function(val) {
            this.getDurationElement().val(val);
        },
        // Gets the duration in seconds
        getDurationSeconds: function () {
            var duration = this.getDuration(),
                unit = this.getData('unit');
            if (EddBk.Utils.Units[unit]) {
                return duration * EddBk.Utils.Units[unit];
            } else {
                return null;
            }
        },
        
        // When view is loaded
        onContentLoaded: function() {
            this.initElements();
            this.update();
        },
        // Updates the elements
        update: function() {
            var min = this.getData('min'),
                max = this.getData('max'),
                step = this.getData('step');
            // Update field
            if (min === max) {
                // If min is the same as max, then there is nothing to input. Show static alternative
                this.getDurationElement().hide();
                this.getStaticAltElement()
                    .text(min)
                    .show();
            } else {
                // If min is not equal to max, show the input field
                this.getDurationElement()
                    .attr({min: min, max: max, step: step})
                    .val(min)
                    .show();
            }
            // Update unit
            this.getUnitElement().text(this.getData('unit'));
            
            this.l.trigger('update');
        },
        
        // Triggered on duration change
        onChange: function() {
            var max = this.getData('max');
            if (this.getDuration() > max) {
                this.setDuration(max);
            }
        }
    });

    EddBk.Ui.Widget.DurationPicker.Units = {
        minutes: 60,
        hours: 3600,
        days: 86400,
        weeks: 604800
    };
    
})(jQuery, top, document);
