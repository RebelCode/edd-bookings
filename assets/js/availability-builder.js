(function ($) {
    
    EddBk.availBuilder = {
        // Data namespace
        namespace: 'EddBk.AvailBuilder',
        // Default vars
        defaults: $.extend(EddBk.Utils.jqp.defaults, {
            form: null,
            builder: null,
            body: null,
            addBtn: null,
            nonceEl: null,
            nonce: {
                name: null,
                value: null
            }
        }),
        // Methods
        methods: $.extend(EddBk.Utils.jqp.methods, {
            /**
             * Initializes the instance.
             * 
             * @returns {Array} This instance.
             */
            init: function() {
                _do(this, 'initData');
                _do(this, 'initEvents');
                _do(this, 'initSortable');
                return this;
            },
            /**
             * Initializes the data.
             *  
             * @returns {Array} This instance.
             */
            initData: function() {
                return this.each(function() {
                    var nonce = $(this).find('input[name="edd_bk_availability_ajax_nonce"]');
                    var builder = $(this).find('div.edd-bk-builder'),
                        header = builder.find('div.edd-bk-header'),
                        body = builder.find('div.edd-bk-body'),
                        footer = builder.find('div.edd-bk-footer'),
                        addBtn = footer.find('div.edd-bk-col-add-rule > button');
                    setOption(this, {
                        isInit: true,
                        builder: builder,
                        header: header,
                        body: body,
                        footer: footer,
                        addBtn: addBtn,
                        form: $(this).closest('form'),
                        nonceEl: nonce,
                        nonce: {
                            name: nonce.attr('name'),
                            value: nonce.val()
                        }
                    });
                });
            },
            /**
             * Initializes the events.
             * 
             * @returns {Array} This instance.
             */
            initEvents: function() {
                return this.each(function() {
                    var body = getOption(this, 'body');
                    var rows = body.find('> div.edd-bk-row:not(.edd-bk-if-no-rules)');
                    // Bind the rule type change action to all current rows
                    _do(this, 'bindRowOnChangeType', rows);
                    // Bind the row remove event to all current rows
                    _do(this, 'bindRowOnRemove', rows);

                    // Enhance the row fields
                    _do(this, 'enhanceRowFields', rows);

                    // Add Button clck event
                    getOption(this, 'addBtn').unbind('click').click(methods.createRow.bind(this));

                    // Form submit event
                    var form = getOption(this, 'form');
                    form.unbind('submit', methods.onSubmit);
                    EddBk.Utils.preBind(form, 'submit', methods.onSubmit.bind(this));

                    // On rules modified event
                    $(this).on('edd-bk-availability-rules-modified', methods.onRulesChanged.bind(this));
                    // Trigger it for the first time
                    $(this).trigger('edd-bk-availability-rules-modified');
                });
            },
            /**
             * Initializes the sortable table body.
             */
            initSortable: function() {
                getOption(this, 'body').sortable({
                    helper: function (e, row) {
                        var originals = row.children();
                        var helper = row.clone();
                        // Copy selected type
                        var selectedType = row.find('div.edd-bk-col-time-unit > select option:selected').val();
                        selectedType = selectedType.replace(/\\/g, '\\\\');
                        helper.find('div.edd-bk-col-time-unit > select option[value="'+selectedType+'"]').prop('selected', true);
                        // Copy selected start if the range start uses a select element
                        var selectedStart = row.find('div.edd-bk-col-start > select option:selected').val();
                        if (selectedStart) {
                            selectedStart = selectedStart.replace(/\\/g, '\\\\');
                            helper.find('div.edd-bk-col-start > select option[value="'+selectedStart+'"]').prop('selected', true);
                        }
                        // Copy selected end if the end start uses a select element
                        var selectedEnd = row.find('div.edd-bk-col-end > select option:selected').val();
                        if (selectedEnd) {
                            selectedEnd = selectedEnd.replace(/\\/g, '\\\\');
                            helper.find('div.edd-bk-col-end > select option[value="'+selectedEnd+'"]').prop('selected', true);
                        }
                        helper.children().each(function (i) {
                            $(this).width(originals.eq(i).width());
                        });
                        helper.css('box-shadow', '0 0 8px rgba(0,0,0,0.4)');
                        return helper;
                    },
                    handle: 'div.edd-bk-col-move',
                    distance: 5,
                    containment: getOption(this, 'builder'),
                    axis: 'y',
                    opacity: 0.8,
                    revert: 100
                });
            },
            /**
             * Gets the rows.
             *
             * @returns {Array}
             */
            getAvailability: function() {
                var result = {};
                var rows = getOption(this, 'body').find('div.edd-bk-row:not(.edd-bk-if-no-rules)');
                rows.each(function(i, row) {
                    row = $(row);
                    result[i] = {
                        type: row.find('div.edd-bk-col-time-unit > select').val(),
                        start: row.find('div.edd-bk-col-start > *:first-child').val(),
                        end: row.find('div.edd-bk-col-end > *:first-child').val(),
                        available: row.find('div.edd-bk-col-available > input').is(':checked')
                    };
                });

                return result;
            },
            /**
             * Creates a new row.
             */
            createRow: function() {
                _do(this, 'setLoading', true);
                _do(this, 'fetchRow', '', function(response) {
                    if (response && !response.error) {
                        var row = _do(this, 'addRow', $(response.rendered));
                        _do(this, 'enhanceRowFields', row);
                        _do(this, 'setLoading', false);
                    }
                }.bind(this));
            },
            /**
             * Adds a row.
             * 
             * This method is usually called after the row-fetching XHR response is received.
             * 
             * @param {Element} row The row to add.
             * @returns {Element} The row element.
             */
            addRow: function(row) {
                // Add it to the table
                row.appendTo(getOption(this, 'body'));
                // Add events
                _do(this, 'bindRowOnRemove', row);
                _do(this, 'bindRowOnChangeType', row);
                // Trigger event
                $(this).trigger('edd-bk-availability-rules-modified');
                return row;
            },
            /**
             * Toggles the loading on or off.
             * 
             * @param {boolean} isLoading True to turn on loading, false to turn it off.s
             */
            setLoading: function(isLoading) {
                getOption(this, 'addBtn').toggleClass('edd-bk-loading', isLoading);
            },
            /**
             * Fetches row HTML though an XHR request to the server.
             * 
             * @param {string} ruletype The ruletype. Leave empty for a new default row.
             * @param {Function} callback The function to call after the response is received.
             */
            fetchRow: function(ruletype, callback) {
                var data = {};
                var nonce = getOption(this, 'nonce');
                data[nonce.name] = nonce.value;
                data = $.extend({
                    action: 'edd_bk_service_request',
                    request: 'availability_row',
                    service_id: 0,
                    args: {
                        ruletype: ruletype
                    }
                }, data);
                $.ajax({
                    url: EddBk.Ajax.url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response, status, xhr) {
                        callback(response, status, xhr);
                    }
                });
            },
            /**
             * Changes "date" the "time" input fields into datepicker and timepicker elements.
             * 
             * @param {Array} rows The rows to enhance.
             */
            enhanceRowFields: function(rows) {
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
                    // Date fields
                    $(row).find('input[type="date"]').datepicker(datepickerOptions).attr('type', 'text');
                    // Time fields
                    $(row).find('input[type="time"]')
                        .timepicker(timepickerOptions)
                        .attr('type', 'text')
                        // Redirect focus to timepicker's time input field
                        .focus(function() {
                            var dp = $(this).data('datepicker');
                            if (dp && dp.dpDiv) {
                                dp.dpDiv.find('input.ui_tpicker_time_input').focus();
                            }
                        });
                    $(row).find('input[type="datetime"]').datetimepicker(datetimepickerOptions).attr('type', 'text');
                });
            },
            /**
             * Binds row removal events to a set of rows.
             * 
             * @param {Array} rows The rows
             */
            bindRowOnRemove: function(rows) {
                rows.each(function(i, row) {
                    $(row).find('div.edd-bk-col-remove').click(function(e) {
                        _do(this, 'removeRow', row);
                        e.preventDefault();
                        e.stopPropagation();
                    }.bind(this));
                }.bind(this));
            },
            /**
             * Removes a row.
             * 
             * @param {Element} row The row to remove.
             */
            removeRow: function(row) {
                $(row).remove();
                $(this).trigger('edd-bk-availability-rules-modified');
            },
            /**
             * Binds the row event for when the rule type changes.
             * 
             * @param {Array} rows The rows.
             */
            bindRowOnChangeType: function(rows) {
                rows.each(function(i, row) {
                    row = $(row);
                    row.find('div.edd-bk-col-time-unit > select').change(function(e) {
                        _do(this, 'rowUpdateRuleType', row);
                    }.bind(this));
                }.bind(this));
            },
            /**
             * Updates the fields for a row based on the selected rule type.
             * 
             * @param {Element} row The row.
             */
            rowUpdateRuleType: function(row) {
                row.addClass('edd-bk-loading');
                var ruletype = row.find('div.edd-bk-col-time-unit > select option:selected').val();
                _do(this, 'fetchRow', ruletype, function(response) {
                    if (response && !response.error) {
                        row.find('div.edd-bk-col-start').html(response.rendered.start);
                        row.find('div.edd-bk-col-end').html(response.rendered.end);
                        row.removeClass('edd-bk-loading');
                        _do(this, 'enhanceRowFields', row);
                    }
                }.bind(this));
            },
            /**
             * Called when the rules have changed.
             * Toggles the "no rules" message based on the number of current rules.
             */
            onRulesChanged: function() {
                var body = getOption(this, 'body');
                var rows = body.find('> div.edd-bk-row:not(.edd-bk-if-no-rules)');
                var numRows = rows.length;
                body.find('> div.edd-bk-if-no-rules').toggle(numRows === 0);
            },
            /**
             * Called when the closest form containing the builder is submitted.
             */
            onSubmit: function() {
                var rows = getOption(this, 'body').find('div.edd-bk-row:not(.edd-bk-if-no-rules)');
                var i = 0;
                rows.each(function(i, row) {
                    row = $(row);
                    var paren = '[' + i + ']';
                    row.find('div.edd-bk-col-time-unit > select').attr('name', 'edd-bk-rule-type'+paren);
                    row.find('div.edd-bk-col-start > *:first-child').attr('name', 'edd-bk-rule-start'+paren);
                    row.find('div.edd-bk-col-end > *:first-child').attr('name', 'edd-bk-rule-end'+paren);
                    row.find('div.edd-bk-col-available > input').attr('name', 'edd-bk-rule-available'+paren);
                    i++;
                });
            }
        })
    };
    
    /*-------------------------------------------------
     * POINTERS
     *-------------------------------------------------*/
    
    var methods = EddBk.availBuilder.methods;
    var _do = EddBk.Utils.jqp.call.bind(EddBk.availBuilder);
    var getOption = EddBk.Utils.jqp.getData.bind(EddBk.availBuilder);
    var setOption = EddBk.Utils.jqp.setData.bind(EddBk.availBuilder);
    
    /*-------------------------------------------------
     * JQUERY PLUGIN
     *-------------------------------------------------*/
    
    $.fn.eddBkAvailabilityBuilder = function(args) {
        return EddBk.Utils.jqp.fn.apply(this, [EddBk.availBuilder, args]);
    };
    
    /*-------------------------------------------------
     * ELEMENT INITIALIZATION
     *-------------------------------------------------*/
    
    // Initializes all availability containers
    $(document).ready(function() {
        $('.edd-bk-availability-container').each(function() {
            $(this).eddBkAvailabilityBuilder();
        });
    });
    
})(jQuery);
