/* global EddBk, top */

;(function($, undefined) {
    
    EddBk.newClass('EddBk.Widget.DurationPicker', EddBk.Widget, {
        // Constructor
        init: function(element, min, max, step, unit) {
            this._super(element, 'Widget.DurationPicker');
            this.addData({
                min: min,
                max: max,
                step: step,
                unit: unit? unit : EddBk.Utils.Units.hours
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
            this.on('input', this.onChange.bind(this));
            
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
        // Get the number of sessions
        getNumSessions: function () {
            return this.getDuration() / this.getData('step');
        },
        // Sets the duration
        setDuration: function(val) {
            this.getDurationElement().val(val);
        },
        // Gets the duration in seconds
        getDurationSeconds: function () {
            var duration = this.getDuration(),
                unit = this.getData('unit'),
                unitLength = EddBk.Utils.UnitLengths[unit];
            return unitLength
                ? duration * EddBk.Utils.UnitLengths[unit]
                : null;
        },
        
        // When view is loaded
        onContentLoaded: function() {
            this.initElements();
            this.initEvents();
            this.update();
        },
        // Updates the elements
        update: function() {
            // The step value also represents the length of a single session.
            // ex. min = 1 session, max = 3 sessions, step = 2 days
            // We need to multiply the min and max with step to get the min/max session lengths
            var step = this.getData('step'),
                min = this.getData('min') * step,
                max = this.getData('max') * step;

            // Hide the static text alternative
            this.getStaticAltElement().hide();
            // Calculate the new value and clamp it between the min and max
            var oldVal = parseInt(this.getDurationElement().val()) || 0,
                newVal = Math.min(max, Math.max(min, oldVal));
            // If min is not equal to max, show the input field
            this.getDurationElement()
                .attr({min: min, max: max, step: step})
                .val(newVal)
                .show();
            // Check to see if static text alternative should be shown
            if (min === max) {
                // If min is the same as max, then there is nothing to input. Show static alternative
                this.getDurationElement().hide();
                this.getStaticAltElement()
                    .text(min)
                    .show();
            }
            if (oldVal !== newVal) {
                this.trigger('input');
            }
            // this.updateUnitLabel();
            this.getUnitElement().text(this.getData('unit'));
            
            this.l.trigger('update');
        },

        /**
         * Updates the unit label.
         */
        updateUnitLabel: function() {
            // Update unit
            var unit = this.getData('unit'),
                val = parseInt(this.getDurationElement().val()),
                unitText = (val === 1)? unit.slice(0, -1) : unit;
            this.getUnitElement().text(unitText);
        },

        // Triggered on duration change
        onChange: function() {
            // this.updateUnitLabel();
        }
    });

})(jQuery);
