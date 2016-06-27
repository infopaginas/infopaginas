define(
    ['jquery', 'abstract/view'],
    function( $, view) {
    'use strict'




var rating = function() {
};

rating.prototype.starsRating = function() {
    var starRating = $( '.star-rating .fa' );

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
