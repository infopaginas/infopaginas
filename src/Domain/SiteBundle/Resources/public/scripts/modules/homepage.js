define(
    ['jquery', 'bootstrap', 'tools/search'], 
    function ( $, bootstrap, Search, require ) {
    'use strict';

    var searchOptions = {
        selector : '.search-form',
        searchSelector : '#searchBox',
        locationsSelector : '#searchLocation',
        submitSelector : '#searchButton'

    }
    var search = new Search(searchOptions);
});
