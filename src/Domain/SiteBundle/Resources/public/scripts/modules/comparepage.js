define(
    ['jquery',  'abstract/view', 'tools/select', 'bootstrap', 'select2', 'tools/star-rating', 'slick', 'tools/comparison'], 
    function ( $, view, select  ) {
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

        new select();
    }

    comparepage.prototype.selectCategory = function ( e ) {
        var route = $( e.currentTarget ).find( 'option:selected' ).data( 'route' );

        window.location = route;
    }
   
    return comparepage;
});
