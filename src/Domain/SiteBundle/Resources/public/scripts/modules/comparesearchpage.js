define(
    ['jquery', 'tools/directions'], function ( $, directions ) {
    'use strict';

    var compareSearchPage = function () {
        this.init();

        return this;
    };

    compareSearchPage.prototype.init = function () {
        this.options = {
            directions: new directions
        };

        this.options.directions.bindEventsDirections();
    };

    return compareSearchPage;
});
