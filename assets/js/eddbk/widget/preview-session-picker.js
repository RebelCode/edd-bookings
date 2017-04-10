;(function($, undefined) {

    /**
     * A service session picker that previews the availability of a service in memory, rather than actually showing a
     * working session picker for a service in the database.
     *
     * This works by sending the service meta data in the AJAX request that retrieves the sessions and sending a service
     * ID of -15. The server will recognize this ID and "mock" a service using the given meta data.
     *
     * An availability builder instance reference is used to dynamically retrieve the availability from the availability
     * builder.
     */
    EddBk.newClass('EddBk.Widget.PreviewSessionPicker', EddBk.Widget.ServiceSessionPicker, {
        /**
         * Constructor.
         *
         * @param {jQuery} element The session picker element.
         * @param {EddBk.Service} The service instance.
         */
        init: function(element, service, availBuilder) {
            // Ensure service has the preview ID
            service._id = -15;
            // Initialize using parent constructor
            this._super(element, service);
            // Set pointer to availability builder instance
            this.availBuilder = availBuilder;
        },

        /**
         * @override Includes the service data in the ajax call.
         */
        getMonthSessionsFromServer: function(month, year, callback) {
            var extra = {
                session_unit: this.getData('unit'),
                session_length: this.getData('sessionLength'),
                session_cost: this.getData('sessionCost'),
                min_sessions: this.getData('minSessions'),
                max_sessions: this.getData('maxSessions'),
                use_customer_tz: this.getData('useCustomerTz'),
                availability: this.availBuilder.eddBkAvailabilityBuilder('getAvailability')
            };
            this._super(month, year, callback, extra);
        },

        /**
         * Refreshes the widget and previewed availability.
         */
        refresh: function() {
            this.trigger('before_refresh_preview');

            // Clear and reset availability
            this.setData('monthsLoaded', {});
            this.getAvailability().setData({});
            // Deselect currently selected date and hide session options
            this.getDatePicker().setSelectedDate(null);
            this.toggleSessionOptions(false);
            // Update the widget
            this.update();

            this.trigger('refresh_preview');
        }
    });

})(jQuery);
