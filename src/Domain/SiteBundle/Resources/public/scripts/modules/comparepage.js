define(
    ['jquery',  'abstract/view','bootstrap', 'select2', 'tools/select', 'tools/star-rating', 'slick', 'tools/comparison'], 
    function ( $, view  ) {
    'use strict';

    var comparepage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.events = {
            "#category-select change" : "selectCategory"
        };
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        this.bindEvents();
        return this;
    }

    comparepage.prototype = new view;

    comparepage.prototype.init = function ( options ) {
        this.options = {};
        $.extend( this.options, options );
    }

    comparepage.prototype.selectCategory = function ( e ) {
        var route = $( e.currentTarget ).find( 'option:selected' ).data( 'route' );

        window.location = route;
    }
   
    return comparepage;
});
