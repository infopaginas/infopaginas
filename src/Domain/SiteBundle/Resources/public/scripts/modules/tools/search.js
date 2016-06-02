define(['jquery', 'jquery-ui'], function( $ ) {
    'use strict'

    var search = function( options ) {
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        return this;
    }


    search.prototype.init = function ( options ) {
        this.options = {
            autoComplete : true,
            autoCompleteUrl : '/search/autocomplete',
            autoCompleteMinLen : 1
        };
        $.extend( this.options, options );

        this.searchBox          = this.$( this.options.searchSelector );
        this.searchLocations    = this.$( this.options.locationsSelector );
        this.submitButton       = this.$( this.options.submitSelector );

        if ( this.options.autoComplete ) {
            this.initAutocomplete( this.options.autoCompleteUrl );
        }
    }

    search.prototype.initAutocomplete = function (url) {
        console.log(url);
        this.searchBox.autocomplete({
            'source': url,
            minLength: this.options.autoCompleteMinLen,
            select: this.onAutoCompleteSelect
        });
        console.log('autoComplete inited');
    }

    search.prototype.onAutoCompleteSelect = function ( event, ui ) {
        alert('olololo');
    }
    
    return search;
});
