define(['jquery', 'slick'], function( $, Slick ) {
    'use strict';

    $( document ).ready(function () {
        var sliderParams = {
            autoplay: true,
            autoplaySpeed: 5000,
            arrows: true,
            dots: true,
            responsive: true,
            mobileFirst: true,
            adaptiveHeight: false,
            variableWidth: false,
            slidesToShow: 1,
            prevArrow: '<span class="arrow prev"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></span>',
            nextArrow: '<span class="arrow next"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></span>'
        };

        $( '.slider:not(.suggested-slider)' ).slick( sliderParams );

        sliderParams.slidesToShow = 5;
        sliderParams.autoplaySpeed = 1500;
        sliderParams.dots = false;
        sliderParams.autoplay = false;
        sliderParams.prevArrow = $( '.suggested-slider-section .prev.slick-arrow' );
        sliderParams.nextArrow = $( '.suggested-slider-section .next.slick-arrow' );

        var slider = $( '.slider.suggested-slider' ).slick(sliderParams);

        addSuggestedSliderEvent (slider);
    });

    function addSuggestedSliderEvent (slick) {
        slick.on( 'beforeChange', function (event, slick, currentSlide, nextSlide) {
            if (Math.abs(nextSlide - currentSlide) == 1) {
                setSideClass(nextSlide - currentSlide > 0);
            }
            else {
                setSideClass(nextSlide - currentSlide <= 0);
            }
        })
    }

    function setSideClass (side) {
        var suggestedSection = $( '.suggested-slider-section' );

        if (side) {
            suggestedSection.removeClass('arrow-left');
            suggestedSection.addClass('arrow-right')
        } else {
            suggestedSection.removeClass('arrow-right');
            suggestedSection.addClass('arrow-left')
        }
    }
});
