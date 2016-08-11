define(
    ['jquery',  'abstract/view', 'tools/directions', 'tools/select', 'bootstrap', 'select2', 'tools/star-rating'], 
    function ( $, view, directions, select) {
    'use strict';

    var searchpage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.events = {
            "#category-select change" : "selectCategory",
            "#neighborhood-select change" : "selectCategory"
        };
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        this.bindEvents();
        return this;
    }

    searchpage.prototype = new view;

    searchpage.prototype.init = function ( options ) {
        this.options = {};
        this.directions = new directions;
        $.extend( this.options, options );

        new select();
    }

    searchpage.prototype.selectCategory = function ( e ) {
        var route = $(e.currentTarget).find('option:selected').data('route');

        window.location = route;
    }
   
    return searchpage;
});
