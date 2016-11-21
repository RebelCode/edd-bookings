/* global moment */

(function ($, moment, document, undefined) {

    var isCreatingCustomer = false;

    $(document).ready(function () {
        initDateTimeFields();
        updateDuration();
        $('#service').on('change', updateServiceInfo);
        $('#customer_tz').on('change', updateAdvancedTimes);
        $('#create-customer, #choose-customer').click(toggleCreateCustomerFields);
        $('#create-customer-btn').click(onCreateCustomerSubmit);
        // Enter press simulates "Create customer" button click
        $('#customer-name, #customer-email').on('keypress', function(e) {
            if (e.which === 13 || e.keyCode === 13) {
                $('#create-customer-btn').click();
                e.preventDefault();
                e.stopPropagation();
            }
        });
        updateServiceInfo();
        updateAdvancedTimes();
    });

    // Initializes date time fields with pickers
    function initDateTimeFields() {
        var datePickerOptions = {
            dateFormat: 'yy-mm-dd'
        };
        var timePickerOptions = {
            timeFormat: "HH:mm:ss",
            showMillisec: false,
            showMicrosec: false,
            showTimezone: false,
            timeInput: true,
            timezone: 0,
            beforeShow: function (el, instance) {
                $(el).prop('disabled', true);
            },
            onClose: function (dateText, instance) {
                // 'this' refers to the input field.
                $(this).prop('disabled', false);
            },
            onSelect: function () {
                updateDuration();
                updateAdvancedTimesForElem($(this));
            }
        };
        var datetimepickerOptions = $.extend({}, datePickerOptions, timePickerOptions);
        $('input.edd-bk-datetime').datetimepicker(datetimepickerOptions);
    }

    /**
     * Gets the duration between selected start and end datetimes.
     *
     * @return integer The duration as a number of milliseconds.
     */
    function getDuration() {
        var start = $('#start').datetimepicker('getDate'),
            end = $('#end').datetimepicker('getDate');
        return (end - start) + 1000;
    }

    /**
     * Updates the duration text to match the selected start and end datetimes.
     */
    function updateDuration() {
        var duration = getDuration(),
            durationText = moment.preciseDiff(0, duration);
        $('#duration').text(durationText);
    }

    /**
     * Gets the server timezone.
     *
     * @returns integer
     */
    function getServerTz() {
        return parseInt($('#server-tz').val());
    }

    /**
     * Gets the customer timezone.
     *
     * @returns integer
     */
    function getCustomerTz() {
        return parseInt($('#customer_tz').val());
    }

    /**
     * Updates the advanced times.
     */
    function updateAdvancedTimes() {
        updateAdvancedTimesForElem($('#start'));
        updateAdvancedTimesForElem($('#end'));
    }

    /**
     * Updates the advanced times for a given element (start or end input field).
     *
     * @param {Element} e
     */
    function updateAdvancedTimesForElem(e) {
        var serverTimestamp = moment(e.val(), 'YYYY-MM-DD HH:mm:ss').format('X'),
            serverTz = getServerTz(),
            // Compute UTC datetime
            utcTs = serverTimestamp - serverTz,
            utcDate = moment.unix(utcTs),
            // Compute customer datetime
            customerTz = getCustomerTz(),
            customerDate = moment(utcDate).add(customerTz, 's'),
            // Get elements
            advTimesContainer = $(e).parent().next().find('> div'),
            utcField = advTimesContainer.find('p.utc-time > code'),
            customerField = advTimesContainer.find('p.customer-time > code');
        // Update element texts to show the correct datetimes
        utcField.text(utcDate.format('YYYY-MM-DD HH:mm:ss'));
        customerField.text(customerDate.format('YYYY-MM-DD HH:mm:ss'));
    }

    /**
     * Toggles the create customer fields visibility.
     */
    function toggleCreateCustomerFields() {
        isCreatingCustomer = !isCreatingCustomer;
        $('.edd-bk-if-create-customer').toggle(isCreatingCustomer);
        $('.edd-bk-if-choose-customer').toggle(!isCreatingCustomer);
    }

    /**
     * Called when the "Create Customer" button is clicked, to submit the info to the
     * server and create the customer.
     */
    function onCreateCustomerSubmit() {
        // Set loading state
        setCreateCustomerLoading(true);
        // Clear any previous errors
        $('#create-customer-error').text();
        // Get user-inputted customer data
        var customerData = {
            name: $('#customer-name').val(),
            email: $('#customer-email').val(),
        };
        // Send AJAX request to create customer
        EddBk.Ajax.post('create_customer', customerData, createCustomerCallback);
    }

    /**
     * Called when the AJAX request for creating a customer returns with a response.
     *
     * @param {Object} response The response
     */
    function createCustomerCallback(response) {
        if (response.success && response.result) {
            reloadCustomerDropdown(response.result, function () {
               setCreateCustomerLoading(false);
            });
        } else {
            setCreateCustomerLoading(false);
            $('#create-customer-error').text(response.error);
        }
    }

    /**
     * Reloads the customer dropdown by retrieving an updated version from the server.
     *
     * @param {integer} selected The selected customer ID.
     * @param {Function} callback The callback.
     */
    function reloadCustomerDropdown(selected, callback) {
        var args = {
            id: 'customer',
            name: 'customer_id',
            class: 'customer-id',
            chosen: false,
            selected: selected
        };
        EddBk.Ajax.post('get_customer_dropdown', args, function(response) {
            if (response.success && response.result) {
                $('#customer').replaceWith($(response.result));
                toggleCreateCustomerFields(false);
            }
            callback(response);
        });
    }

    /**
     * Sets the loading state of the "Create Customer" button.
     *
     * @param {boolean} loading True for loading state, false for normal state.
     */
    function setCreateCustomerLoading(loading) {
        var btn = $('#create-customer-btn');
        btn.prop('disabled', loading);
        btn.find('> span').toggle(!loading);
        btn.find('.edd-bk-loading').css('display', loading? 'inline-block' : 'none');
    }

    /**
     * Updates the service info message shown in the Booking Details section.
     */
    function updateServiceInfo() {
        // Hide messages and show loading
        $('#service-info-msg-singular, #service-info-msg-plural').hide();
        $('#service-info-loading').show();
        // Get selected service
        var serviceId = $('#service').val();
        // Stop if no service selected
        if (serviceId === '') {
            $('#service-info-loading').hide();
            return;
        }
        // Create service instance and get info from server
        var service = new EddBk.Service(serviceId);
        service.loadData(function() {
            var meta = service.getData(),
            // Get proper session unit string - pluralized if needed and translated
                sessionUnitLabel = EddBk.Utils.UnitLabels[meta.session_unit],
                sessionUnit = (parseInt(meta.session_length_n) > 1)
                    ? sessionUnitLabel.plural
                    : sessionUnitLabel.singular;
            // Target the appropriate message element (singular or plural)
            var msg = (meta.min_sessions === meta.max_sessions)
                ? $('#service-info-msg-singular')
                : $('#service-info-msg-plural');
            // Set the data
            msg.find('span.service-name').text(meta.name);
            msg.find('span.session-length').text(meta.session_length_n);
            msg.find('span.session-unit').text(sessionUnit);
            msg.find('span.min-sessions').text(meta.min_sessions);
            msg.find('span.max-sessions, span.num-sessions').text(meta.max_sessions);
            // Show it
            msg.show();
            // Hide loading
            $('#service-info-loading').hide();
        });
    }

    /**
     * Gets the meta data for a particualr service.
     *
     * @param {integer|string} serviceId The service ID.
     * @param {Function} callback The callback.
     */
    function getServiceMeta(serviceId, callback) {
        EddBk.Ajax.post('get_service_meta', {
            id: serviceId
        }, function(response) {
            callback((response && response.success && response.meta)? response.meta : null);
        });
    }

})(jQuery, moment, document);
