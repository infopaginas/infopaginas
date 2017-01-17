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

        var highlightsElems = ['address', 'phone', 'hours', 'brands', 'social', 'payment', 'share'];

        highlightsElems.forEach(function(elemName){
          var maxRowHeight = (elemName === 'share') ? 88 : 0;
          $('.highlights__item_'+elemName+'-row').each(function(i, element){
            var ulHeight = $(element).find('ul').height();
            maxRowHeight = ulHeight > maxRowHeight ? ulHeight : maxRowHeight;
          });
          if(maxRowHeight === 0){
            $('.highlights__item_'+elemName+'-row').each(function(i, element){
              $(element).css('display', 'none');
            });
          } else {
            $('.highlights__item_'+elemName+'-row').each(function(i, element){
              var currentelement = $(element).find('ul');
              currentelement.css('height', maxRowHeight+'px');
              if(elemName === 'share'){
                $(element).css('height', maxRowHeight+'px')
              } 
            });
          }
        })

        this.options.directions.bindEventsDirections();
    };

    return compareSearchPage;
});
