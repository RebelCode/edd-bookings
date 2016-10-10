/* global EddBk */

;(function (undefined) {
    /*
     * A session controller - any object that can assert whether or not a particular session is available for booking
     * or not.
     *
     * The mechanics of how it decides this is not important, as long as it can give a boolean output for a session
     * instance input.
     */
    EddBk.newClass('EddBk.Session.Controller', EddBk.Object, {
        /**
         * Checks if a given session is available for booking.
         *
         * @param {EddBk.Object.Session} session The session object instance.
         * @returns {boolean} True if the session is available for booking; false if not.
         */
        isSessionAvailable: function(session) {
            throw this.class + " extends `EddBk.Object.SessionController` and does not implement the `" + arguments.callee.name + "` method.";
        }
    });
})();
