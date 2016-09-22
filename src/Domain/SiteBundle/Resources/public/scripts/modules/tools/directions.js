define(['jquery', 'abstract/view', 'js-cookie'],
    function ($, view, cookie) {
    'use strict';

    var directions = function( options ) {

        this.events = {
             ".get-dir click" : "getDirections"
        };

        this.init( options );
        this.bindEvents( );
    };

    directions.prototype = new view;

    directions.prototype.init = function ( options ) {
        this.options = {
            detDirectionsLink : 'https://www.google.by/maps/dir/{userLoc}/{companyLoc}'
        };
        $.extend( this.options, options );
    }

    directions.prototype.getDirections = function ( e, Latlng ) {
        var latlng;

        if ( e ) {
            latlng = $( e.currentTarget ).data( 'latlng' );
        } else if ( Latlng ) {
            latlng = Latlng;
        }

        var userLatLng = cookie.get( 'lat' ) + ',' +cookie.get( 'lng' );

        var directionsLink = this.options.detDirectionsLink.replace( '{companyLoc}', latlng ).replace( '{userLoc}', userLatLng );
        window.open( directionsLink );

        return directionsLink;
    }

    return directions;
});
