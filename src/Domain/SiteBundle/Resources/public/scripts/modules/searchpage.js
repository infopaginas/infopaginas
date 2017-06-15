define(
    ['jquery',  'abstract/view', 'tools/directions', 'tools/select', 'bootstrap', 'select2', 'tools/starRating'],
    function ( $, view, directions, select) {
    'use strict';

    var searchpage = function ( options ) {
        options = options || {};
        options.selector = options.selector || 'body';
        this.events = {
            ".category-select change"       : "selectCategory",
            ".neighborhood-select change"   : "selectCategory",
            ".order-by-select change"       : "selectCategory",
            ".view-phone select2:opening"      : "viewProfile"
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
        this.directions.bindEventsDirections();

        $.extend( this.options, options );

        new select();
    }

    searchpage.prototype.selectCategory = function ( e ) {
        var route = $(e.currentTarget).find('option:selected').data('route');

        window.location = route;
    }

    searchpage.prototype.viewProfile = function (e) {
        var id = $(e.currentTarget).data('business');

        $.get(Routing.generate('domain_business_register_view', {id: id}));
    }

    return searchpage;
});
