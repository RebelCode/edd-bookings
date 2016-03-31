(function($) {
    
    $(document).ready(function() {
        // Move the Calendar View button to the header
        var title = $('#wpbody-content > .wrap > h1').first();
        $('a.edd-bk-calendar-view-link').appendTo(title).show();
    });
    
})(jQuery);
