define(
    ['jquery', 'bootstrap', 'tools/search'], 
    function ( $, bootstrap, Search, require ) {
    'use strict';

    var homepage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        return this;
    }

    homepage.prototype.init = function ( options ) {
        this.options = {};

        $.extend( this.options, options );

        this.initSearch();
    }

    homepage.prototype.initSearch = function ( ) {
        var searchOptions = {
            selector            : '.search-form',
            searchSelector      : '#searchBox',
            searchHintSelector  : '#searchHint',
            searchResultsSelector : '#searchResultsAutosuggest',
            locationsSelector   : '#searchLocation',
            submitSelector      : '#searchButton',
        }

        var search = new Search(searchOptions);
    }
   

    return homepage;
});
