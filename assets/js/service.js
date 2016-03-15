function eddBkService(element) {
    $ = jQuery;
    element = $(element);
    
    // Toggles the sections based on whether bookings are enabled
    var updateSectionVisibility = function() {
        var bookingsEnabled = element.find('input#edd-bk-bookings-enabled').is(':checked');
        element.find('div.edd-bk-service-section:not(:first-child)').toggle(bookingsEnabled);
        // Also hide some other metaboxes
        var edd_metaboxes_to_hide = [
            '#edd_product_prices',
            '#edd_product_files',
            '#edd_product_settings'
        ];
        $(edd_metaboxes_to_hide.join(',')).toggle(!bookingsEnabled);
    };
    
    // When the bookings are enabled/disabled, update the section visibility
    element.find('input#edd-bk-bookings-enabled').change(updateSectionVisibility);
    element.find('div.edd-bk-help-section > a').click(function() {
        $(this).parent().find('> div').slideToggle(200);
    });
    // Check section visibility on first run
    updateSectionVisibility();
}

// Initializes all service containers
jQuery(document).ready(function() {
    jQuery('div.edd-bk-service-container').each(function() {
        eddBkService(this);
    });
});