;(function($) {

    var EddBkBookingsCalendar = function(element) {
        this.element = $(element);
        var dataSchedules = this.element.data('schedule');
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
                    data: {
                        action: 'edd_bk_get_bookings_for_calendar',
                        schedules: this.schedules
                    }
                }
            ]
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