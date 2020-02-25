define(['jquery'], function( $ ) {
    'use strict';

    var googleMapLink = function() {
        this.urls = {
            default: 'https://maps.google.com/maps',
            IOS: 'maps://maps.google.com/maps'
        };
    };

    googleMapLink.prototype.getGoogleMapUrl = function( lat, lng ) {
        var url = '';

        if ( ( navigator.platform.indexOf( 'iPhone' ) !== -1 ) ||
            ( navigator.platform.indexOf( 'iPad' ) !== -1 ) ||
            ( navigator.platform.indexOf( 'iPod' ) !== -1 ) ) {
            url = this.urls.IOS + '?daddr=' + lat + ',' + lng + '&amp;ll=';
        } else {
            url = this.urls.default + '?daddr=' + lat + ',' + lng + '&amp;ll=';
        }

        return url;
    };

    return googleMapLink;
});
