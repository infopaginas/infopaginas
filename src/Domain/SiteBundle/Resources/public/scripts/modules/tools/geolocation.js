define(['jquery', 'underscore',  'abstract/view', 'js-cookie', 'jquery-ui'], function( $, _, view, cookie ) {
    'use strict'

    var geolocation = function ( options ) {
        this.init(options);

        this.position = null;
        this.getPosition();
        return this;
    }

    geolocation.prototype = new view();

    geolocation.prototype.init = function ( options ) {
        this.options = {
            geoCodeApiURL : '//maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng=',
            googleAutoSuggestApiURL : '',
            autoCompleteUrl : '/app_dev.php/geolocation/autocomplete',
            autoCompleteMinLen : 2,
        };
        $.extend( this.options, options );

        if ( _.isNull(this.options.locationBox) || _.isUndefined(this.options.locationBox) ) {
            this.options.locationBox = this.$(this.options.locationBoxSelector);
        }

        setInterval(this.initPosition.bind(this), 30000); // 30 secs timeout
    }

    geolocation.prototype.getAddress = function ( callback ) {
        if (!this.isGelocationAvailable()) {
            return null;
        }

        this.callback = callback;

        this.getGeodata();
    }

    geolocation.prototype.isGelocationAvailable = function () {
        return !!navigator.geolocation;
    }

    geolocation.prototype.showPosition = function ( position ) {
        this.getLocationsNameByLatLng.bind(this)( position.coords.latitude, position.coords.longitude );
    }

    geolocation.prototype.getLocationsNameByLatLng = function ( lat, lng ) {
        var self = this;
        $.when(
            $.get(window.location.protocol + this.options.geoCodeApiURL + (lat + ',' + lng))
        ).then(
            this.onGeoLocationSuccess.bind(self),
            this.onGeoLocationError.bind(self)
        );
    }

    geolocation.prototype.onGeoLocationSuccess = function ( data ) {
        this.callback(
            this.extractAddress(data)
        );
    }

    geolocation.prototype.onGeoLocationError = function ( data ) {
        this.locationAutocomplete();
    }

    geolocation.prototype.extractAddress = function ( arrayData ) {
        var mainAddr = arrayData.results[0];

        var city = null, zip = null, fullAddr;
        _.each(mainAddr.address_components, function( item ) {
            if ( _.contains( item.types, 'locality') ) {
                city = item.long_name;
            }

            if ( _.contains( item.types, 'postal_code') ) {
                zip = item.long_name;
            }
        })

        fullAddr = ( !_.isNull(city) ? city : '' ) + (!_.isNull(zip) ? ', ' + zip : ''); 
        return fullAddr;
    }

    geolocation.prototype.setPosition = function ( position ) {
        this.position = position;

        cookie.set('lat', this.getLat());
        cookie.set('lng', this.getLng());

        return this;
    }

    geolocation.prototype.getPosition = function () {
        if ( _.isNull(this.position) ) {
            this.initPosition();
        }

        return this.position;
    }

    geolocation.prototype.initPosition = function () {
        navigator.geolocation.getCurrentPosition(this.setPosition.bind(this), this.onGeoLocationError.bind(this))
    }


    geolocation.prototype.getLat = function () {
        return this.position.coords.latitude;
    }

    geolocation.prototype.getLng = function () {
        return this.position.coords.longitude;
    }

    geolocation.prototype.coordinatesCallback = function ( position ) {
        this.setPosition( position );

        this.getLocationsNameByLatLng(
            this.getLat(),
            this.getLng()
        );
    }

    geolocation.prototype.getGeodata = function () {
        navigator.geolocation.getCurrentPosition(this.coordinatesCallback.bind(this), this.onGeoLocationError.bind(this))
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
