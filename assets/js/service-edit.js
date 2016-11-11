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
        l_preview = null,
        refreshContainer = null,
        refreshBtn = null,
        previewSessionPicker = null,
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
        l_preview = $('.edd-bk-preview-session-picker');
        refreshContainer = $('.edd-bk-refresh-preview-container');
        refreshBtn = refreshContainer.find('.edd-bk-refresh-preview-btn');
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

        // Init preview session picker
        previewSessionPicker = new EddBk.Widget.PreviewSessionPicker(l_preview, service, availBuilder);

        // Refresh button refreshes preview
        refreshBtn.click(function() {
            previewSessionPicker.refresh();
        });
        // Before session picker updates data from service, update the service
        previewSessionPicker.on('before_update_data', function() {
            updateServiceInstance();
        });

        // Load data for session picker
        previewSessionPicker.loadData(function() {
            // Load widget content
            previewSessionPicker.loadContent(function() {
                // Move button inside session picker
                refreshContainer.appendTo(previewSessionPicker.getElement())
            });
        });
    });

})(jQuery, top, document);
