define(['jquery'], function ($) {
    'use strict';

    var rating = function() {
        var cards = $('.card-item');

        cards.each(function(index, card) {
            var starRating = $(card).find( '.star-rating .fa ' );
            starRating.each(function() {
                if (parseInt( $(this).siblings( 'input.rating-value' ).val()) >= parseInt( $( this ).data( 'rating' ))) {
                    return $( this ).removeClass( 'fa-star' ).addClass( 'fa-star-selected' );
                } else {
                    return $( this ).removeClass( 'fa-star-selected' ).addClass( 'fa-star' );
                }
            });
        });

        return this;
    };

    rating.prototype.starsRating = function() {
        var starRating = $( '.star-rating.selectable .fa' );

        starRating.on('click', function(event) {
            $( event.currentTarget ).parent().addClass( 'active' );
            $( event.currentTarget ).siblings( 'input.rating-value' ).val( $( this ).data( 'rating' ));

            return starRating.each(function() {
                if( $( this ).parent().hasClass( 'active' ) ){
                    if (parseInt( $(this).siblings( 'input.rating-value' ).val()) >= parseInt( $( this ).data( 'rating' ))) {
                        return $( this ).removeClass( 'fa-star' ).addClass( 'fa-star-selected' );
                    } else {
                        return $( this ).removeClass( 'fa-star-selected' ).addClass( 'fa-star' );
                    }
                }
           });
        });
    };

    rating.prototype.run = function() {
        this.starsRating();
    };

    $( function () {
        var controller = new rating();
        controller.run();
    });
});
