/* global edd_scripts */

(function ($) {

    function saveEddHandler() {
        // Get the EDD click handler function
        var eddHandler = $('body').data('events')['click.eddAddToCart'];
        // For more recent jquery versions:
        if (!eddHandler) {
            // Get all click bindings
            var bindings = $._data(document.body, 'events')['click'];
            // Search all bindings for those with the 'eddAddToCart' namespace
            for (var i in bindings) {
                if (bindings[i].namespace === 'eddAddToCart') {
                    eddHandler = bindings[i].handler;
                    break;
                }
            }
        }
        // Set globals
        window.eddBkGlobals = {
            eddHandler: eddHandler
        };
    }

    function createInstance(elem) {
        var instance = new EddBkPurchaseFormSessionPicker(elem);
        instance.init();
        return instance;
    }

    function createInstances(selector) {
        // Initialize the instances
        window.eddBkInstances = {};
        // Find all elements that match the selector
        $(selector).each(function (i, elem) {
            var $elem = $(elem);
            // If in content and not in a widget, initialize
            if ($elem.parents('[id*="content"]:not(body), [class*="content"]:not(body)').length > 0) {
                var instance = createInstance($elem);
                if (instance !== null) {
                    window.eddBkInstances[i] = instance;
                }
            } else {
                // Otherwise, remove element
                $elem.remove();
                return;
            }
        });

        return window.eddBkInstances;
    }

    $(document).ready(function () {
        return;
        // Save the EDD add to cart handler
        saveEddHandler();
        // Initialize the instances
        var instances = createInstances('.edd-bk-service-container');
        // If instances have been created, unbind the traditional EDD add to cart handler.
        if (Object.keys(instances).length) {
            $('body').unbind('click.eddAddToCart');
        }
    });

    $(document).ready(function() {
        $('.content-area .edd-bk-service-session-picker').each(function(i, l) {
            var serviceId = $(l).data('service');
            var service = new EddBk.Service(serviceId);
            var instance = new EddBk.Widget.ServiceSessionPicker(l, service);
            instance.loadData(instance.loadContent.bind(instance));
        });
    });


})(jQuery);
