/* global moment */

(function ($, moment, document, BookingsEdit, TimePickerI18n, undefined) {

    var isCreatingCustomer = false,
        dateFormat = 'yy-mm-dd',
        timeFormat = 'HH:mm:ss';

    $.timepicker.setDefaults(TimePickerI18n);

    $(document).ready(function () {
        moment.locale(BookingsEdit.locale);

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
        $('[type="submit"]').click(function(e) {
            if (!getStartDateTime()) {
                $('#start').focus();
                e.preventDefault();
            } else if (!getEndDateTime()) {
                $('#end').focus();
                e.preventDefault();
            }
        });
        updateServiceInfo();
        updateAdvancedTimes();
    });

    // Initializes date time fields with pickers
    function initDateTimeFields() {
        var datePickerOptions = {
            dateFormat: dateFormat
        };
        var timePickerOptions = {
            timeFormat: timeFormat,
            showMillisec: false,
            showMicrosec: false,
            showTimezone: false,
            timeInput: true,
            beforeShow: function (el, instance) {
                $(el).prop('disabled', true);
            },
            onSelect: function () {
                updateDuration();
                updateAdvancedTimesForElem($(this));
            }
        };
        var datetimepickerOptions = $.extend({}, datePickerOptions, timePickerOptions);
        $('input.edd-bk-datetime').datetimepicker(datetimepickerOptions);

        // Each datepicker limits the selection of the other
        $('#start').datetimepicker('option', 'onClose', function() {
            var start = getStartDateTime();
            $('#end').datetimepicker('option', {
                minDate: start,
                minDateTime: start
            });
            $(this).prop('disabled', false);
        });
        $('#end').datetimepicker('option', 'onClose', function() {
            var end = getEndDateTime();
            $('#start').datetimepicker('option', {
                maxDate: end,
                maxDateTime: end
            });
            $(this).prop('disabled', false);
        });
    }

    /**
     * Gets the Date for a given datepicker.
     *
     * @param {Element} element
     * @returns {Date}
     */
    function getDatepickerDate(element) {
        return $(element).datetimepicker('getDate');
    }

    /**
     * Gets the date time for the "Start" datetime picker.
     *
     * @returns {Date}
     */
    function getStartDateTime() {
        return getDatepickerDate($('#start'));
    }

    /**
     * Gets the date time for the "End" datetime picker.
     *
     * @returns {Date}
     */
    function getEndDateTime() {
        return getDatepickerDate($('#end'));
    }

    /**
     * Gets the duration between selected start and end datetimes.
     *
     * @return integer The duration as a number of milliseconds.
     */
    function getDuration() {
        var start = $('#start').datetimepicker('getDate'),
            end = $('#end').datetimepicker('getDate');
        return end - start;
    }

    /**
     * Updates the duration text to match the selected start and end datetimes.
     */
    function updateDuration() {
        var duration = Math.floor(getDuration() / 1000),
            durationText = humanizeDuration(duration);
            //durationText = moment.duration(duration).humanize(); //moment.preciseDiff(0, duration);
        $('#duration').text(durationText);
    }

    /**
     * Humanizes a duration.
     *
     * @param seconds
     *
     * @returns {string}
     */
    function humanizeDuration(seconds) {
        var duration = moment.duration(getDuration());
        var units = {
            'years': 'yy',
            'months': 'mm',
            'days': 'dd',
            'hours': 'hh',
            'minutes': 'mm',
            'seconds': 'ss'
        };

        var parts = [];

        for (var unit in units) {
            var localeKey = units[unit];
            var unitAmount = moment.duration(seconds, 'seconds')[unit]();

            if (unitAmount > 0) {
                var localeString = moment.localeData()._relativeTime[localeKey];
                parts.push(localeString.replace('%d', unitAmount));
            }
        }

        return parts.join(', ');
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
        var serverTimestamp = moment(e.val(), 'YYYY-MM-DD HH:mm:ss').format('X');

        if (serverTimestamp) {
            var serverTz = getServerTz(),
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
            return;
        }
        // On invalid date
        utcField.text('...');
        customerField.text('...');
    }

    /**
     * Toggles the create customer fields visibility.
     */
    function toggleCreateCustomerFields() {
        isCreatingCustomer = !isCreatingCustomer;
        $('.edd-bk-if-create-customer').toggle(isCreatingCustomer);
        $('.edd-bk-if-choose-customer').toggle(!isCreatingCustomer);
        $('#create-customer-error').text('');
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
               $('#customer-name').val('');
               $('#customer-email').val('');
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
        $('#service-info-msg-singular, #service-info-msg-plural, #service-info-bookings-disabled').hide();
        $('#service-info-loading').show();
        // Get selected service
        var serviceId = $('#service').val();
        // Stop if no service selected
        if (!serviceId || serviceId === '0') {
            $('#service-info-loading').hide();
            return;
        }
        // Create service instance and get info from server
        var service = new EddBk.Service(serviceId);
        service.loadData(function() {
            var meta = service.getData();
            if (meta.bookings_enabled) {
                // Get proper session unit string - pluralized if needed and translated
                var sessionUnitLabel = EddBk.Utils.UnitLabels[meta.session_unit],
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
            } else {
                $('#service-info-bookings-disabled').show();
            }
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

})(jQuery, moment, document, EddBkLocalized_BookingsEdit, EddBkLocalized_TimePickerI18n);
