/* global EddBk, top, EddBkAjaxLocalized */

;(function($, window, document, undefined) {

    EddBk.Utils = {
        // Time units and their respective keys
        Units: {
            minutes: 'minutes',
            hours: 'hours',
            days: 'days',
            weeks: 'weeks'
        },
        // Time units and their respective lengths in seconds
        UnitLengths: {
            minutes: 60,
            hours: 3600,
            days: 86400,
            weeks: 604800
        },
        // Month names
        months: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ],
        /**
         * Returns the ordinal suffix for the given number.
         *
         * @param  {number} n The number
         * @return {string}   The ordinal suffix
         */
        ordSuffix: function (n) {
            var u = n % 10;
            switch (u) {
                case 1:
                    return (n === 11) ? 'th' : 'st';
                case 2:
                    return (n === 12) ? 'th' : 'nd';
                case 3:
                    return (n === 13) ? 'th' : 'rd';
            }
            return 'th';
        },
        /**
         * Uppercases the first letter of the string.
         *
         * @param  {string} str The string
         * @return {string}
         */
        ucfirst: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        /**
         * Generates a pluralized string using the given string and number.
         * The resulting is in the form:
         * n str(s)
         *
         * @param  {string} str The string to optionally pluralize.
         * @param  {number} n   The number to use to determine if pluralization is requred.
         * @return {string}     A string in the form: "n str(s)" where "(s)" denotes an option "s" character.
         */
        pluralize: function (str, n) {
            var newStr = str.toLowerCase().charAt(str.length - 1) === 's' ? str.slice(0, -1) : str;
            if (n !== 1)
                newStr += 's';
            return n + ' ' + newStr;
        },
        // Checks if the argument string is a time unit string name
        isTimeUnit: function(something) {
            return [EddBk.Utils.Units.hours, EddBk.Utils.Units.minutes].indexOf(something) !== -1;
        },
        // Converts milliseconds into seconds, i.e. a proper timestamp
        msToSeconds: function(ms) {
            return Math.floor(ms / 1000);
        },
        // Creates a UTC timestamp
        utcTimestamp: function(year, month, date, hours, minutes, seconds, ms) {
            var d = Date.UTC(year, month, date, hours || 0, minutes || 0, seconds || 0, ms || 0);
            return EddBk.Utils.msToSeconds(d);
        },
        // Gets the timezone offset of a date
        timezone: function(date) {
            if (date === undefined) date = new Date();
            return date.getTimezoneOffset() * (-60);
        },
        // jQuery plugin utility functions
        jqp: {
            fn: function (plugin, args) {
                var method = null;
                var options = null;
                // Check if given arg is an existing method
                if (plugin.methods[args]) {
                    // If so, run it while passing all but the first argument (which is the method name)
                    method = plugin.methods[args];
                    options = Array.prototype.slice.call(arguments, 1);
                }
                // If the given first arg is an object or not given at all, call "constructor"
                if (typeof args === 'object' || !args) {
                    method = plugin.methods.init;
                    options = arguments;
                }
                // Invoke method with options
                if (method !== null && options !== null) {
                    if (method !== plugin.methods.init && method !== plugin.methods._checkInit && plugin.methods['_checkInit']) {
                        window.EddBk.Utils.jqp.call(this, '_checkInit');
                    }
                    return window.EddBk.Utils.jqp.call(this, method, options);
                }
                // If all checks fail, throw error
                $.error('Method ' + args + 'does not exist for ' + plugin.namespace);
            },
            namespace: 'EddBk',
            defaults: {
                isInit: false
            },
            methods: {
                /**
                 * Initializes the instance.
                 *
                 * @returns {Array} This instance.
                 */
                init: function () {
                    return this;
                },
                /**
                 * Checks if the instance is initialized. If not, init() is invoked.
                 * @returns {undefined}
                 */
                _checkInit: function () {
                    if (!getOption(this, 'isInit')) {
                        window.EddBk.Utils.call(this, 'init');
                    }
                }
            },
            /**
             * Calls a method for an instance context.
             *
             * @param {Object} instance The "this" instance context.
             * @param {string|Function} method A function object or method name string, referring to the index of a jQuery plugin's "methods" object
             * @returns {mixed} The return value of the called method.
             */
            call: function (instance, method) {
                var args = Array.prototype.slice.call(arguments, 2);
                var fn = (typeof method === 'string') ? this.methods[method] : method;
                return fn.apply(instance, args);
            },
            /**
             * Gets an object's data set, or an entry from that data set.
             *
             * @param {Object} instance The object instance.
             * @param {string} name Optional index name of entry to get.
             * @returns {mixed} The data set, or the entry with key "name" if given.
             */
            getData: function (instance, name) {
                var data = $(instance).data(this.namespace);
                if (!data) {
                    data = $.extend({}, this.defaults);
                    $(instance).data(this.namespace, data);
                }
                return (typeof name === 'string')
                    ? data[name]
                    : data;
            },
            /**
             * Sets an object's data set, or an entry in that data set.
             *
             * @param {Object} instance The object instance.
             * @param {string|Object} name The data set to set to the object, or the name of the entry to set.
             * @param {type} value The value of the entry to set, if the "name" param is a string.
             * @returns {Object} The instance.
             */
            setData: function (instance, name, value) {
                var data = window.EddBk.Utils.jqp.getData.apply(this, [instance]);
                if (typeof name === 'object') {
                    $.extend(data, name);
                } else {
                    data[name] = value;
                }
                return $(instance).data(this.namespace, data);
            }
        },
        getEvents: function (el) {
            var $this = $(el);
            if (typeof ($._data) === 'function') {
                return $._data($this.get(0), 'events') || {};
            } else if (typeof ($this.data) === 'function') { // jQuery version < 1.7.?
                return $this.data('events') || {};
            }
            return {};
        },
        preBind: function (el, type, data, fn) {
            $(el).each(function () {
                var $this = $(this);
                $this.bind(type, data, fn);
                var currentBindings = EddBk.Utils.getEvents($this)[type];
                if ($.isArray(currentBindings)) {
                    currentBindings.unshift(currentBindings.pop());
                }
            });
            return this;
        }
    };

})(jQuery, top, document);
