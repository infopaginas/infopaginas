define(
    ['jquery',  'abstract/view', 'underscore','bootstrap', 'select2', 'tools/select', 'tools/star-rating', 'async!https://maps.googleapis.com/maps/api/js?v=3&signed_in=false&libraries=drawing,places&key=AIzaSyACRiuSCjh3c3jgxC53StYJCvag6Ig8ZIw'], 
    function ( $, view, _ ) {
    'use strict';

    var mapSearchPage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        return this;
    }

    mapSearchPage.prototype = new view;

    mapSearchPage.prototype.init = function ( options ) {
        this.map = null;
        this.markers = [];
        this.options = {
            mapContainer : 'map-canvas',
            mapOptions   : {
                center: new google.maps.LatLng(18.2208, -66.5901),
                zoom: 8
            }
        };
        $.extend( this.options, options );

        this.initMap(this.options);

    }

    mapSearchPage.prototype.initMap = function ( options ) {
        this.map = new google.maps.Map(document.getElementById(options.mapContainer), this.options.mapOptions);

        if (!_.isEmpty(this.options.markers)) {
            this.addMarkers(this.options.markers);
        }
    }

    mapSearchPage.prototype.addMarkers = function ( markers )
    {
        _.each(markers, this.addMarker.bind(this));
    }

    mapSearchPage.prototype.addMarker = function ( marker )
    {
        this.markers.push(
            new google.maps.Marker({
                position: {
                    lat: parseFloat(marker.latitude),
                    lng: parseFloat(marker.longitude)
                },
                map: this.map,
                title: marker.name
              })
        );
    }

    return mapSearchPage;
});
