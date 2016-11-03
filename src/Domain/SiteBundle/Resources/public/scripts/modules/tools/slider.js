define(['jquery', 'lightbox', 'slick'], function ($) {
    'use strict';

    var slider = function() {
    };

    slider.prototype.carousel = function() {
        var $carousel = $( '.carousel-property' ),
            gallery = $('.gallery a'),
            widthScreen = $( window ).width();

        function showSliderScreen($widthScreen) {
            if ( $widthScreen <= '890' ) {
                if ( !$carousel.hasClass('slick-initialized' ) ) {
                    $carousel.slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: false,
                        arrows: false,
                        focusOnSelect: true,responsive: [
                            {
                                breakpoint: 768,
                                settings: {
                                    // centerPadding: '40px',
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

        $( window ).ready( showSliderScreen( widthScreen ) ).resize(
            function () {
               var widthScreen = $( window ).width();
               showSliderScreen( widthScreen );
            }
        );
        
        gallery.simpleLightbox();

    };

    slider.prototype.run = function() {
        this.carousel();
    };

    $(function () {
        var controller = new slider();
        controller.run();
    });
});
