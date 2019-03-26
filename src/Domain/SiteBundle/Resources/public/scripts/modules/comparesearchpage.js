define(
    ['jquery', 'tools/redirect'], function ( $, Redirect ) {
    'use strict';

    var compareSearchPage = function () {
        this.init();

        return this;
    };

    compareSearchPage.prototype.init = function () {
        this.redirect = new Redirect;

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

        this.bindEventsDirections();
    };

    compareSearchPage.prototype.bindEventsDirections = function () {
        $( document ).on( 'click', '.get-dir', function( e, latlngEvent ) {
            var latlng = getDirection( e, latlngEvent );
            var self = this;

            if ( navigator.geolocation ) {
                navigator.geolocation.getCurrentPosition(function( position ) {
                    foundLocation( position, self, latlng );
                }, notAllowedLocation);

                function notAllowedLocation( error ) {
                    redirectOnDirection( latlng, 0 );
                }

                function foundLocation( position, self, latlng ) {
                    var currentCoordinates = position.coords.latitude + ',' + position.coords.longitude;
                    redirectOnDirection( latlng, currentCoordinates );
                }
            } else {
                redirectOnDirection( latlng, 0 );
            }
        });
    };

    function getDirection( e, latlngEvent ) {
        var latlng;

        if ( e ) {
            latlng = $( e.currentTarget ).data( 'latlng' );
            var id = $( e.currentTarget ).data( 'id' );
            $( document ).trigger( 'trackingInteractions', ['directionButton', id] );
        } else if ( latlngEvent ) {
            latlng = latlngEvent;
        }

        return latlng;
    }

    function redirectOnDirection( latlng, currentCoordinates ) {
        window.open(Routing.generate(
            'domain_search_show_directions',
            {
                targetCoordinates:  latlng,
                currentCoordinates: currentCoordinates
            }
        ));
    }

    return compareSearchPage;
});
