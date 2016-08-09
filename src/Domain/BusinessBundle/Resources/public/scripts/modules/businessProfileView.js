define( ['jquery', 'bootstrap', 'tools/select', 'slick', 'lightbox', 'tools/slider'], function( $, bootstrap, select, slick, lightbox, slider ) {
    'use strict';

    var businessProfileView = function() {
        this.run();
    };

    //setup required "listeners"
    businessProfileView.prototype.run = function() {
        new select();
    };

    return businessProfileView;
});
