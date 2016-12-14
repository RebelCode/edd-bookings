/* global top, EddBkLocalized_Ajax */

;(function($, window, document, remote, undefined) {
    
    EddBk.Ajax = {
        url: remote.url || window.ajaxurl,
        ajax: function(type, action, args, callback, dataType) {
            var data = {
                action: 'eddbk_ajax',
                request: action,
                args: args
            };
            dataType = dataType || 'json';
            $.ajax({
                url: EddBk.Ajax.url,
                type: type,
                data: data,
                success: callback,
                error: function() {
                    callback({
                        success: false,
                        error: 'Internal Server Error'
                    });
                },
                dataType: dataType,
                xhrFields: {
                    withCredentials: true
                }
            });
        },
        get: function(action, args, callback, dataType) {
            this.ajax('GET', action, args, callback, dataType);
        },
        post: function(action, args, callback, dataType) {
            this.ajax('POST', action, args, callback, dataType);
        }
    };
    
})(jQuery, top, document, EddBkLocalized_Ajax);
