;(function($, window, document, undefined) {
    function eddBkService(element) {
        element = $(element);

        // Toggles the sections based on whether bookings are enabled
        var updateSectionVisibility = function() {
            var bookingsEnabled = element.find('input#edd-bk-bookings-enabled').is(':checked');
            element.find('div.edd-bk-collapse-container').toggle(bookingsEnabled);
            // Also hide some other metaboxes
            var edd_metaboxes_to_hide = [
                '#edd_product_prices',
                '#edd_product_files'
            ];
            $(edd_metaboxes_to_hide.join(',')).toggle(!bookingsEnabled);
        };

        // When the bookings are enabled/disabled, update the section visibility
        element.find('input#edd-bk-bookings-enabled').change(updateSectionVisibility);

        // Check section visibility on first run
        updateSectionVisibility();
    }

    // Initializes all service containers
    $(document).ready(function() {
        $('div.edd-bk-service-container').each(function() {
            eddBkService(this);
        });
    });

})(jQuery, top, document);
