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

        sliderParams.slidesToShow  = 3;
        sliderParams.autoplaySpeed = 1500;
        sliderParams.dots = false;

        $( '.slider.suggested-slider' ).slick( sliderParams );
    });
});
