;(function($) {

    var BOOKING_INFO_SELECTOR = '.edd-bk-bookings-calendar-info';
    var BOOKING_INFO_MODAL_OFFSET = {
        x: 0,
        y: 1
    };

    var EddBkBookingsCalendar = function(element) {
        this.element = $(element);
        this.referer = this.element.prev();
        this.nonce = this.referer.prev();
        this.modal = this.element.parent().find(BOOKING_INFO_SELECTOR);
        this.modalContent = this.modal.find('> div');
        var dataSchedules = this.element.data('schedules');
        if (typeof dataSchedules === 'undefined') {
            dataSchedules = '0';
        }
        this.schedules = ('' + dataSchedules).split(',');
        var dataOptions = this.element.data('options');
        this.options = (typeof dataOptions === 'undefined')
            ? {}
            : dataOptions;
        if (typeof this.options === 'string') {
            this.options = $.parseJSON(this.options);
        }
        this.initEvents();
        this.initFullCalendar();
    };
    
    EddBkBookingsCalendar.prototype.initEvents = function() {
        $(document).click(function(event) {
            if (!$.contains(this.modal[0], event.target)) {
                this.modal.hide();
            }
        }.bind(this));
    };
    
    EddBkBookingsCalendar.prototype.initFullCalendar = function() {
        var _this = this;
        this.nonceData = {};
        this.nonceData[this.nonce.attr('name')] = this.nonce.attr('value');
        var fullCalendarArgs = $.extend({
            defaultView: 'month',
            header: {
                left: 'today prev,next',
                center: 'title',
                right: 'agendaDay,agendaWeek,month'
            },
            views: {
                basic: {},
                agenda: {},
                week: {},
                day: {}
            },
            aspectRatio: 2.2,
            viewRender: function(view, element) {
                $('.fc-scroller').off('scroll').on('scroll', function() {
                    this.modal.hide();
                }.bind(this));
            }.bind(this),
            eventSources: [
                {
                    url: window.ajaxurl,
                    type: 'POST',
                    data: $.extend({
                        action: 'edd_bk_get_bookings_for_calendar',
                        schedules: this.schedules,
                    }, this.nonceData),
                    async: true
                }
            ],
            eventClick: this.onEventClick.bind(this),
            timeFormat: 'H:mm'
        }, this.options);
        this.element.fullCalendar(fullCalendarArgs);
    };
    
    EddBkBookingsCalendar.prototype.onEventClick = function(event, jsEvent, view) {
        if (event.bookingId) {
            var target = $(jsEvent.currentTarget);
            this.modalContent.empty().html('<i class="fa fa-spinner fa-spin"></i> Loading');
            var position = this.calculateModalPosition(target, BOOKING_INFO_MODAL_OFFSET);
            this.modal.css(position).show();
            
            $.ajax({
                url: window.ajaxurl,
                type: 'POST',
                data: $.extend({
                    action: 'edd_bk_get_bookings_info',
                    bookingId: event.bookingId
                }, this.nonceData),
                success: function(response, status, xhr) {
                    if (response.output) {
                        this.modalContent.empty().html(response.output);
                        var position = this.calculateModalPosition(target, BOOKING_INFO_MODAL_OFFSET);
                        this.modal.css(position).show();
                    }
                }.bind(this),
                dataType: 'json'
            });
            // Stop propagation. Otherwise it will propagate to our document click handler
            jsEvent.stopPropagation();
        }
    };
    
    EddBkBookingsCalendar.prototype.calculateModalPosition = function(target, offset) {
        // Window
        var win = $(window);
        var winWidth = win.width();
        var winHeight = win.height();
        // Modal size
        var modalWidth = this.modal.outerWidth();
        var modalHeight = this.modal.outerHeight();
        // Ensure it fits in window
        if (modalWidth > winWidth) {
            this.modal.width(winWidth);
        }
        if (modalHeight > winHeight) {
            this.modal.height(winHeight);
        }
        // Get target pos and size
        var targetPos = target.offset();
        var targetWidth = target.outerWidth();
        var targetHeight = target.outerHeight();
        // Calculate position of modal
        var posX = ((targetPos.left + modalWidth + offset.x) <= winWidth)
                ? targetPos.left + offset.x
                : targetPos.left + targetWidth - modalWidth + offset.x;
        var posY = ((targetPos.top + targetHeight + modalHeight + offset.y) <= winHeight)
                ? targetPos.top + targetHeight + offset.y
                : targetPos.top - modalHeight - offset.y;
        return {
            top: posY,
            left: posX
        };
    };

    $(document).ready(function() {
        window.eddBkCalendarInstances = {};
        $('div.edd-bk-bookings-calendar').each(function(i, obj) {
            eddBkCalendarInstances[i] = new EddBkBookingsCalendar(obj);
        });
    });

})(jQuery);