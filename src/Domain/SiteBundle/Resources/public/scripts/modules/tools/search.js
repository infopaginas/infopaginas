define(['jquery', 'abstract/view',  'jquery-ui'], function( $, view ) {
    'use strict'

    var search = function( options ) {
        this.events = {};
        
        this.events[options.searchSelector + ' focus'] = 'onSearchBoxFocus';
        this.events[options.searchSelector + ' blur'] = 'onSearchBoxBlur';
        
        this.options = {
            autoComplete : true,
            autoCompleteUrl : '/search/autocomplete',
            autoCompleteMinLen : 1,
            geoCodeApiURL : 'http://maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng='
        };

        $.extend( this.options, options );

        this.init( options );
        this.bindEvents();

        return this;
    }

    search.prototype = new view();

    search.prototype.init = function ( options ) {
        this.parent = this.__proto__.__proto__;

        this.parent.init( options );

        this.searchBox          = this.$( this.options.searchSelector );
        this.searchHintBox      = this.$( this.options.searchHintSelector );
        this.searchBoxResults   = this.$( this.options.searchResultsSelector );
        this.searchLocations    = this.$( this.options.locationsSelector );
        this.submitButton       = this.$( this.options.submitSelector );

        if ( this.options.autoComplete ) {
            this.initAutocomplete( this.options.autoCompleteUrl );
        }

        if (navigator.geolocation) {
            this.initGeolocation();
        } else {
            this.onGeolocationError();
        }
    }

    search.prototype.initAutocomplete = function (url) {
        url = url || this.options.autoCompleteUrl;
        this.searchBox.autocomplete({
            'source': url,
            minLength: this.options.autoCompleteMinLen,
            select: this.onAutoCompleteSelect
        });
    }

    search.prototype.onAutoCompleteSelect = function ( event, ui ) {
        alert('olololo');
    }

    search.prototype.onSearchBoxFocus = function () {
        this.searchHintBox.show();
    }

    search.prototype.onSearchBoxBlur = function () {
        this.searchHintBox.hide();
    }

    search.prototype.initGeolocation = function () {
        navigator.geolocation.getCurrentPosition( this.showPosition.bind(this) );
    }

    search.prototype.showPosition = function ( position ) {
        console.log(position);
        this.getLocationsNameByLatLng.bind(this)( position.coords.latitude, position.coords.longitude );
    }

    search.prototype.onGeolocationError = function () {

    }

    search.prototype.getLocationsNameByLatLng = function ( lat, lng ) {
        var self = this;
        $.when(
            $.get(this.options.geoCodeApiURL + (lat + ',' + lng))
        ).then(
            this.onGeoLocationSuccess,
            this.onGeoLocationError
        );
    }



    search.prototype.onGeoLocationSuccess = function (data) {
        console.log(data);
    }

    search.prototype.onGeoLocationError = function (data) {
        console.log(data);
    }

    return search;
});
