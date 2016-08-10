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
            detDirectionsLink : 'https://www.google.by/maps/dir/{companyLoc}/{userLoc}'
        };
        $.extend( this.options, options );
    }

    directions.prototype.getDirections = function ( e ) {
        var latlng = $( e.currentTarget ).data( 'latlng' );
        var userLatLng = cookie.get( 'lat' ) + ',' +cookie.get( 'lng' );

        var directionsLink = this.options.detDirectionsLink.replace( '{companyLoc}', latlng ).replace( '{userLoc}', userLatLng );
        window.open( directionsLink );
    }

    return directions;
});
