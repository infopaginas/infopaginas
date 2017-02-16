define(['jquery', 'tools/reportTracker'], function( $, ReportTracker ) {
    'use strict';

    var redirect = function() {
        this.events = {
            "redirectEvent" : ".redirect-event"
        };

        this.reportTracker = new ReportTracker;

        this.init();
    };

    redirect.prototype.init = function () {
        this.bindRedirectEvents( );
    };

    redirect.prototype.redirectEvent = function ( e ) {
        e.preventDefault();

        var current = $( e.currentTarget );

        var redirectionLink = current.data( 'href' );
        var id = current.data( 'id' );
        var type = current.data( 'type' );

        this.reportTracker.trackEvent( type, id );

        window.open( redirectionLink );
    };

    redirect.prototype.bindRedirectEvents = function ( e ) {
        var self = this;

        $( this.events.redirectEvent ).on( 'click', function( evt ) {
            self.redirectEvent( evt );
        });
    };

    return redirect;
});
