;(function($)
{
    
    var schedule, editLink, linkFormat, selector;
    
    var init = function()
    {
        // Only continue only schedule edit page
        if (!$(document.body).is('.post-type-edd_bk_schedule.post-php') && $('div.edd-bk-schedule').length === 0) {
            return;
        }
        // Init element pointers
        schedule = $('div.edd-bk-schedule'),
            editLink = schedule.find('a.edd-bk-edit-availability'),
            linkFormat = editLink.attr('href'),
            selector = schedule.find('select');

        // Selector on change event - update link
        selector.on('change', updateAvailabilityLink);
        // Update link first time
        updateAvailabilityLink();
    };
    
    /**
     * Updates the edit link
     */
    var updateAvailabilityLink = function()
    {
        var id = selector.find('option:selected').val(),
            link = linkFormat.replace('%s', id);
        editLink
            .toggle(id !== 'new')
            .attr('href', link);
    };
    
    $(document).ready(init);
    
})(jQuery);