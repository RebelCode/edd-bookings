/* global edd_scripts */

(function ($, EddBkI18n, undefined) {

    EddBk.SessionPickerI18n = EddBkI18n;

    EddBk.newClass('EddBk.PurchaseForm', EddBk.Object, {
        // Initializes the instance
        init: function (element) {
            this.l = element;
            // Init service and session picker
            this.service = new EddBk.Service(this.l.data('service'));
            this.sessionPicker = new EddBk.Widget.ServiceSessionPicker(this.l, this.service);
            // Instance loads content after it loads the service data
            this.sessionPicker.loadData(this.sessionPicker.loadContent.bind(this.sessionPicker));

            this.initElements();
            this.initEvents();
        },
        // Initializes the element pointers
        initElements: function () {
            this.form = this.l.closest('form');
            this.btn = this.form.find('a.edd-add-to-cart');
            this.btn.find('.edd-add-to-cart-label').text('Purchase');

            return this;
        },
        // Initializes the events
        initEvents: function () {
            // Show/hide the add to cart button depending on the validity of the selected session
            this.sessionPicker.on('update', this.onUpdate.bind(this));

            this.btn.click(this.onSubmit.bind(this));
            this.form.on('submit', this.onSubmit.bind(this));

            return this;
        },
        // Triggered on update
        onUpdate: function () {
            this.btn.toggle(this.sessionPicker.getSelectedSession() !== null);
        },
        // Triggered on submission
        onSubmit: function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            // Update spinner
            var $spinner = this.btn.find('.edd-loading');
            $spinner.css({
                'margin-left': $spinner.width() / -2,
                'margin-top': $spinner.height() / -2
            });
            // Disable button, preventing rapid additions to cart during ajax request and show the spinner
            this.btn
                .prop('disabled', true)
                .attr('data-edd-loading', '');

            this.sessionPicker.validate(this.onValidateSession.bind(this, ev));
        },
        // Triggered when a session is validated with the server when the form is submitted
        onValidateSession: function (ev, valid) {
            if (valid) {
                // Invoke default EDD ATC handler function
                EddBk.PurchaseForm.getEddAddToCartEventHandler().apply(this.btn, [ev]);
            } else {
                // Re-enable the button
                // If not valid, the session picker will show the "unavailable session" message.
                this.btn.removeAttr('data-edd-loading')
                    .prop('disabled', false);
            }
        }
    });

    EddBk.PurchaseForm.elementWhitelist = {
        // List of whitelisted attributes
        list: {
            'main': ['id', 'class'],
            'content': ['id', 'class'],
            'page': ['id', 'class']
        },
        // Attribute selector formatting function
        formatAttrSelector: function(attr, value) {
            return '[%a*="%v"]:not(body)'
                .replace(/%a/g, attr)
                .replace(/%v/g, value);
        },
        // Gets an array of selectors for the given attributes and the value
        attrSelector: function(attrs, value) {
            return attrs.map(function(attr) {
                return this.formatAttrSelector(attr, value);
            }.bind(this));
        },
        // Gets the selectors for whitelisted elements
        selectors: function() {
            var selectors = Object.keys(this.list).map(function(element) {
                return this.attrSelector(this.list[element], element);
            }.bind(this));

            return [].concat.apply([], selectors);
        },
        // Gets a single selector for all whitelisted elements
        selector: function() {
            return this.selectors().join(', ');
        }
    };

    /**
     * Utility static method for getting the EDD ATC handler function.
     * 
     * @returns {Function}
     */
    EddBk.PurchaseForm.getEddAddToCartEventHandler = function() {
        if (EddBk.PurchaseForm.eddAddToCartEventHandler === undefined) {
            // Get the EDD click handler function
            var eventType = 'click',
                eventNamespace = 'eddAddToCart',
                eventHandle = eventType + '.' + eventNamespace,
                eddHandler = $('body').data('events')[eventHandle];
            // For more recent jquery versions:
            if (!eddHandler) {
                // Get all click bindings
                var bindings = $._data(document.body, 'events')[eventType];
                // Search all bindings for those with the 'eddAddToCart' namespace
                for (var i in bindings) {
                    if (bindings[i].namespace === eventNamespace) {
                        eddHandler = bindings[i].handler;
                        break;
                    }
                }
            }
            // Save pointer to handler
            EddBk.PurchaseForm.eddAddToCartEventHandler = eddHandler;
            // Unbind all handlers from the event
            $('body').unbind(eventHandle);
        }

        return EddBk.PurchaseForm.eddAddToCartEventHandler;
    };

    // Initializes instances when the DOM is ready
    $(document).ready(function () {
        // Create and save instances
        EddBk.PurchaseForm.instances = autoCreateInstances($('.edd-bk-service-session-picker'));
    });

    /**
     * Creates all the required session picker widget instances for the current page.
     *
     * @param {jQuery} jq The jQuery element selector object.
     * @returns {Array} An array of created instances.
     */
    function autoCreateInstances(jq) {
        var instances = [];
        var selector = EddBk.PurchaseForm.elementWhitelist.selector();
        // Create instances
        jq.each(function (i, l) {
            var $l = $(l);
            // If in content and not in a widget, initialize
            if ($l.parents(selector).length > 0) {
                var instance = new EddBk.PurchaseForm($l);
                instances.push(instance);
            } else {
                $l.remove();
            }
        });

        return instances;
    }

})(jQuery, EddBkLocalized_SessionPickerI18n);
