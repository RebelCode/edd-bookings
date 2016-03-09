(function ($) {

    var BookableDownload = function (element) {
        this.element = $(element);
        this.initScope();
        this.initElements();
        this.initDatepicker();
    };

    /**
     * Initializes element pointers.
     */
    BookableDownload.prototype.initElements = function () {
        this.datepickerContainer = this.element.find('.edd-bk-datepicker-container');
        this.datepickerAltField = this.element.find('.edd-bk-datepicker-value');
        this.datepicker = this.datepickerContainer.find('.edd-bk-datepicker');
        this.messagesContainer = this.element.find('.edd-bk-messages');
        this.eddSubmitWrapper = this.element.find('.edd_purchase_submit_wrapper');
        this.noTimesForDateElement = this.element.find('.edd-bk-no-times-for-date');
        this.timepickerDuration = this.element.find('.edd_bk_duration');
        this.datefixElement = this.element.find('.edd-bk-datefix-msg');
        this.invalidDateElement = this.element.find('.edd-bk-invalid-date-msg');
        this.sessionUnavailableMessage = this.element.find('.edd-bk-unavailable-msg');
        this.priceElement = this.element.find('p.edd-bk-price span');
        this.timezone = this.element.find('.edd-bk-timezone');
    };

    /**
     * Initializes the scope and retrieves the ID of this service.
     */
    BookableDownload.prototype.initScope = function () {
        if ($('div.edd_downloads_list').length > 0) {
            // Look for EDD containers. Case for multiple downloads in one page
            this.eddContainer = this.element.closest('div.edd_download');
            this.serviceId = this.eddContainer.attr('id').substr(this.eddContainer.attr('id').lastIndexOf('_') + 1);
        } else if (this.element.is('.edd_download_purchase_form')) {
            // Look for EDD containers. Case for download [purchase_link] shortcode
            this.eddContainer = this.element;
            this.serviceId = this.eddContainer.attr('id').substr(this.eddContainer.attr('id').lastIndexOf('_') + 1);
        } else {
            // Look for id in the body tag. Case for a single download page
            this.serviceId = parseInt((document.body.className.match(/(?:^|\s)postid-([0-9]+)(?:\s|$)/) || [0, 0])[1]);
            if (!this.serviceId) {
                throw "Failed to initialize scope!";
            }
            this.eddContainer = this.element.closest('article');
        }
    };
    
    /**
     * Gets the data for this service.
     * 
     * @param {Function} callback The callback to call when the response is received.
     */
    BookableDownload.prototype.getData = function (callback) {
        this.ajax({
            data: {
                action: 'get_edd_bk_data',
                post_id: this.serviceId
            },
            success: function (response, status, jqXHR) {
                this.data = response;
                if (typeof callback !== 'undefined') {
                    callback();
                }
            }.bind(this)
        });
    };
    
    /**
     * Generic AJAX function.
     * 
     * @param {object} obj Optional object containing the AJAX params.
     */
    BookableDownload.prototype.ajax = function (obj) {
        obj = typeof obj === 'undefined' ? {} : obj;
        obj.dataType = 'json';
        obj.xhrFields = {withCredentials: true};
        obj.url = edd_scripts.ajaxurl;
        obj.type = 'POST';
        $.ajax(obj);
    };
    
    /**
     * Initializes the datepicker.
     */
    BookableDownload.prototype.initDatepicker = function () {
        var datepickerFunction = BookableDownload.determineDatepickerFunction();

        // Check if the range has been given. Default to the session duration
        var range = 1;
        /*
        if ( _.isUndefined(range) ) {
            range = this.data.meta.session_length;
        }
        // Get the session duration unit
        var unit = this.data.meta.session_unit.toLowerCase();
        // Check which datepicker function to use, depending on the unit
        var pickerFn = Utils.getDatePickerFunction( unit );
        // Stop if the datepicker function returned is null
        if ( pickerFn === null ) return;
        // Set range to days, if the unit is weeks
        if ( unit === 'weeks' ) range *= 7;
        */

        var options = {
            // Hide the Button Panel
            showButtonPanel: false,
            // Options for multiDatePicker. These are ignored by the vanilla jQuery UI datepicker
            mode: 'daysRange',
            autoselectRange: [0, range],
            adjustRangeToDisabled: true,
            // Alt field
            altField: this.datepickerAltField,
            // altFormat: '@',
            showOtherMonths: true,
            // Prepares the dates for availability
            // beforeShowDay: this.datepickerIsDateAvailable.bind(this),
            // When a date is selected by the user
            // onSelect: this.datepickerOnSelectDate.bind(this),
            // When the month of year changes
            // onChangeMonthYear: this.datepickerOnChangeMonthYear.bind(this)
        };

        // Apply the datepicker function on the HTML datepicker element
        $.fn[datepickerFunction].apply(this.datepicker, [options]);
    };

    BookableDownload.determineDatepickerFunction = function () {
        return 'datepicker';
    };

    $(document).ready(function () {
        var instances = {};
        $('.edd-bk-service-container').each(function (i, elem) {
            var instance = new BookableDownload(elem);
            if (instance.id !== null) {
                instances[i] = instance;
            }
        });
        window.eddBkInstances = instances;
    });

})(jQuery);
