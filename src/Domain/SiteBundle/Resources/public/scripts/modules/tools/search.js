define(['jquery', 'abstract/view', 'tools/geolocation', 'jquery-ui'], function( $, view, Geolocation ) {
    'use strict';

    var search = function( options ) {
        this.events = {};

        this.events[options.searchSelector + ' focus']  = 'onSearchBoxFocus';
        this.events[options.searchSelector + ' blur']   = 'onSearchBoxBlur';

        this.options = {
            autoComplete : true,
            searchMenu : false,
            autoCompleteUrl : Routing.generate('domain_search_autocomplete'),
            autoCompleteMinLen : 1,
            searchBaseUrl : '/businesses',
            mediaWidth: 480,
            mediaSearchSection: '.search-input.home',
            mediaCloseSection: '.searchCloseSection'
        };

        $.extend( this.options, options );

        this.init( this.options );
        this.bindEvents();

        $(options.searchHeaderButton).add( options.submitSelector ).on( 'click', function( evt ) {
            if( $( options.searchSelector ).val() === '' || !$( options.searchSelector ).val().trim().length ){
                evt.preventDefault();
                $( options.searchSelector ).val('');
                $( options.searchSelector ).css( {"border-color": "#FF3300"}) ;
                $( options.searchSelector ).addClass('search-input_red-placeholder');
                $( options.searchSelector ).parent().addClass( "validation-error" );
                $( options.searchSelector ).attr( "placeholder", $( options.searchSelector).data( "error-placeholder" ) );
            }
        });

        $( options.searchSelector ).on( 'input', function() {
            if( $( options.searchSelector ).val() !== '' ){
                $( options.searchSelector ).css( {"border-color": "#cadb53"} );
                $( options.searchSelector ).parent().removeClass( "validation-error" );
                $( options.searchSelector ).removeClass('search-input_red-placeholder');
                $( options.searchSelector ).attr( "placeholder", $( options.searchSelector).data( "placeholder" ) );
            }
        });

        $( this.options.searchSelector ).focus( function(){
            $( this.options.mediaSearchSection ).css( {"display": "inline-block"} );
        }.bind( this ));

        $( this.options.mediaCloseSection ).click( function(){
            $( this.options.mediaSearchSection ).css( {"display" : ""} )
        }.bind( this ));

        return this;
    };

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

        if ( this.options.searchMenu !== false ) {
            this.options.searchMenu.initQuickLinks( this.quickSearch.bind( this ) );
        }
    };

    search.prototype.initAutocomplete = function ( url ) {
        url = url || this.options.autoCompleteUrl;
        var self = this;
        this.searchBox.autocomplete({
            'source': function( term, callback ) {
                $.getJSON( url, { q : term.term }, callback );
            } ,
            minLength: this.options.autoCompleteMinLen,
            create: function() {
                $( this ).data( 'ui-autocomplete' )._renderItem = self.returnAutocompleteDataElement;
                $(this).prev('.ui-helper-hidden-accessible').remove();
            },
            select: this.onAutoCompleteSelect.bind( self ),
            change: function( event, ui ){},
            close: function( event, ui ){},
            open: function() {
              $('.ui-autocomplete').css('width', '500px');
              $('.ui-autocomplete').css('background-color', 'rgba(122, 122, 122, 0.95)');
            }
        });
    };

    search.prototype.onAutoCompleteSelect = function ( event, ui ) {
        this.searchBox.val( ui.item.name );
        event.preventDefault();
        this.onSearchBoxBlur();
        return true;
    };

    search.prototype.onSearchBoxFocus = function () {
        this.searchHintBox.show();
    };

    search.prototype.onSearchBoxBlur = function () {
        this.searchHintBox.hide();
    };

    search.prototype.quickSearch = function ( searchQuery ) {
        this.searchBox.val( searchQuery );
        this.submitButton.first().click();
    };

    search.prototype.returnAutocompleteDataElement = function ( ul, item ) {
        return $( "<li>" )
            .append( $( "<a></a>" )["html"]( item.data ) )
            .attr( "data-value",  decodeURIComponent( item.data ) )
            .attr( "data-name",  item.name )
            .appendTo( ul );
    };


    return search;
});
