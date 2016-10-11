(function(undefined) {

    /**
     * Base "class" for an interface - describes a prototype that has unimplemented member functions intended to be
     * implemented by extending classes, i.e. imposes a contract.
     */
    EddBk.newClass('EddBk.Interface', EddBk.Object, {
        __unimplemented: function() {
            throw this.class + ' implements interface "' + this.base + "' and does not implement function `" + arguments.callee.caller.name + "`";
        }
    });

    // Reset base class to allow top-level extending classes to be their own base
    EddBk.Interface.prototype.base = null;

})();
