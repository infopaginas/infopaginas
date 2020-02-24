define(['jquery', 'underscore',  'abstract/view', 'js-cookie', 'jquery-ui'], function( $, _, view, cookie ) {
    'use strict';

    var geolocation = function ( options ) {
        this.position = null;
        this.init(options);

        return this;
    };

    geolocation.prototype = new view();

    geolocation.prototype.init = function ( options ) {
        this.options = {
            geoCodeApiURL : '//maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng=',
            googleAutoSuggestApiURL : '',
            autoCompleteUrl : Routing.generate('domain_search_autocomplete_locality'),
            autoCompleteMinLen : 2,
            refreshPositionTimeout : 600, // secs
            cookieKey : 'geo_location_data',
            searchLocationInput : '#searchLocation',
            getLocalityByCoordUrl: Routing.generate( 'domain_search_closest_locality_by_coord' ),
        };
        $.extend( this.options, options );

        if ( _.isNull(this.options.locationBox) || _.isUndefined(this.options.locationBox) ) {
            this.options.locationBox = this.$(this.options.searchLocation);
        }

        var cookieString = cookie.get( this.options.cookieKey);

        if ( cookieString ) {
            this.position = JSON.parse( cookieString );

            if ( this.position.localityName && this.position.coords.latitude && this.position.coords.longitude ) {
                this.setSearchGroData();
                this.autofillLocation();
            }
        } else {
            this.initPosition();
        }

        this.locationAutocomplete();
    };

    geolocation.prototype.autofillLocation = function( ) {
        if ( !$( this.options.searchLocationInput ).val() ) {
            $( this.options.searchLocationInput ).val( this.position.localityName );
        }
    };

    geolocation.prototype.getLocalityNameByLatLng = function ( lat, lng ) {
        var self = this;

        $.ajax( self.options.getLocalityByCoordUrl, {
            data: {'clt': lat, 'clg': lng},
            success: function( data ) {
                self.position.localityName = data['localityName'];

                self.autofillLocation();
                self.setToCookie();
                self.saveLocationToDatabase();
                self.setSearchGroData();
            },
            timeout: 2000,
        } );
    };

    geolocation.prototype.setToCookie = function () {
        var expiresDate = new Date(new Date().getTime() + this.options.refreshPositionTimeout * 1000);

        var cookieString = {
            localityName: this.position.localityName,
            coords:
                {
                    latitude: this.position.coords.latitude,
                    longitude: this.position.coords.longitude
                }
        };

        cookie.set( this.options.cookieKey, JSON.stringify(cookieString), { expires: expiresDate } );
    };

    geolocation.prototype.saveLocationToDatabase = function () {
        var coordsObject = { 'geolocation' : {
            'latitude' : this.position.coords.latitude,
            'longitude' : this.position.coords.longitude
        }};

        $( document ).trigger( 'trackingMapResult', coordsObject );
    };

    geolocation.prototype.setSearchGroData = function () {
        // set to form fields coordinates and address
        this.$( this.options.searchLocationGeoLoc ).val( this.position.localityName );

        this.$( this.options.searchLatSelector ).val( this.position.coords.latitude );
        this.$( this.options.searchLngSelector ).val( this.position.coords.longitude );
    };

    geolocation.prototype.onGeoLocationError = function ( data ) {
    };

    geolocation.prototype.setPosition = function ( position ) {
        this.position = position;

        this.getLocalityNameByLatLng(
            this.position.coords.latitude,
            this.position.coords.longitude
        );

        return this;
    };

    geolocation.prototype.initPosition = function () {
        if (window.navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.setPosition.bind(this), this.onGeoLocationError.bind(this))
        }
    };

    geolocation.prototype.locationAutocomplete = function ( ) {
        this.options.locationBox.autocomplete({
            'source': this.options.autoCompleteUrl,
            minLength: this.options.autoCompleteMinLen,
            select: this.onAutoCompleteSelect
        });
    };

    return geolocation;
});
