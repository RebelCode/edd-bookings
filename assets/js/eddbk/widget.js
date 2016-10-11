/* global EddBk, top */

;(function($, undefined) {

    EddBk.newClass('EddBk.Widget', EddBk.Object, {
        // Constructor
        init: function(element, type) {
            this.l = $(element);
            this._super({
                l: this.l,
                type: type? type : 'Widget'
            });
        },
        // Gets the widget type
        getType: function() {
            return this.getData('type');
        },
        // Gets the element
        getElement: function() {
            return this.l;
        },
        // Loads the widget HTML content
        loadContent: function(callback) {
            var args = $.extend({
                view: 'Ajax.' + this.getType()
            }, this.getLoadContentArgs() || {});
            this._tmpCallback = callback;
            EddBk.Ajax.post('get_view', args, this._loadContentCallback.bind(this));
        },
        _loadContentCallback: function(response, status, jqXhr) {
            if (response && response.success && response.result) {
                var previousElement = this.getElement();
                var newElement = $(response.result);
                previousElement.replaceWith(newElement);
                this.setData('l', this.l = newElement);
                this.onContentLoaded();
                this.l.trigger('content_loaded');
            }
            if (typeof this._tmpCallback === 'function') {
                this._tmpCallback(response, status, jqXhr);
            }
        },
        getLoadContentArgs: function() {
            return {};
        },

        /**
         * Alias shortcut for attaching an event handler to the widget element.
         *
         * @returns {EddBk.Widget}
         */
        on: function() {
            $.fn.on.apply(this.l, arguments);
            return this;
        },

        /**
         * Alias shortcut for triggering an event on the widget element.
         *
         * @returns {EddBk.Widget}
         */
        trigger: function() {
            $.fn.trigger.apply(this.l, arguments);
            return this;
        },

        onContentLoaded: function() {}
    });

})(jQuery);
