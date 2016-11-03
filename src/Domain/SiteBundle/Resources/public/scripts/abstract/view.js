define(['jquery', 'underscore'], function( $ ) {
    'use strict'

    var view = function( options ) {
        this.init( options );
    }

    view.prototype.init = function ( options ) {
        this.options = {};
        $.extend( this.options, options );

        this.$el = $( this.options.selector || 'body' );
    }

    view.prototype.bindEvents = function bindEvents() {
        var self = this;
        _.each( _.keys( this.events ), function(e) {
            var targetEvent = e.split(" ");
            self.$el.find( targetEvent[0] ).on( targetEvent[1], _.bind( self[self.events[e]], self ) );
        });
    }

    view.prototype.$ = function( selector ) {
        return $( this.$el ).find( selector );
    }

    return view;
});
