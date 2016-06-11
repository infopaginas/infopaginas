define(
    ['jquery', 'abstract/view', 'tools/geolocation'],
    function( $, view, Geolocation ) {
    'use strict'

    var searchMenu = function( options ) {
        this.options = {
            'selector' : '.menu-hexagon'
        };

        this.events = {
            'a click' : 'onLinkClick'
        };

        this.init( options );
        this.bindEvents();
        return this;
    }

    searchMenu.prototype = new view;

    searchMenu.prototype.init = function ( options ) {
        $.extend( this.options, options );

        this.callback = false;
    }

    searchMenu.prototype.initQuickLinks = function ( callback ) {
        this.callback = callback;
    }

    searchMenu.prototype.onLinkClick = function ( event ) {
        var target = event.target

        if (target.tagName == "SPAN") {
            target = this.$(target).parents('a').first();
        }

        var searchString = this.$(target).data('search');

        if (this.callback !== false)
            this.callback(searchString);
    }

    return searchMenu;
})
