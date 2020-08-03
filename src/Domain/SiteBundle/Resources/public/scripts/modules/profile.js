define(['jquery', 'slick'], function( $, Slick ) {
    'use strict';

    $( document ).ready(function () {
        var suggestedSlider = $( '.slider.suggested-slider' ),
            amazonAffiliateSlider = $( '.slider.amazon-affiliate' ),
            testimonialSlider = $( '.slider.testimonials' );

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
        $( '.slider.gallery' ).slick( sliderParams );

        var testimonialsSliderParams = sliderParams;
        testimonialsSliderParams.prevArrow = '<span class="arrow prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>';
        testimonialsSliderParams.nextArrow = '<span class="arrow next"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>';
        testimonialsSliderParams.autoplay = true;
        testimonialsSliderParams.adaptiveHeight = true;
        testimonialSlider.slick( testimonialsSliderParams );

        var amazonAffiliateSliderParams = sliderParams;
        amazonAffiliateSliderParams.slidesToShow = 2;
        amazonAffiliateSlider.slick( amazonAffiliateSliderParams );

        var suggestedBusinessesCount = suggestedSlider.find('.slider__item').length;
        var slidesToShow_sm = suggestedBusinessesCount < 2 ? suggestedBusinessesCount : 2;
        var slidesToShow_md = suggestedBusinessesCount < 3 ? suggestedBusinessesCount : 3;
        var slidesToShow_lg = suggestedBusinessesCount < 4 ? suggestedBusinessesCount : 4;
        var slidesToShow_xl = suggestedBusinessesCount < 5 ? suggestedBusinessesCount : 5;

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
            responsive: [
                {
                    breakpoint: 319,
                    settings: {
                        slidesToShow: slidesToShow_sm
                    }
                },
                {
                    breakpoint: 620,
                    settings: {
                        slidesToShow: slidesToShow_md
                    }
                },
                {
                    breakpoint: 970,
                    settings: {
                        slidesToShow: slidesToShow_lg
                    }
                },
                {
                    breakpoint: 1215,
                    settings: {
                        slidesToShow: slidesToShow_xl
                    }
                }
            ],
            prevArrow: $( '.suggested-slider-section .prev.slick-arrow' ),
            nextArrow: $( '.suggested-slider-section .next.slick-arrow' )
        };

        var slider = suggestedSlider.slick( sliderSuggestedParams );
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
