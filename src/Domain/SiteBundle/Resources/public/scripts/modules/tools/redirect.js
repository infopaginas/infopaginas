define(['jquery', 'tools/googleMapLink'],
    function( $, googleMap ) {
    'use strict';

    var redirect = function() {
        this.events = {
            "redirectEvent" : ".redirect-event"
        };

        this.init();
    };

    redirect.prototype.init = function () {
        this.bindRedirectEvents( );
        this.googleMap = new googleMap();
    };

    redirect.prototype.redirectEvent = function ( e ) {
        e.preventDefault();

        var current = $( e.currentTarget );
        var useCurrentTab = current.data( 'current-tab' );

        if ( $(current[0]).hasClass( 'redirect-google-map' ) ) {
            var coordinatesArray = current.data( 'latlng' ).split( ',' );
            var latitude = coordinatesArray[ 0 ];
            var longitude = coordinatesArray[ 1 ];
            var redirectionLink = this.googleMap.getGoogleMapUrl( latitude, longitude );
        } else {
            var redirectionLink = current.data( 'href' );
        }

        var id = current.data( 'id' );
        var type = current.data( 'type' );

        $( document ).trigger( 'trackingInteractions', [ type, id ] );

        if ( useCurrentTab ) {
            window.location.href = redirectionLink;
        } else {
            window.open( redirectionLink );
        }
    };

    redirect.prototype.bindRedirectEvents = function ( e ) {
        var self = this;

        $( document ).on( 'click', this.events.redirectEvent, function( evt ) {
            self.redirectEvent( evt );
        });
    };

    return redirect;
});
