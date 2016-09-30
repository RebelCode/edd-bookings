EddBkPurchaseFormSessionPicker = (function($) {
    
    function EddBkPurchaseFormSessionPicker(elem) {
        this.l = elem;
        this.service = null;
        this.container = null;
        this.sessionPicker = null;
    }
    
    EddBkPurchaseFormSessionPicker.prototype = {
        construct: EddBkPurchaseFormSessionPicker,
        init: function() {
            this.initScope();
            
            this.service = new EddBkService(this.serviceId);
            this.sessionPicker = new EddBkSessionPicker(this.service, this.l);
            this.sessionPicker.init();
            
            this.initElements();
            this.setupEvents();
        },
        initScope: function() {
            // Determine the scope
            var scope = this.determineScope(this.l);
            // Check ID
            if (scope.serviceId !== null) {
                this.serviceId = scope.serviceId;
            } else {
                throw "Failed to init scope ID";
            }
            // Check container
            if (scope.container !== null) {
                this.container = scope.container;
            } else {
                throw "Failed to init scope container";
            }
        },
        initElements: function() {
            this.submitWrapper = this.l.parent().find('.edd_purchase_submit_wrapper');
            // Hide EDD quantity field
            this.l.parent().find('div.edd_download_quantity_wrapper').hide();
            // Change EDD cart button text
            this.l.parent().find('.edd-add-to-cart-label').text("Purchase");
            // Hide the submit button
            this.hideSubmit();
            
            if (this.submitWrapper.length) {
                var _this = this;
                this.addToCart = this.submitWrapper.find('.edd-add-to-cart.edd-has-js');
                // Our intercepting callback function
                var cb = function (e) {
                    // Get parent form
                    var targetForm = $(e.target).parents('form.edd_download_purchase_form');
                    // Check if in the same form as the add to cart button/link
                    if (targetForm.length === 0 || targetForm.closest('form')[0] !== _this.addToCart.closest('form')[0]) {
                        return;
                    }
                    _this.onSubmit(e, _this.addToCart, eddBkGlobals.eddHandler.bind(_this.addToCart));
                };
                // Add our click and submit bindings
                this.addToCart.unbind('click').click(cb);
                this.addToCart.closest('form').on('submit', cb);
            }
        },
        determineScope: function(elem) {
            var serviceId = null;
            var eddContainer = [];
            if (elem.parents('div.edd_downloads_list').length > 0) {
                // Look for EDD containers. Case for multiple downloads in one page
                eddContainer = this.l.closest('div.edd_download');
                serviceId = eddContainer.attr('id').substr(eddContainer.attr('id').lastIndexOf('_') + 1);
            } else if (elem.parents('.edd_download_purchase_form').length > 0) {
                // Look for EDD containers. Case for download [purchase_link] shortcode
                eddContainer = elem.closest('.edd_download_purchase_form');
                serviceId = eddContainer.attr('id').substr(eddContainer.attr('id').lastIndexOf('_') + 1);
            }
            // Strip any non-id parts from the id
            if (serviceId !== null) {
                var dash = serviceId.indexOf('-');
                if (dash !== -1) {
                    serviceId = serviceId.substr(0, dash);
                }
            } else {
                // Look for id in the body tag. Case for a single download page
                serviceId = parseInt((document.body.className.match(/(?:^|\s)postid-([0-9]+)(?:\s|$)/) || [0, 0])[1]);
                if (!serviceId) {
                    serviceId = null;
                }
                eddContainer = elem.closest('article');
            }

            return {
                serviceId: serviceId,
                container: eddContainer,
            };
        },
        setupEvents: function() {
            this.sessionPicker
                .on('before_date_selected', this.hideSubmit.bind(this))
                .on('before_duration_changed_date', this.hideSubmit.bind(this))
                .on('after_show_timepicker', this.showSubmit.bind(this))
                .on('duration_changed_date', function(event, valid) {
                    if (valid) this.showSubmit();
                    return valid;
                }.bind(this))
                // Marketify - on update price for session picker, update other prices on the page
                .on('updated_price', function(event) {
                    var serviceId = event.instance.service.getId();
                    var text = event.result;
                    $('[id="edd_price_'+serviceId+'"]').html(text);
                }.bind(this))
            ;
        },
        showSubmit: function() {
            this.submitWrapper.show();
        },
        hideSubmit: function() {
            this.submitWrapper.hide();
        },
        onSubmit: function(ev, $this, callback) {
            ev.preventDefault();
            // Disable button, preventing rapid additions to cart during ajax request
            $this.prop('disabled', true);

            // Update spinner
            var $spinner = $this.find('.edd-loading');
            var spinnerWidth = $spinner.width(),
                spinnerHeight = $spinner.height();
            $spinner.css({
                'margin-left': spinnerWidth / -2,
                'margin-top': spinnerHeight / -2
            });
            // Show the spinner
            $this.attr('data-edd-loading', '');

            // Hide the unavailable message
            this.sessionPicker.resetMessages();
            // Validate selected session
            this.sessionPicker.validateSelectedSession(function (response, status, xhr) {
                if (response && response.success && response.available) {
                    // EDD should take it from here ...
                    callback(ev);
                } else {
                    // Hide loading spinners and re-enable button
                    $this.removeAttr('data-edd-loading');
                    $this.prop('disabled', false);
                    // Show message
                    this.sessionPicker.getMessage('booking-unavailable-msg').show();
                }
            }.bind(this));
        }
    };
    
    return EddBkPurchaseFormSessionPicker;
    
})(jQuery);
