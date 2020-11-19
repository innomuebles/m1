;(function (exports){

    // convertHexToString from http://stackoverflow.com/a/17057456
    function convertHexToString(input) {
        // split input into groups of two
        var hex = input.match(/[\s\S]{2}/g) || [];
        var output = '';
        // build a hex-encoded representation of your string
        for (var i = 0, j = hex.length; i < j; i++) {
            output += '%' + ('0' + hex[i]).slice(-2);
        }
        // decode it using this trick
        return decodeURIComponent(output)
    }

    var attributeName = 'data-perform';

    var BotProtection = Class.create();
    BotProtection.prototype = {

        initialize: function(){
            this.restoredElements = [];
        },

        restore: function(el){
            // console.log(el);
            var encoded = el.readAttribute(attributeName);
            if (encoded) {
                el.writeAttribute('action', convertHexToString(encoded));
                el.removeAttribute(attributeName);
                this.restoredElements.push(el);
            }
        }

    }

    exports.BP = new BotProtection()

})(this);

document.observe("dom:loaded", function(){
    $$('form').each(function(el){
        BP.restore(el)
    })
});
