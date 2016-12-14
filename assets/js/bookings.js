(function($, remote) {
    
    $(document).ready(function() {
        // Move the Calendar View button to the header
        var title = $('#wpbody-content > .wrap > h1').first();
        $('a.edd-bk-calendar-view-link').appendTo(title).show();
        // Replace "Published" with "Confirmed"
        var publishStatusLink = $('ul.subsubsub li.publish a'),
            childSpan = publishStatusLink.find('> span').detach();
        publishStatusLink.text(remote.Confirmed + " ").append(childSpan);
    });
    
})(jQuery, EddBkLocalized_AdminBookings);
