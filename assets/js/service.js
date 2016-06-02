function eddBkService(element) {
    $ = jQuery;
    element = $(element);
    
    var availabilityList = element.find('select[name="edd-bk-service-availability"]');
    var availabilityLinksSection = element.find('.edd-bk-availability-links-section');
    var availabilityEditRulesLink = element.find('#edd-bk-edit-rules');
    var availabilityCreateNewTooltip = element.find('.edd-bk-availability-create-new-tooltip');
    // Get link formats
    var availabilityLinkFormat = element.find('a.edd-bk-availability-link').attr('href');
    
    // Toggles the sections based on whether bookings are enabled
    var updateSectionVisibility = function() {
        var bookingsEnabled = element.find('input#edd-bk-bookings-enabled').is(':checked');
        element.find('div.edd-bk-service-section:not(:first-child)').toggle(bookingsEnabled);
        // Also hide some other metaboxes
        var edd_metaboxes_to_hide = [
            '#edd_product_prices',
            '#edd_product_files'
        ];
        $(edd_metaboxes_to_hide.join(',')).toggle(!bookingsEnabled);
        if (bookingsEnabled) {
            // Update links on first run
            updateAvailabilityHelperLinks();
        }
    };
    
    // Updates the availability links
    var updateAvailabilityHelperLinks = function() {
        // Get selected value
        var v = availabilityList.find('option:selected').val();
        // Toggle links (to hide when using the "new" option)
        availabilityLinksSection.toggle(v !== 'new');
        // Toggle the "create new" help text
        availabilityCreateNewTooltip.toggle(v === 'new');
        // Update availability link
        element.find('.edd-bk-availability-link')
            .attr('href', availabilityLinkFormat.replace('%s', v));
    };
    
    // When the bookings are enabled/disabled, update the section visibility
    element.find('input#edd-bk-bookings-enabled').change(updateSectionVisibility);
    // When the user changes the schedule, update the links
    availabilityList.change(updateAvailabilityHelperLinks)
    
    // Check section visibility on first run
    updateSectionVisibility();
    
    var resetAvailabilityEditRulesLink = function() {
        // If link already clicked
        if (availabilityEditRulesLink.data('clicked')) {
            // Get original text
            var originalText = availabilityEditRulesLink.data('clicked');
            // Restore original text
            availabilityEditRulesLink.text(originalText);
            // Set click marker to false
            availabilityEditRulesLink.data('clicked', false);
        }
    };
    
    var availabilityEditLinkAction = function(e) {
        clearTimeout(availabilityEditRulesLink.data('timeout'));
        // If link already clicked
        if (availabilityEditRulesLink.data('clicked')) {
            resetAvailabilityEditRulesLink();
        } else {
            // Backup current text
            availabilityEditRulesLink.data('clicked', availabilityEditRulesLink.text());
            // Get new text
            var newText = $('#edd-bk-edit-rules-second-text').text();
            // Swap text
            availabilityEditRulesLink.text(newText);
            // Prevent link default action
            e.preventDefault();
            // Set a timeout to reset after some seconds
            availabilityEditRulesLink.data('timeout', setTimeout(resetAvailabilityEditRulesLink, 8000));
        }
    };
    
    availabilityEditRulesLink.click(availabilityEditLinkAction);
    
}

// Initializes all service containers
jQuery(document).ready(function() {
    jQuery('div.edd-bk-service-container').each(function() {
        eddBkService(this);
    });
});