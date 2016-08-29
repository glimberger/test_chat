/**
 * Created by guillaumelimberger on 13/07/2016.
 */

/**
 *
 */
(function($){
    $.glimChat = function(el, radius, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("GlimChat", base);

        base.init = function(){
            if( typeof( radius ) === "undefined" || radius === null ) radius = "20px";

            base.radius = radius;

            base.options = $.extend({},$.glimChat.defaultOptions, options);

            // Put your initialization code here
        };

        // Sample Function, Uncomment to use
        // base.functionName = function(paramaters){
        //
        // };

        // Run initializer
        base.init();
    };

    $.yourPluginName.defaultOptions = {
        host: "localhost",
        port: 80,
        roomID: 0,
        chatroomID: "gl-chatroom",
        sendBtnID: "gl-send"
    };

    $.fn.glimChat = function(radius, options){
        return this.each(function(){
            (new $.glimChat(this, radius, options));

            // HAVE YOUR PLUGIN DO STUFF HERE


            // END DOING STUFF

        });
    };

})(jQuery);
