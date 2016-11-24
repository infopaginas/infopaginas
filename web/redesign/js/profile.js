define(['jquery', 'slick'], function( $, Slick ) {
    'use strict';

    $(document).ready(function () {
        $('.slider').slick({
            autoplay: true,
            autoplaySpeed: 5000,
            arrows: true,
            dots: true,
            responsive: true,
            adaptiveHeight: true,
            variableWidth: false,
            slidesToShow: 1,
            prevArrow: '<span class="arrow prev"></span>',
            nextArrow: '<span class="arrow next"></span>'
        });
    });
});
