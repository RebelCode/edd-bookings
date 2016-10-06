;(function($, window, document, undefined) {

    EddBk.newClass('EddBk.Ui.Widget', EddBk.Object, {
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
            }
            if (typeof this._tmpCallback === 'function') {
                this._tmpCallback(response, status, jqXhr);
            }
        },
        getLoadContentArgs: function() {
            return {};
        },

        onContentLoaded: function() {}
    });

})(jQuery, top, document);
