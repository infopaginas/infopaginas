define(['jquery', 'underscore',  'abstract/view', 'js-cookie', 'jquery-ui'], function( $, _, view, cookie ) {
    'use strict'

    var geolocation = function ( options ) {
        this.position = null;
        this.init(options);

        return this;
    }

    geolocation.prototype = new view();

    geolocation.prototype.init = function ( options ) {
        this.options = {
            geoCodeApiURL : '//maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng=',
            googleAutoSuggestApiURL : '',
            autoCompleteUrl : Routing.generate('domain_site_geolocation_autocomplete'),
            autoCompleteMinLen : 2,
            refreshPositionTimeout : 600, // secs
            cookieKey : 'geo_location_data',
        };
        $.extend( this.options, options );

        if ( _.isNull(this.options.locationBox) || _.isUndefined(this.options.locationBox) ) {
            this.options.locationBox = this.$(this.options.searchLocation);
        }

        if ( !this.options.locationBox.val()) {
            // start geo if custom field is empty

            var cookieString = cookie.get( this.options.cookieKey);

            if ( cookieString ) {
                this.position = JSON.parse( cookieString );

                this.setToForm();
            } else {
                this.initPosition();
            }
        }

        this.locationAutocomplete();
    }

    geolocation.prototype.isGelocationAvailable = function () {
        return !!navigator.geolocation;
    }

    geolocation.prototype.getLocationsNameByLatLng = function ( lat, lng ) {
        var self = this;

        $.when(
            $.get(window.location.protocol + this.options.geoCodeApiURL + (lat + ',' + lng))
        ).then(
            this.onLocationsNameByLatLngSuccess.bind(self),
            this.onGeoLocationError.bind(self)
        );
    }

    geolocation.prototype.onLocationsNameByLatLngSuccess = function ( data ) {
        this.extractAddress(data);
        this.setToCookie();
        this.setToForm();
    }

    geolocation.prototype.setToCookie = function () {
        var expiresDate = new Date(new Date().getTime() + this.options.refreshPositionTimeout * 1000);

        var cookieString = {
            address: this.position.address,
            coords:
                {
                    latitude: this.position.coords.latitude,
                    longitude: this.position.coords.longitude,
                }
        };

        cookie.set( this.options.cookieKey, JSON.stringify(cookieString), { expires: expiresDate } );
    }

    geolocation.prototype.setToForm = function () {
        // set to form fields coordinates and address

        this.$( this.options.searchLocation ).val( this.position.address );
        this.$( this.options.searchLocationGeoLoc ).val( this.position.address );

        this.$( this.options.searchLatSelector ).val( this.position.coords.latitude );
        this.$( this.options.searchLngSelector ).val( this.position.coords.longitude );
    }

    geolocation.prototype.onGeoLocationError = function ( data ) {
    }

    geolocation.prototype.extractAddress = function ( arrayData ) {
        var mainAddr = arrayData.results[0];

        var city = null, zip = null, fullAddr;
        _.each(mainAddr.address_components, function( item ) {
            if ( _.contains( item.types, 'locality') ) {
                city = item.long_name;
            }
        })

        fullAddr = ( !_.isNull(city) ? city : '' );

        this.position.address = fullAddr;
    }

    geolocation.prototype.setPosition = function ( position ) {
        this.position = position;

        this.getLocationsNameByLatLng(
            this.position.coords.latitude,
            this.position.coords.longitude
        );

        return this;
    }

    geolocation.prototype.initPosition = function () {
        navigator.geolocation.getCurrentPosition(this.setPosition.bind(this), this.onGeoLocationError.bind(this))
    }

    geolocation.prototype.locationAutocomplete = function ( ) {
        this.options.locationBox.autocomplete({
            'source': this.options.autoCompleteUrl,
            minLength: this.options.autoCompleteMinLen,
            select: this.onAutoCompleteSelect
        });
    }

    return geolocation;
})
