/* global EddBk, top */

;(function($, undefined) {

    EddBk.newClass('EddBk.Widget', EddBk.Object, {
        // Constructor
        init: function(element, type) {
            this.l = $(element);
            this._super({
                l: this.l,
                type: type? type : 'Widget.Generic'
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
            this.setLoading(true);

            var content = this.getWidgetContent();
            this.l.html(content);

            this.setLoading(false);
            this.onContentLoaded();
            this.l.addClass('edd-bk-widget');
            this.l.trigger('content_loaded');

            if (typeof callback === 'function') {
                callback();
            }
        },
        _loadContentCallback: function(callback, response, status, jqXhr) {
            if (response && response.success && response.result) {
                this.l.html(response.result);
                this.setLoading(false);
                this.onContentLoaded();
                this.l.addClass('edd-bk-widget');
                this.l.trigger('content_loaded');
            }
            if (typeof callback === 'function') {
                callback(response, status, jqXhr);
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

        /**
         * Alias shortcut for finding elements in the widget element.
         *
         * @returns {EddBk.Widget}
         */
        find: function() {
            return $.fn.find.apply(this.l, arguments);
        },

        /**
         * Sets the loading state of the widget.
         *
         * @param {boolean} loading True for loading, false otherwise.
         * @returns {EddBk.Widget}
         */
        setLoading: function (loading) {
            this.l.toggleClass('edd-bk-widget-is-loading', loading);
            return this;
        },

        onContentLoaded: function() {}
    });

})(jQuery);
