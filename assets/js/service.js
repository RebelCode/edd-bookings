function eddBkService(element) {
    $ = jQuery;
    element = $(element);
    
    var scheduleList = element.find('select[name="edd-bk-service-availability"]');
    var serviceLinksSection = element.find('div.edd-bk-service-links');
    
    // Get link formats
    var scheduleLinkFormat = element.find('a.edd-bk-schedule-link').attr('href');
    var timetableLinkFormat = element.find('a.edd-bk-timetable-link').attr('href');
    // Populate timetable IDs and titles
    var timetableIds = {};
    var timetableTitles = {};
    scheduleList.find('option').each(function() {
        var scheduleId = $(this).val();
        var timetableId = $(this).data('timetable-id');
        var timetableTitle = $(this).data('timetable-title');
        timetableIds[scheduleId] = timetableId;
        timetableTitles[timetableId] = timetableTitle;
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
    
    // Updates the schedule and timetable links
    var updateLinks = function() {
        // Get selected value
        var v = scheduleList.find('option:selected').val();
        // Toggle links (to hide when using the "new" option)
        serviceLinksSection.toggle(v !== 'new');
        if (v !== "new") {
            // Get the timetable ID and title
            var timetableId = timetableIds[v];
            var timetableTitle = timetableTitles[timetableId];
            // Update schedule link
            element.find('.edd-bk-schedule-link')
                .attr('href', scheduleLinkFormat.replace('%s', v));
            // Update timetable link
            element.find('.edd-bk-timetable-link')
                .attr('href', timetableLinkFormat.replace('%s', timetableId))
            // Update timetable name
                .find('span').text(timetableTitle);
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