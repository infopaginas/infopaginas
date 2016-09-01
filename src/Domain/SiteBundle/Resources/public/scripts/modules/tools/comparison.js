define(['jquery', 'tools/slick'], function ($) {
    'use strict';

    var comparison = function() {
        this.$ = function( selector ) {
            return $( '.compare-wrapper' ).find( selector );
        }
    };

    comparison.prototype.heightSetter = function(){
        comparison.prototype.resetHeightSetter();
        var listOfCardChildren= $(".card-item").first().children();
        var listOfTitles = $("#compare-block").first().children();
        listOfCardChildren.each(function(i, el){
            var elementClass = el.className.split(' ').join('.');
            var elements = $('.' + elementClass);
            var maxHeight = Math.max.apply(null, elements.map(function ()
            {
                return $(this).height();
            }).get());
            elements.height(maxHeight);
            $(listOfTitles[i]).height(maxHeight);
        });
    };

    comparison.prototype.resetHeightSetter = function(){
        var listOfCardChildren= $(".card-item").first().children();
        listOfCardChildren.each(function(i, el){
            var elementClass = el.className.split(' ').join('.');
            var elements = $('.' + elementClass);
            elements.each(function(i, el){
                el.style.height = "";
            })
        });
    };

    // comparison.prototype.comparisonCarousel = function() {
    //     var $carousel = $( '.carousel-property' ),
    //         nextButton = $( '.nextSlide' ),
    //         prevButton = $( '.prevSlide' ),
    //         sliderWrap = $( '.comparison-wrap'),
    //         sliderBlock = $( '.comparison-block'),
    //         cardItem = $( '.card-item'),
    //         cardItemWidth = 220,
    //         slickwrap = $( '.slick-track'),
    //         closeButton = $( '.times' );
    //
    //     $carousel.slick({
    //         slidesToShow: 1,
    //         slidesToScroll: 1,
    //         infinite: false,
    //         dots: false,
    //         accessibility: false,
    //         swipe: true,
    //         prevArrow: $( '.prevSlide' ),
    //         nextArrow: $( '.nextSlide' ),
    //     });
    //
    //
    //     sliderWrap.width( cardItemWidth * cardItem.length );
    //
    //     closeButton.on( 'click', function() {
    //         $( this ).closest( '.card-item' ).remove();
    //         sliderWrap.width( cardItemWidth * ( $( '.card-item').length - 1));
    //         comparison.prototype.heightSetter();
    //     });
    //
    //     $carousel.on( 'swipe', function(event, slick, direction){
    //         if(direction == "left") {
    //             var leftPos = sliderBlock.scrollLeft();
    //             sliderBlock.animate({ scrollLeft: leftPos + cardItemWidth }, 800 );
    //         } else {
    //             var leftPos = sliderBlock.scrollLeft();
    //             sliderBlock.animate({ scrollLeft: leftPos - cardItemWidth }, 800 );
    //         }
    //     });
    //
    //     nextButton.on( 'click', function() {
    //         var leftPos = sliderBlock.scrollLeft();
    //         sliderBlock.animate({ scrollLeft: leftPos + cardItemWidth }, 800 );
    //     });
    //
    //     prevButton.on( 'click', function() {
    //         var leftPos = sliderBlock.scrollLeft();
    //         sliderBlock.animate({ scrollLeft: leftPos - cardItemWidth }, 800 );
    //     });
    // }

    comparison.prototype.run = function() {
        // this.comparisonCarousel();
        this.heightSetter();
    };

    $(function () {
        var controller = new comparison();
        controller.run();
    });
});
