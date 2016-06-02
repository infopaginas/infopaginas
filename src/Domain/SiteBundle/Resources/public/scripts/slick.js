var slider = function() {

};

slider.prototype.carousel = function() {
    var $carousel = $( '.carousel-property' );
    var gallery = $('.gallery a');

    function showSliderScreen($widthScreen) {
        if ( $widthScreen <= '890' ) {
            if ( !$carousel.hasClass('slick-initialized' ) ) {
                $carousel.slick({
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    focusOnSelect: true,responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                centerPadding: '40px',
                                slidesToShow: 3
                            }
                        },
                       {
                           breakpoint: 480,
                           settings: {
                               slidesToShow: 2
                           }
                       }]
                   });
               }
            } else {
               if ( $carousel.hasClass( 'slick-initialized' ) ) {
                   $carousel.slick( 'unslick' );
               }
            }   
        }

    var widthScreen = $( window ).width();
    $( window ).ready( showSliderScreen( widthScreen ) ).resize(
        function () {
           var widthScreen = $( window ).width();
           showSliderScreen( widthScreen );
        }
    );

    gallery.simpleLightbox();

};

slick.prototype.run = function() {
    this.slider();
};

$(function () {
    var controller = new slider();
    controller.run();
});
