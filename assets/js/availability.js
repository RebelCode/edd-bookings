/**
 * Converts an element into a JS-function availability interface.
 * 
 * @param {Element} element The element
 */
function eddBkAvailability(element) {
    $ = jQuery;
    element = $(element);
    
    // Pointer to the Add Rule button
    var addRuleButton = element.find('tfoot td.edd-bk-availability-add-rule > button');
    
    // Updates the row based on its selected rule type
    var rowUpdateRuleType = function(row) {
        row.addClass('edd-bk-loading');
        var ruletype = row.find('td.edd-bk-rule-selector > select option:selected').val();
        eddBkFetchRow(0, ruletype, function(response, status, xhr) {
            if (response && !response.error) {
                row.find('td.edd-bk-rule-start').html(response.rendered.start);
                row.find('td.edd-bk-rule-end').html(response.rendered.end);
                row.removeClass('edd-bk-loading');
                useEnhancedFields(row);
            }
        });
    };
    // Binds the row on change rule type event
    var bindRowOnChangeType = function(rows) {
        rows.each(function(i, row) {
            row = $(row);
            row.find('td.edd-bk-rule-selector > select').change(function(e) {
                rowUpdateRuleType(row);
            });
        });
    };
    // Removes a row
    var removeRow = function(row) {
        $(row).remove();
        element.trigger('edd-bk-availability-rules-modified');
    };
    // Bind the row remove callback to a row
    var bindRowOnRemove = function(rows) {
        rows.each(function(i, row) {
            $(row).find('td.edd-bk-rule-remove-handle').click(function(e) {
                removeRow(row);
            });
        });
    };
    // Callback for when the rules change
    var onRulesChanges = function() {
        var numRows = element.find('tbody > tr:not(.edd-bk-if-no-rules)').length;
        element.find('tbody > tr.edd-bk-if-no-rules').toggle(numRows === 0);
    };
    // Adds a row
    var addRow = function(row) {
        // Add it to the table
        var row = $(row).appendTo(element.find('tbody'));
        // Add events
        bindRowOnRemove(row);
        bindRowOnChangeType(row);
        // Trigger event
        element.trigger('edd-bk-availability-rules-modified');
        return row;
    };
    // Sets the loading of the Add Rule button
    var addRuleLoading = function(b) {
        addRuleButton.toggleClass('edd-bk-loading', b);
    };
    // Callback on Add rule button clicked
    var onAddRuleButtonClicked = function (e) {
        addRuleLoading(true);
        eddBkFetchRow(0, '', function(response, status, xhr) {
            if (response && !response.error) {
                var row = addRow(response.rendered);
                addRuleLoading(false);
                useEnhancedFields(row);
            }
        });
    };
    // Callback on form submit event
    var onFormSubmit = function() {
        var rows = element.find('tbody tr:not(.edd-bk-if-no-rules)');
        var i = 0;
        rows.each(function(i, row) {
            row = $(row);
            var paren = '[' + i + ']';
            row.find('td.edd-bk-rule-selector > select').attr('name', 'edd-bk-rule-type'+paren);
            row.find('td.edd-bk-rule-start > *:first-child').attr('name', 'edd-bk-rule-start'+paren);
            row.find('td.edd-bk-rule-end > *:first-child').attr('name', 'edd-bk-rule-end'+paren);
            row.find('td.edd-bk-rule-available > input').attr('name', 'edd-bk-rule-available'+paren);
            i++;
        });
    };
    // Changes "date" the "time" input fields into datepicker and timepicker elements
    var useEnhancedFields = function(rows) {
        var datepickerOptions = {
            dateFormat: 'yy-mm-dd'
        };
        var timepickerOptions = {
            timeFormat: "HH:mm:ss",
            showMillisec: false,
            showMicrosec: false,
            showTimezone: false,
            timeInput: true,
            timezone: 0,
            beforeShow: function(el, instance) {
                $(el).prop('disabled', true);
            },
            onClose: function(dateText, instance) {
                // 'this' refers to the input field.
                $(this).prop('disabled', false);
            }
        };
        var datetimepickerOptions = $.extend({}, datepickerOptions, timepickerOptions);
        rows.each(function(i, row) {
            $(row).find('input[type="date"]').datepicker(datepickerOptions).attr('type', 'text');
            $(row).find('input[type="time"]').timepicker(timepickerOptions).attr('type', 'text');
            $(row).find('input[type="datetime"]').datetimepicker(datetimepickerOptions).attr('type', 'text');
        });
    };
    
    // Bind the rule type change action to all current rows
    bindRowOnChangeType(element.find('tbody > tr'));
    // Bind the row remove event to all current rows
    bindRowOnRemove(element.find('tbody tr:not(.edd-bk-if-no-rules)'));
    // On rules changed
    element.on('edd-bk-availability-rules-modified', onRulesChanges);
    // Trigger event for the first time
    element.trigger('edd-bk-availability-rules-modified');
    // Add rule action
    addRuleButton.click(onAddRuleButtonClicked);
    // On submit action
    element.closest('form').on('submit', onFormSubmit);
    // Enhance all rows on first run
    useEnhancedFields(element.find('tbody > tr'));
    // Make sortable
    element.find('tbody').sortable({
        helper: function (e, tr) {
            var originals = tr.children();
            var helper = tr.clone();
            // Copy selected type
            var selectedType = tr.find('td.edd-bk-rule-selector > select option:selected').val();
            helper.find('td.edd-bk-rule-selector > select option[value="'+selectedType+'"]').prop('selected', true);
            // Copy selected start if the range start uses a select element
            var selectedStart = tr.find('td.edd-bk-rule-start > select option:selected').val();
            if (selectedStart) {
                helper.find('td.edd-bk-rule-start > select option[value="'+selectedStart+'"]').prop('selected', true);
            }
            // Copy selected end if the end start uses a select element
            var selectedEnd = tr.find('td.edd-bk-rule-end > select option:selected').val();
            if (selectedEnd) {
                helper.find('td.edd-bk-rule-end > select option[value="'+selectedEnd+'"]').prop('selected', true);
            }
            helper.children().each(function (i) {
                $(this).width(originals.eq(i).width());
            });
            helper.css('box-shadow', '0 0 8px rgba(0,0,0,0.4)');
            return helper;
        },
        handle: 'td.edd-bk-rule-move-handle',
        distance: 5,
        containment: element.find('table'),
        axis: 'y',
        opacity: 0.8,
        revert: 100
    }).disableSelection();
}

function eddBkAvailabilityPreview(element) {
    element = $(element);
    var instance = null;
    var serviceSelector = element.find('select.edd-bk-calendar-preview-service');
    var getSelectedService = function() {
        return serviceSelector.find('option:selected').val();
    };
    var initDatepicker = function() {
        var serviceId = getSelectedService();
        instance = new BookableDownload(element, serviceId);
    };
    initDatepicker();
};

// Fetches the row HTML from the server
function eddBkFetchRow(serviceId, ruletype, callback) {
    var nonce = $('#edd_bk_availability_ajax_nonce');
    var nonceData = {};
    nonceData[nonce.attr('name')] = nonce.val();
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: $.extend({
            action: 'edd_bk_service_request',
            request: 'availability_row',
            service_id: serviceId,
            args: {
                ruletype: ruletype
            }
        }, nonceData),
        success: function(response, status, xhr) {
            callback(response, status, xhr);
        }
    });
}

// Initializes all availability containers
jQuery(document).ready(function() {
    jQuery('div.edd-bk-availability-container').each(function() {
        eddBkAvailability(this);
    });
    jQuery('div.edd-bk-calendar-preview').each(function() {
        eddBkAvailabilityPreview(this);
    });
});
