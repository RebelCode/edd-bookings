/* global edd_scripts, EddBkAjax */
;(function ($, undefined) {

    /**
     * Service class - represents an EDD Download that has bookings enabled.
     */
    EddBk.newClass('EddBk.Service', EddBk.Object, {
        /**
         * Constructor.
         *
         * @param {integer} id The ID of the service.
         * @returns {EddBk.Service} This instance.
         */
        init: function (id, data) {
            this._super(data);
            this._id = id;
            this._init();
        },
        _init: function () {},
        /**
         * Gets the service ID.
         *
         * @returns {integer} The ID.
         */
        getId: function () {
            return this._id;
        },
        /**
         * Loads the meta from an AJAX request to the server.
         *
         * @param {Function} callback The callback after meta has been loaded.
         * @returns {EddBk.Service} This instance.
         */
        loadData: function (callback) {
            var args = {
                service_id: this.getId()
            };
            EddBk.Ajax.post('get_meta', args, function (response, status, jqXHR) {
                var success = (response && response.success && response.meta);
                if (success) {
                    this.setData(this.normalizeAjaxMeta(response.meta));
                }
                if (callback) {
                    callback(response, success, jqXHR);
                }
                return success;
            }.bind(this));

            return this;
        },
        /**
         * Normalizes meta data retrieved via AJAX.
         *
         * @param {Object} meta The meta object.
         * @returns {Object} The normalized meta object.
         */
        normalizeAjaxMeta: function(meta) {
            // Make sure the customer timezone flag is a boolean
            if (meta.use_customer_tz !== undefined) {
                meta.use_customer_tz = (meta.use_customer_tz === "1" || meta.use_customer_tz);
            }
            return meta;
        },
        /**
         * Checks if the service has loaded its meta data.
         *
         * @returns {Boolean} True if the meta has been loaded, false if not.
         */
        isDataLoaded: function () {
            return Object.keys(this._data).length > 0;
        },
        /**
         * Gets the available sessions in a given range.
         *
         * @param {integer} start The starting unix timestamp.
         * @param {integer} start The ending unix timestamp.
         * @param {Function} callback The callback.
         * @returns {EddBk.Service} This instance.
         */
        getSessions: function (start, end, callback) {
            var args = {
                service_id: this.getId(),
                range_start: start,
                range_end: end
            };
            EddBk.Ajax.post('get_sessions', args, function(response) {
                callback(response.sessions);
            });
            return this;
        },
        /**
         * Gets whether or not a particular session can be booked for this service.
         *
         * @param {integer} start A timestamp
         * @param {integer} duration The number of seconds
         * @param {Function} callback A callback to invoke after the AJAX response is received.
         */
        canBook: function (start, duration, callback) {
            var args = {
                start: start,
                duration: duration
            };
            this.ajax('validate_booking', args, function (response, status, jqXHR) {
                var available = response && response.success && response.available;
                callback(available);
            }.bind(this));
        },
        /**
         * AJAX handler.
         *
         * @param {string} request The request string.
         * @param {object} args The arguments to send.
         * @param {Function} callback The function to call on response.
         * @returns {EddBk.Service} This instance.
         */
        ajax: function (request, args, callback) {
            var obj = {
                url: EddBk.Ajax.url,
                type: 'POST',
                data: {
                    action: 'edd_bk_service_request',
                    service_id: this.getId(),
                    request: request,
                    args: args
                },
                success: callback,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                }
            };
            $.ajax(obj);
            return this;
        }
    });

})(jQuery);
