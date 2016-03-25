(function($) {
    
    $(document).ready(function() {
        // Move the Calendar View button to the header
        var lastAction = $('h1 a.page-title-action').last();
        $('a.edd-bk-calendar-view-link').insertAfter(lastAction).show();
    });
    
})(jQuery);
