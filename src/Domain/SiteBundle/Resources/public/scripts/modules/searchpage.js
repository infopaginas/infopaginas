define(
    ['jquery',  'abstract/view','bootstrap', 'select2', 'tools/select', 'tools/star-rating'], 
    function ( $, view  ) {
    'use strict';

    var searchpage = function ( options ) {
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

    searchpage.prototype = new view;

    searchpage.prototype.init = function ( options ) {
        this.options = {};
        $.extend( this.options, options );
    }

    searchpage.prototype.selectCategory = function ( e ) {
        var route = $(e.currentTarget).find('option:selected').data('route');

        window.location = route;
    }
   
    return searchpage;
});
