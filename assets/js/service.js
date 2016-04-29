function eddBkService(element) {
    $ = jQuery;
    element = $(element);
    
    var scheduleList = element.find('select[name="edd-bk-service-schedule"]');
    var serviceLinksSection = element.find('div.edd-bk-service-links');
    
    // Get link formats
    var scheduleLinkFormat = element.find('a.edd-bk-schedule-link').attr('href');
    var availabilityLinkFormat = element.find('a.edd-bk-availability-link').attr('href');
    // Populate availability IDs and titles
    var availabilityIds = {};
    var availabilityTitles = {};
    scheduleList.find('option').each(function() {
        var scheduleId = $(this).val();
        var availabilityId = $(this).data('availability-id');
        var availabilityTitle = $(this).data('availability-title');
        availabilityIds[scheduleId] = availabilityId;
        availabilityTitles[availabilityId] = availabilityTitle;
    });
    
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
        if (bookingsEnabled) {
            // Update links on first run
            updateLinks();
        }
    };
    
    // Updates the schedule and availability links
    var updateLinks = function() {
        // Get selected value
        var v = scheduleList.find('option:selected').val();
        // Toggle links (to hide when using the "new" option)
        serviceLinksSection.toggle(v !== 'new');
        if (v !== "new") {
            // Get the availability ID and title
            var availabilityId = availabilityIds[v];
            var availabilityTitle = availabilityTitles[availabilityId];
            // Update schedule link
            element.find('.edd-bk-schedule-link')
                .attr('href', scheduleLinkFormat.replace('%s', v));
            // Update availability link
            element.find('.edd-bk-availability-link')
                .attr('href', availabilityLinkFormat.replace('%s', availabilityId))
            // Update availability name
                .find('span').text(availabilityTitle);
        }
    };
    
    // When the bookings are enabled/disabled, update the section visibility
    element.find('input#edd-bk-bookings-enabled').change(updateSectionVisibility);
    // When the user changes the schedule, update the links
    scheduleList.change(updateLinks);
    // Show help on click
    element.find('.edd-bk-help-toggler').click(function() {
        element.find('div.edd-bk-help-section').slideToggle(200);
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