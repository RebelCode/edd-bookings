;(function($, local) {

    local = $.extend({
        theme: true,
        fesLinks: false
    }, local);

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
        this.nonceData[this.nonce.next().attr('name')] = this.nonce.next().attr('value');
        var fullCalendarArgs = $.extend({
            defaultView: 'month',
            theme: local.theme,
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
                    url: EddBk.Ajax.url,
                    type: 'POST',
                    data: $.extend({
                        action: 'edd_bk_get_bookings_for_calendar',
                        schedules: this.schedules,
                        fes: false || local.fesLinks
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
                url: EddBk.Ajax.url,
                type: 'POST',
                data: $.extend({
                    action: 'edd_bk_get_bookings_info',
                    bookingId: event.bookingId,
                    fesLinks: false || local.fesLinks
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
        // Reset position and show temporarily
        // Must be visible for offset parent to be correctly determined
        this.modal.css({
            top: 0,
            left: 0
        }).show();

        // Get parent and its offset
        var parent = this.modal.offsetParent(),
            parentOffset = parent.offset();

        // Calculate target position
        var targetPos = {
            x: jsEvent.pageX + offset.x,
            y: jsEvent.pageY + offset.y
        };

        // Get modal size
        var modalSize = {
            width: this.modal.outerWidth(),
            height: this.modal.outerHeight()
        };
        // Calculate bottom right point of modal
        var modalBounds = {
            x: targetPos.x + modalSize.width,
            y: targetPos.y + modalSize.height
        };

        // The modal must be hidden to calculate the window size, in the event the modal causes
        // the window to grow in size (such as result in horizontal scroll).
        this.modal.hide();

        // Get window size
        var winSize = {
            width: $(window).outerWidth(),
            height: $(window).outerHeight()
        };
        // Keep inside window
        if (modalBounds.x > winSize.width) {
            targetPos.x = winSize.width - modalSize.width;
        }
        if (modalBounds.y > winSize.height) {
            targetPos.y = winSize.height - modalSize.height;
        }

        // Return relative to parent
        return {
            top: targetPos.y - parentOffset.top,
            left: targetPos.x - parentOffset.left
        };
    };
    
    EddBkBookingsCalendar.prototype.onChangeView = function(view, element) {
        this.selectedDate = null;
        this.element.fullCalendar('unselect');
    };

    $(document).ready(function() {
        window.eddBkCalendarInstances = {};
        $('div.edd-bk-bookings-calendar').each(function(i, obj) {
            eddBkCalendarInstances[i] = new EddBkBookingsCalendar(obj);
        });
    });

})(jQuery, EddBkLocalized_BookingsCalendar);
