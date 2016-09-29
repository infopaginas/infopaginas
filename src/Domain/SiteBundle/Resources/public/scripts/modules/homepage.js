define(
    [
        'jquery', 'bootstrap', 'tools/search', 'tools/geolocation', 'tools/searchMenu', 'tools/resetPassword',
        'tools/login', 'tools/registration'
    ], function ( $, bootstrap, Search, Geolocation, SearchMenu, ResetPassword ) {
    'use strict';

    var homepage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        };

        $( '#forgottenPasswordModal' ).on('shown.bs.modal', function () {
            $( 'body' ).addClass( 'modal-open' );
        }).on('hidden', function () {
            $( 'body' ).removeClass( 'modal-open' )
        });

        $( '#mobileLanguageSelect' ).on('change', function () {
            document.location = $(this).val();
        });

        this.init( options );
        return this;
    };

    homepage.prototype.init = function ( options ) {
        this.options = {};

        $.extend( this.options, options );

        this.initSearch();
    };

    homepage.prototype.initSearch = function ( ) {
        var searchOptions = {
            selector              : '.search-form',
            searchSelector        : '#searchBox',
            searchHintSelector    : '#searchHint',
            searchResultsSelector : '#searchResultsAutosuggest',
            searchLocation        : '#searchLocation',
            searchLocationGeoLoc  : '#searchLocationGeoLoc',
            searchLatSelector     : '#searchLat',
            searchLngSelector     : '#searchLng',
            submitSelector        : '#searchButton',
            searchHeaderButton    : '#searchHeaderButton'
        };

        searchOptions['geolocation'] = new Geolocation( {
            'searchLocation'        : searchOptions.searchLocation,
            'searchLocationGeoLoc'  : searchOptions.searchLocationGeoLoc,
            'searchLatSelector'     : searchOptions.searchLatSelector,
            'searchLngSelector'     : searchOptions.searchLngSelector
        } );

        searchOptions['searchMenu'] = new SearchMenu;

        var search = new Search( searchOptions );
        this.resetPassword = new ResetPassword();
    };

    return homepage;
});
