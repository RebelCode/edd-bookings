;(function($) {

    window.EddBk = window.EddBk || {};
    // FC config
    var EddBkFc = window.EddBkFc || {};
    EddBkFc = $.extend({
        theme: true,
        fesLinks: false
    }, EddBkFc);
    // Ajax URL
    EddBk.ajaxurl = (window.EddBkLocalized)
        ? EddBkLocalized.ajaxurl
        : ajaxurl;

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
        $(document).on('scroll', this.onScroll.bind(this));
    };
    
    EddBkBookingsCalendar.prototype.initFullCalendar = function() {
        var _this = this;
        this.nonceData = {};
        this.nonceData[this.nonce.attr('name')] = this.nonce.attr('value');
        this.nonceData[this.nonce.next().attr('name')] = this.nonce.next().attr('value');
        var fullCalendarArgs = $.extend({
            defaultView: 'month',
            theme: EddBkFc.theme,
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
            aspectRatio: 1.8,
            viewRender: this.onChangeView.bind(this),
            eventSources: [
                {
                    url: EddBk.ajaxurl,
                    type: 'POST',
                    data: $.extend({
                        action: 'edd_bk_get_bookings_for_calendar',
                        schedules: this.schedules,
                        fes: false || EddBkFc.fesLinks
                    }, this.nonceData),
                    async: true
                }
            ],
            eventClick: this.onEventClick.bind(this),
            selectable: true,
            selectConstraint: {
                start : '00:00',
                end : '24:00'
            },
            select: this.onDaySelect.bind(this),
            dayClick: this.onDayClick.bind(this),
            timeFormat: 'H:mm'
        }, this.options);
        this.element.fullCalendar(fullCalendarArgs);
    };
    
    EddBkBookingsCalendar.prototype.onDaySelect = function(start, end, jsEvent, view) {
        this.selectedDate = start;
    };
    
    EddBkBookingsCalendar.prototype.onDayClick = function(date, jsEvent, view) {
        if (this.selectedDate && this.selectedDate.isSame(date) && view.name !== 'agendaDay') {
            this.element.fullCalendar('changeView', 'agendaDay');
            this.element.fullCalendar('gotoDate', date);
        }
    };
    
    EddBkBookingsCalendar.prototype.onEventClick = function(event, jsEvent, view) {
        if (event.bookingId) {
            var target = $(jsEvent.currentTarget);
            this.modalContent.empty().html('<i class="fa fa-spinner fa-spin"></i> Loading');
            var position = this.calculateModalPosition(jsEvent, BOOKING_INFO_MODAL_OFFSET);
            this.modal.css(position).show();
            
            $.ajax({
                url: EddBk.ajaxurl,
                type: 'POST',
                data: $.extend({
                    action: 'edd_bk_get_bookings_info',
                    bookingId: event.bookingId,
                    fesLinks: false || EddBkFc.fesLinks
                }, this.nonceData),
                success: function(response, status, xhr) {
                    if (response.output) {
                        this.modalContent.empty().html(response.output);
                        var position = this.calculateModalPosition(jsEvent, BOOKING_INFO_MODAL_OFFSET);
                        this.modal.css(position).show();
                    }
                }.bind(this),
                dataType: 'json'
            });
            // Stop propagation. Otherwise it will propagate to our document click handler
            jsEvent.stopPropagation();
        }
    };
    
    EddBkBookingsCalendar.prototype.calculateModalPosition = function(jsEvent, offset) {
        // Window
        var win = $(window);
        var winWidth = win.width();
        var winHeight = win.height();
        // Modal size
        var modalWidth = this.modal.outerWidth();
        var modalHeight = this.modal.outerHeight();
        // Ensure it fits in window
        if (modalWidth > winWidth) {
            this.modal.css('width', winWidth);
        }
        if (modalHeight > winHeight) {
            this.modal.css('height', winHeight);
        }
        // Get target pos and size
        var targetPos = {
            x: jsEvent.clientX,
            y: jsEvent.clientY
        };
        // Calculate position of modal
        var pos = {};
        pos.x = targetPos.x + offset.x;
        if ((pos.x + modalWidth) > winWidth) {
            pos.x = winWidth - modalWidth;
        }
        pos.y = targetPos.y + offset.y;
        if ((pos.y + modalHeight) > winHeight) {
            pos.y = winHeight - modalHeight;
        }
        return {
            top: pos.y,
            left: pos.x
        };
    };
    
    EddBkBookingsCalendar.prototype.onChangeView = function(view, element) {
        $('.fc-scroller').off('scroll').on('scroll', this.onScroll.bind(this));
        this.selectedDate = null;
        this.element.fullCalendar('unselect');
    };
    
    EddBkBookingsCalendar.prototype.onScroll = function() {
        this.modal.hide();
    };

    $(document).ready(function() {
        window.eddBkCalendarInstances = {};
        $('div.edd-bk-bookings-calendar').each(function(i, obj) {
            eddBkCalendarInstances[i] = new EddBkBookingsCalendar(obj);
        });
    });

})(jQuery);