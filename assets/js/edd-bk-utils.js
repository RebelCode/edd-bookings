;(function ($) {

    window.EddBk = window.EddBk || {};

    /**
     * Utilities
     */
    window.EddBk.utils = {
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
                        window.EddBk.utils.jqp.call(this, '_checkInit');
                    }
                    return window.EddBk.utils.jqp.call(this, method, options);
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
                        window.EddBk.utils.call(this, 'init');
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
                var data = window.EddBk.utils.jqp.getData.apply(this, [instance]);
                if (typeof name === 'object') {
                    $.extend(data, name);
                } else {
                    data[name] = value;
                }
                return $(instance).data(this.namespace, data);
            }
        }
    };

})(jQuery);
