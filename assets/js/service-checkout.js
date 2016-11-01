(function($, window, document, undefined) {
    
    EddBk.newClass('EddBk.CheckoutForm', EddBk.Object, {
        init: function(element) {
            // Element pointer and index
            this.modalElement = $(element);
            this.index = this.modalElement.data('cart-index');
            // Session picker element
            this.sessionPickerElement = this.modalElement.find('.edd-bk-service-session-picker');
            // Service instance
            this.service = new EddBk.Service(this.sessionPickerElement.data('service-id'));
            this.sessionPicker = new EddBk.Widget.ServiceSessionPicker(this.sessionPickerElement, this.service);
            // Instance loads content after it loads the service data
            this.sessionPicker.loadData(this.sessionPicker.loadContent.bind(this.sessionPicker));

            this.initElements();
            this.initEvents();
        },
        // Initializes the element pointers
        initElements: function () {
            this.trigger = $('.edd-bk-show-modal-' + this.index);
            this.btn = this.modalElement.find('.edd-bk-edit-cart-item-session').css('display', 'block').hide();

            return this;
        },
        // Initializes the events
        initEvents: function () {
            this.trigger.click(this.showModal.bind(this));
            // Show/hide the add to cart button depending on the validity of the selected session
            this.sessionPicker.on('update', this.onUpdate.bind(this));
            // On button click
            this.btn.on('click', this.onSubmit.bind(this));

            return this;
        },
        // Shows the modal
        showModal: function() {
            this.modalElement.modal('show');
        },
        // Triggered on update
        onUpdate: function () {
            this.btn.toggle(this.sessionPicker.getSelectedSession() !== null);
            this.btnSpinner = this.btn.find('.edd-loading');
        },
        // Triggered on button click, which submits the session edit
        onSubmit: function(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            this.toggleButtonSpinner(true);
            
            this.sessionPicker.validate(function(valid) {
                if (valid) {
                    var session = this.sessionPicker.getSelectedSession();
                    // Edit cart item and refresh page`
                    EddBk.Ajax.ajax('post', 'edit_cart_item_session', {
                        index: this.index,
                        service: this.service.getId(),
                        session: {
                            start: session.start,
                            duration: session.duration,
                            timezone: session.timezone
                        }
                    }, function() { window.location.reload(); });
                } else {
                    // Re-enable the button
                    // If not valid, the session picker will show the "unavailable session" message.
                    this.btn.removeAttr('data-edd-loading')
                        .prop('disabled', false);
                }
            }.bind(this));
        },
        toggleButtonSpinner: function(toggle) {
            if (toggle) {
                // Update spinner
                this.btnSpinner.css({
                    'margin-left': this.btnSpinner.width() / -2,
                    'margin-top': this.btnSpinner.height() / -2
                });
                // Disable button, preventing rapid additions to cart during ajax request and show the spinner
                this.btn
                    .prop('disabled', true)
                    .attr('data-edd-loading', '');
            } else {
                // Re-enable the button
                // If not valid, the session picker will show the "unavailable session" message.
                this.btn.removeAttr('data-edd-loading')
                    .prop('disabled', false);
            }
        }
    });
    
    // Initializes instances when the DOM is ready
    $(document).ready(function () {
        // Create and save instances
        EddBk.CheckoutForm.instances = autoCreateInstances($('.edd-bk-modal'));
    });

    /**
     * Creates all the required session picker widget instances for the current page.
     *
     * @param {jQuery} jq The jQuery element selector object.
     * @returns {Array} An array of created instances.
     */
    function autoCreateInstances(jq) {
        var instances = [];

        // Create instances
        jq.each(function (i, l) {
            var $l = $(l),
                instance = new EddBk.CheckoutForm($l);
            instances.push(instance);
        });

        return instances;
    }
    
})(jQuery, top, document);
