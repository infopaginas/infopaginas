define(['jquery', 'abstract/view', 'js-cookie'],
    function ($, view, cookie) {
    'use strict';

    var directions = function( options ) {
        this.init( options );
    };

    directions.prototype = new view;

    directions.prototype.init = function ( options ) {
        this.options = {
            detDirectionsLink : 'https://www.google.by/maps/dir/{userLoc}/{companyLoc}'
        };
        $.extend( this.options, options );
    }

    directions.prototype.bindEventsDirections = function () {
        var self = this;

        $( document ).on( 'click', '.get-dir', function( e, latlngEvent ) {
            var directionLink = self.getDirection( e, latlngEvent );
            window.open( directionLink );
        });
    }

    directions.prototype.getDirection = function ( e, latlngEvent ) {
        var latlng;

        if ( e ) {
            latlng = $( e.currentTarget ).data( 'latlng' );
            var id = $( e.currentTarget).data( 'id' );
            $( document ).trigger( 'trackingInteractions', ['directionButton', id] );
        } else if ( latlngEvent ) {
            latlng = latlngEvent;
        }

        var cookieString = cookie.get( 'geo_location_data' );

        if ( cookieString ) {
            var position = JSON.parse( cookieString );

            var userLatLng = position.coords.latitude + ',' + position.coords.longitude;
        } else {
            var userLatLng = ',';
        }

        var directionLink = this.options.detDirectionsLink.replace( '{companyLoc}', latlng ).replace( '{userLoc}', userLatLng );

        return directionLink;
    }

    return directions;
});
