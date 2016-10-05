/**
 * A truncated version of essential classes in the EddBk namespace.
 * 
 * Adopted from Xedin's Xdn.Object
 * 
 * Requires EddBk.Class.
 * @author Xedin Unknown <xedin.unknown@gmail.com>, Miguel Muscat <miguelmuscat93@gmail.com>
 */

;(function($, window, document, undefined) {

    // This is the base, top level namespace
    window.EddBk = window.EddBk || {};
    
    // Allows easy namespacing of classes
    EddBk.assignNamespace = function (object, ns, overwrite) {
        if (!object) return;
        
        if ((typeof object === 'string') && !ns) {
            ns = object;
            object = this;
        }

        ns = ns.split('.');
        var obj, base, nsi;
        for (var i = 0; i < (ns.length-1); i++) {
            nsi = ns[i];
            base = i ? obj : window;
            base[nsi] = base[nsi] || {};
            obj = base[nsi];
        }
        nsi = ns[i];
        
        if (obj && !overwrite && obj[ns[i]] && $.isPlainObject(obj[ns[i]])) {
            object = $.extend(object, obj[ns[i]]);
        }
        obj[ns[i]] = object;

        return object;
    };

    // Resolves namespace string into object reference
    EddBk.resolve = function(ns) {
        ns = ns.split('.');
        var obj, base, nsi;
        for (var i = 0; i < ns.length; i++) {
            nsi = ns[i];
            base = i ? obj : window;
            base[nsi] = base[nsi] || {};
            obj = base[nsi];
        }
        return obj;
    };

    EddBk.resolveSet = function(ns, value) {
        ns = ns.split('.');
        var baseNs = ns.slice(0, -1).join('.'),
            base = EddBk.resolve(baseNs),
            objName = ns[ns.length - 1];
        base[objName] = value;
        return value;
    };

    // Easy creation of a namespaced class
    EddBk.create = function(ns, parent, proto) {
        // Prepare namespace
        EddBk.assignNamespace({}, ns, true);
        // Extend it with proto and set it to ns object
        return EddBk.resolveSet(ns, parent.extend(proto));
    };

    // Prevents errors in browsers that do not have a `console` global
    !window.console && (window.console = {
        log:            function() {},
        info:           function() {},
        warn:           function() {},
        error:          function() {}
    });
})(jQuery, top, document);

/* EddBk.Object */
;(function($, window, document, undefined) {
    
    var EddBk_Object = EddBk.Class.extend({
        _data: {},
        
        init: function(data) {
            this._data = {};
            data && (this._data = data);
        },
        
        getData: function(key) {
            return key ? this._data[key] : this._data;
        },
        
        setData: function(key, value) {
            if( !value ) {
                this._data = key;
                return this;
            }
            
            this._data[key.toString()] = value;
            return this;
        },
        
        unsData: function(key) {
            if( !key ) {
                this._data = {};
                return this;
            }
            
            delete this._data[key];
        },
        
        addData: function(key, value) {
            if( value ) {
                this.setData(key, value);
                return this;
            }
            
            this.setData($.extend({}, this.getData(), key));
        },
        
        clone: function(additionalData) {
            var newObject = new EddBk.Object(this.getData());
            additionalData && newObject.addData(additionalData);
            return newObject;
        },
        
        _beforeMix: function(mixin) {
            return mixin;
        },
        
        _afterMix: function(mixin) {
            return this;
        },
        
        mix: function(mixin) {
            var self = this;
            mixin = mixin instanceof Array ? mixin : [mixin];
            mixin = this._beforeMix(mixin);
            $.each(mixin, function(i, mixin) {
                if( (/boolean|number|string|array/).test(typeof mixin) ) return true;
                EddBk.Object.augment(self, mixin);
            });
            this._afterMix(mixin);
            
            return this;
        },
        
        // Dummy function for mixin initialization. To be implemented in mixin
        _mix: function() {
        }
    });
    
    EddBk_Object.find = function(object, value, one) {
        one = one && true;
        var result = [];
        $.each(object, function(k, v) {
            var end = (v === value) && (result.push(k) > 1) && one;
            if( end ) return false;
        });
        
        return one ? result : result[0];
    };
    
    EddBk_Object.augment = function(destination, source) {
        for(var prop in source) {
            if( !source.hasOwnProperty(prop) ) continue;
            destination[prop] = typeof(destination[prop]) !== 'undefined' ?
            (function(prop) {
                var fn = source[prop],
                    _super = destination[prop];
                return function() {
                    // Save any _super variable that already existed
                    var tmp = this._super,
                        result;

                    this._super = _super;
                    result = fn.apply(this, arguments);

                    // Restore _super
                    this._super = tmp;
                    return result;
                };
            })(prop) :
            source[prop];
        }

        return destination;
    };
    
    /**
     * @name EddBk.Object
     * @class
     */
    EddBk.assignNamespace(EddBk_Object, 'EddBk.Object');
    
    EddBk.Object.camelize = function(string, separator) {
        separator = separator || '_';
        var ex = new RegExp(separator+'([a-zA-Z])', 'g');
        return string.replace(ex, function (g) { return g[1].toUpperCase(); });
    }
    
})(jQuery, top, document);
