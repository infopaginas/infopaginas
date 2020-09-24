define(['jquery', 'slick'], function( $, Slick ) {
    'use strict';

    $( document ).ready(function () {
        let dots = screen.width >= 768;

        var sliderParams = {
            autoplay: true,
            focusOnSelect: false,
            autoplaySpeed: 3000,
            touchThreshold: 10,
            infinite: false,
            swipeToSlide: true,
            pauseOnDotsHover: true,
            arrows: true,
            dots: dots,
            mobileFirst: true,
            adaptiveHeight: false,
            variableWidth: false,
            slidesToShow: 1,
            responsive: [
                {
                    breakpoint: 500,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1450,
                    settings: {
                        slidesToShow: 4
                    }
                }
            ],
            prevArrow: '<span class="arrow prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></span>',
            nextArrow: '<span class="arrow next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></span>'
        };

        $( '.slider:not(.suggested-slider)' ).slick( sliderParams );

        var sliderSuggestedParams = {
            autoplay: true,
            focusOnSelect: false,
            autoplaySpeed: 3000,
            touchThreshold: 10,
            infinite: false,
            swipeToSlide: true,
            pauseOnDotsHover: true,
            arrows: true,
            dots: dots,
            mobileFirst: true,
            adaptiveHeight: false,
            variableWidth: false,
            slidesToShow: 1,
            responsive: [
                {
                    breakpoint: 500,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1100,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1450,
                    settings: {
                        slidesToShow: 4
                    }
                }
            ],
            prevArrow: $('.suggested-slider-section .prev.slick-arrow'),
            nextArrow: $('.suggested-slider-section .next.slick-arrow')
        };

        var slider = $( '.slider.suggested-slider' ).slick( sliderSuggestedParams );

        slider.on( 'afterChange', function( event, slick, currentSlide, nextSlide ) {
            var slidesToShow = document.getElementsByClassName( 'slick-active' ).length - 1;
            var slidesCount = document.getElementsByClassName( 'slick-slide' ).length;

            if ( currentSlide === ( slidesCount - slidesToShow ) ) {
                setTimeout( function() {
                    slider.slick( 'slickGoTo', 0 );
                }, 6000 );
            }
        });

        addSuggestedSliderEvent( slider );
    });

    function addSuggestedSliderEvent( slick ) {
        slick.on( 'beforeChange', function ( event, slick, currentSlide, nextSlide ) {
            if ( Math.abs( nextSlide - currentSlide ) == 1 ) {
                setSideClass( nextSlide - currentSlide > 0 );
            }
            else {
                setSideClass( nextSlide - currentSlide <= 0 );
            }
        })
    }

    function setSideClass( side ) {
        var suggestedSection = $( '.suggested-slider-section' );

        if ( side ) {
            suggestedSection.removeClass( 'arrow-left' );
            suggestedSection.addClass( 'arrow-right' )
        } else {
            suggestedSection.removeClass( 'arrow-right' );
            suggestedSection.addClass( 'arrow-left' )
        }
    }
});
