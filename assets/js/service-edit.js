;(function($, window, document, undefined) {

    // Service instance for the service currently being edited
    var service = new EddBk.Service(-15),
        l_bookingsEnabled = null,
        l_sessionLength = null,
        l_sessionUnit = null,
        l_minSessions = null,
        l_maxSessions = null,
        l_sessionCost = null,
        l_useCustomerTz = null,
        availBuilder = null,
        eddMetaBoxesToHide = [
            '#edd_product_prices',
            '#edd_product_files'
        ];

    /**
     * Initializes the element pointers.
     */
    function initElements() {
        l_bookingsEnabled = $('#edd-bk-bookings-enabled');
        l_sessionLength = $('#edd-bk-session-length');
        l_sessionUnit = $('#edd-bk-session-unit');
        l_minSessions = $('#edd-bk-min-sessions');
        l_maxSessions = $('#edd-bk-max-sessions');
        l_sessionCost = $('#edd-bk-session-cost');
        l_useCustomerTz = $('#edd-bk-use-customer-tz');
        availBuilder = $('#edd-bk-service.postbox .edd-bk-availability-container');
    }

    // Toggles the sections based on whether bookings are enabled
    function updateSectionVisibility(container) {
        var bookingsEnabled = l_bookingsEnabled.is(':checked');
        container.find('div.edd-bk-collapse-container').toggle(bookingsEnabled);
        $(eddMetaBoxesToHide.join(',')).toggle(!bookingsEnabled);
    }

    /**
     * Initializes a service container section.
     *
     * @param {Element} container The container section element.
     */
    function initServiceContainerSection(container) {
        container = $(container);

        // When the bookings are enabled/disabled, update the section visibility
        l_bookingsEnabled.change(updateSectionVisibility.bind(null, container));

        // Check section visibility on first run
        updateSectionVisibility(container);
    }

    /**
     * Updates the service instance based on the current values of the form fields.
     */
    function updateServiceInstance() {
        service.addData({
            session_unit: l_sessionUnit.val(),
            session_length_n: parseInt(l_sessionLength.val()),
            session_length: parseInt(l_sessionLength.val()) * EddBk.Utils.UnitLengths[l_sessionUnit.val()],
            min_sessions: parseInt(l_minSessions.val()),
            max_sessions: parseInt(l_maxSessions.val()),
            session_cost: parseFloat(l_sessionCost.val()),
            use_customer_tz: l_useCustomerTz.is(':checked')
        });
    }

    $(document).ready(function() {
        initElements();

        // Initializes all service containers
        $('div.edd-bk-service-container').each(function() {
            initServiceContainerSection(this);
        });
    });

})(jQuery, top, document);
