define( ['jquery', 'bootstrap', 'tools/select', 'slick', 'lightbox', 'business/tools/slider', 'tools/directions'], function( $, bootstrap, select, slick, lightbox, slider, directions ) {
    'use strict';

    var businessProfileView = function() {
        this.run();
    };

    //setup required "listeners"
    businessProfileView.prototype.run = function() {
        new select();
        new directions();
    };

    return businessProfileView;
});
