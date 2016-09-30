/* global edd_scripts */

/**
 * Constructor.
 * 
 * @param {integer} id The ID of the service.
 * @returns {EddBkService} This instance.
 */
function EddBkService(id) {
    this.id = id;
    this.meta = {};
    this.ajaxurl = window.ajaxurl
        ? window.ajaxurl
        : edd_scripts.ajaxurl;
}

/**
 * The prototype object of the EddBkService object type.
 * 
 * @type object
 */
EddBkService.prototype = {
    /**
     * Constructor.
     * 
     * @param {integer} id The ID of the service.
     * @returns {EddBkService} This instance.
     */
    construct: EddBkService,
    /**
     * Gets the service ID.
     * 
     * @returns {integer} The ID.
     */
    getId: function () {
        return this.id;
    },
    /**
     * Gets the meta object or an entry in the meta.
     * 
     * @param {string} key Optional key to retrieve a specific entry from the meta.
     * @returns {object|undefined} The meta object if key was not given, the entry with the given key or undefined if
     *  the key does not exist in the meta.
     */
    getMeta: function (key) {
        return (typeof key === 'undefined')
            ? this.meta
            : this.meta[key];
    },
    /**
     * Sets the meta.
     * 
     * @param {object} meta The meta object.
     * @returns {EddBkService} This instance.
     */
    setMeta: function (meta) {
        this.meta = meta;
        this.meta.use_customer_tz = (this.meta.use_customer_tz === "1" || this.meta.use_customer_tz);
        return this;
    },
    /**
     * Loads the meta from an AJAX request to the server.
     * 
     * @param {Function} callback The callback after meta has been loaded.
     * @returns {EddBkService} This instance.
     */
    loadMeta: function (callback) {
        this.ajax('get_meta', {}, function (response, status, jqXHR) {
            var success = (response && response.success && response.meta);
            if (success) {
                this.setMeta(response.meta);
            }
            if (typeof callback === 'function') {
                callback(response, success, jqXHR);
            }
            return success;
        }.bind(this));
        
        return this;
    },
    /**
     * Checks if the service has loaded its meta data.
     * 
     * @returns {Boolean} True if the meta has been loaded, false if not.
     */
    isMetaLoaded: function() {
        return Object.keys(this.meta).length > 0;
    },
    /**
     * Gets the available sessions in a given range.
     * 
     * @param {array} range An array with two values: the start and end of the range as unix timestamps.
     * @param {Function} callback The callback.
     * @returns {EddBkService} This instance.
     */
    getSessions: function (range, callback) {
        var args = {
            range_start: range[0],
            range_end: range[1]
        };
        this.ajax('get_sessions', args, callback);
        return this;
    },
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
     * @returns {EddBkService} This instance.
     */
    ajax: function (request, args, callback) {
        var obj = {
            url: this.ajaxurl,
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
        jQuery.ajax(obj);
        return this;
    }
};

