;(function($, window, document, undefined) {

    // Service instance for the service currently being edited
    var service = new EddBk.Service(-15),
        // elements for option field elements
        l_bookingsEnabled = null,
        l_sessionLength = null,
        l_sessionUnit = null,
        l_minSessions = null,
        l_maxSessions = null,
        l_sessionCost = null,
        l_useCustomerTz = null,
        // availability preview elements and instances
        availBuilder = null,
        l_preview = null,
        refreshContainer = null,
        refreshBtn = null,
        // availability preview containers and parents for responsiveness
        previewMetaboxContainer = null,
        previewMetaboxParent = null,
        previewInlineContainer = null,
        previewInlineParent = null,
        previewSessionPicker = null,
        previewInlineToggler = null,
        elementsToShow = [
            '#edd-bk-availability-preview'
        ],
        elementsToHide = [
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
        previewMetaboxContainer = $('#edd-bk-availability-preview.postbox');
        previewMetaboxParent = previewMetaboxContainer.find('.edd-bk-preview-container');
        previewInlineContainer = $('.edd-bk-inline-availability-preview-container');
        previewInlineParent = previewInlineContainer.find('.edd-bk-inline-availability-preview-session-picker');
        previewInlineToggler = previewInlineContainer.find('.edd-bk-preview-toggler');
    }

    // Toggles the sections based on whether bookings are enabled
    function updateSectionVisibility(container) {
        var bookingsEnabled = l_bookingsEnabled.is(':checked');
        container.find('div.edd-bk-collapse-container').toggle(bookingsEnabled);
        // Update the visibility of other elements
        $(elementsToShow.join(',')).toggle(bookingsEnabled);
        $(elementsToHide.join(',')).toggle(!bookingsEnabled);
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

    /**
     * Updates the availability preview visibility and placement for responsiveness.
     */
    function updatePreviewVisibility() {
        if (!l_bookingsEnabled.is(':checked')) {
            return;
        }
        var w = $('html').width(),
            metaboxVisibility = (w > 850),
            inlineVisibility = !metaboxVisibility,
            parent = (metaboxVisibility) ? previewMetaboxParent : previewInlineParent;

        previewMetaboxContainer.toggle(metaboxVisibility);
        previewInlineContainer.toggle(inlineVisibility);
        previewSessionPicker.getElement().appendTo(parent);
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
                refreshContainer.appendTo(previewSessionPicker.getElement());
            });
        });

        previewInlineToggler.click(function() {
            previewInlineParent.toggle();
        });

        // Update preview visibility for the first time
        updatePreviewVisibility();
    });

    // On window resize, update the availability preview visibility and placement
    $(window).on('resize', updatePreviewVisibility);

})(jQuery, top, document);
