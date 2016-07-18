define(['jquery', 'tools/slick'], function ($) {
    'use strict';

    var comparison = function() {
    };

    comparison.prototype.comparisonCarousel = function() {
        var $carousel = $( '.carousel-property' ),
            nextButton = $( '.nextSlide' ),
            prevButton = $( '.prevSlide' ),
            sliderWrap = $( '.comparison-wrap'),
            sliderBlock = $( '.comparison-block'),
            cardItem = $( '.card-item'),
            cardItemWidth = 220,
            slickwrap = $( '.slick-track'),
            closeButton = $( '.times' );

        $carousel.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: false,
            dots: false,
            accessibility: false,
            swipe: true,
            // variableWidth: true,
            prevArrow: $( '.prevSlide' ),
            nextArrow: $( '.nextSlide' ),
        });


        sliderWrap.width( cardItemWidth * cardItem.length );

        closeButton.on( 'click', function() {
            $( this ).closest( '.card-item' ).remove();
            sliderWrap.width( cardItemWidth * ( $( '.card-item').length - 1));
        });

        $carousel.on( 'swipe', function(event, slick, direction){
            if(direction == "left") {
                var leftPos = sliderBlock.scrollLeft();
                sliderBlock.animate({ scrollLeft: leftPos + cardItemWidth }, 800 );
            } else {
                var leftPos = sliderBlock.scrollLeft();
                sliderBlock.animate({ scrollLeft: leftPos - cardItemWidth }, 800 );
            }
        });

        nextButton.on( 'click', function() {
            var leftPos = sliderBlock.scrollLeft();
            sliderBlock.animate({ scrollLeft: leftPos + cardItemWidth }, 800 );
        });

        prevButton.on( 'click', function() {
            var leftPos = sliderBlock.scrollLeft();
            sliderBlock.animate({ scrollLeft: leftPos - cardItemWidth }, 800 );
        });
    }

    comparison.prototype.run = function() {
        this.comparisonCarousel();
    };

    $(function () {
        var controller = new comparison();
        controller.run();
    });
});
