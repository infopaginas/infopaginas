define(['jquery', 'slick'], function( $, Slick ) {
    'use strict';

    $( document ).ready(function () {
        var sliderParams = {
            autoplay: true,
            autoplaySpeed: 5000,
            touchThreshold: 10,
            swipeToSlide: true,
            arrows: true,
            dots: true,
            mobileFirst: true,
            adaptiveHeight: false,
            variableWidth: false,
            slidesToShow: 1,
            lazyLoad: 'ondemand',
            prevArrow: '<span class="arrow prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></span>',
            nextArrow: '<span class="arrow next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></span>'
        };

        $( '.slider:not(.suggested-slider):not(.testimonials)' ).slick( sliderParams );

        var testimonialsSliderParams = sliderParams;
        testimonialsSliderParams.prevArrow = '<span class="arrow prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>';
        testimonialsSliderParams.nextArrow = '<span class="arrow next"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>';
        testimonialsSliderParams.autoplay = true;
        testimonialsSliderParams.adaptiveHeight = true;
        $( '.slider.testimonials' ).slick( testimonialsSliderParams );

      var sliderSuggestedParams = {
        autoplay: false,
        touchThreshold: 10,
        infinite: false,
        swipeToSlide: true,
        arrows: true,
        dots: false,
        mobileFirst: true,
        adaptiveHeight: false,
        variableWidth: false,
        slidesToShow: 1,
        responsive: [
          {
            breakpoint: 319,
            settings: {
              slidesToShow: 2
            }
          },
          {
            breakpoint: 620,
            settings: {
              slidesToShow: 3
            }
          },
          {
            breakpoint: 970,
            settings: {
              slidesToShow: 4
            }
          },
          {
            breakpoint: 1215,
            settings: {
              slidesToShow: 5
            }
          }
        ],
        prevArrow: $('.suggested-slider-section .prev.slick-arrow'),
        nextArrow: $('.suggested-slider-section .next.slick-arrow')
      };

        var slider = $( '.slider.suggested-slider' ).slick( sliderSuggestedParams );
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
