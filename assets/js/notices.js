(function($) {
    $(window).load(function() {
        // On notice dismiss button click
        $('.notice-eddbk button').click(function() {
            // Get the notice and the action
            var notice = $(this).parent();
            var action = notice.data('action');
            // If action is specified
            if (action.length > 0) {
                // Send AJAX request
                $.ajax({
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: action,
                        notice: notice.attr('id')
                    }
                });
            }
        });
    });
})(jQuery);
