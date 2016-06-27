define(
    ['jquery',  'abstract/view','bootstrap', 'select2', 'slick', 'photo-gallery'], 
    function ( $, view  ) {
    'use strict';

    var searchpage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.init( options );
        return this;
    }

    searchpage.prototype = new view;

    searchpage.prototype.init = function ( options ) {
        this.options = {};
        $.extend( this.options, options );
    }
   
    return searchpage;
});
