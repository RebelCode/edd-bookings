;(function($) {

    var BOOKING_INFO_PANE_SELECTOR = '.edd-bk-bookings-calendar-info-pane';

    var EddBkBookingsCalendar = function(element) {
        this.element = $(element);
        this.referer = this.element.prev();
        this.nonce = this.referer.prev();
        this.infoPane = this.element.parent().find(BOOKING_INFO_PANE_SELECTOR);
        this.infoPaneInner = this.infoPane.find('> div');
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
        this.initFullCalendar();
    };
    
    EddBkBookingsCalendar.prototype.initFullCalendar = function() {
        var _this = this;
        var nonceData = {};
        nonceData[this.nonce.attr('name')] = this.nonce.attr('value');
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
            eventSources: [
                {
                    url: window.ajaxurl,
                    type: 'POST',
                    data: $.extend({
                        action: 'edd_bk_get_bookings_for_calendar',
                        schedules: this.schedules,
                    }, nonceData)
                }
            ],
            eventClick: function(event, jsEvent, view) {
                if (event.bookingId) {
                    $.ajax({
                        url: window.ajaxurl,
                        type: 'POST',
                        data: $.extend({
                            action: 'edd_bk_get_bookings_info',
                            bookingId: event.bookingId
                        }, nonceData),
                        success: function(response, status, xhr) {
                            if (response.output) {
                                _this.infoPaneInner.empty().html(response.output);
                                $('div#edd-bk-calendar-booking-info ' + BOOKING_INFO_PANE_SELECTOR).empty().html(response.output);
                            }
                        },
                        dataType: 'json'
                    });
                }
            },
            timeFormat: 'H:mm'
        }, this.options);
        this.element.fullCalendar(fullCalendarArgs);
    };

    $(document).ready(function() {
        window.eddBkCalendarInstances = {};
        $('div.edd-bk-bookings-calendar').each(function(i, obj) {
            eddBkCalendarInstances[i] = new EddBkBookingsCalendar(obj);
        });
    });

})(jQuery);