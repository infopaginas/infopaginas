define(['jquery', 'tools/slick'], function ($) {
    'use strict';

    var comparison = function() {
    };

    comparison.prototype.heightSetter = function(){
        comparison.prototype.resetHeightSetter();
        var listOfCardChildren= $(".card-item")[0].children;
        var listOfTitles = $("#compare-block")[0].children;
        for(var i=0; i<listOfCardChildren.length; i++){
            var elements = document.getElementsByClassName(listOfCardChildren[i].className);
            var maxHeight =  Math.max.apply(null, $.makeArray( elements ).map(function (el) {
                return el.clientHeight;
            }));
            for(var j=0; j<elements.length; j++){
                $(elements[j]).height(maxHeight);
            }
            $(listOfTitles[i]).height(maxHeight)
        }
    };

    comparison.prototype.resetHeightSetter = function(){
        var listOfCardChildren= $(".card-item")[0].children;
        for(var i=0; i<listOfCardChildren.length; i++){
            var elements = document.getElementsByClassName(listOfCardChildren[i].className);
            for(var j=0; j<elements.length; j++){
                $(elements[j])[0].style.height = "";
            }
        }
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
            prevArrow: $( '.prevSlide' ),
            nextArrow: $( '.nextSlide' ),
        });


        sliderWrap.width( cardItemWidth * cardItem.length );

        closeButton.on( 'click', function() {
            $( this ).closest( '.card-item' ).remove();
            sliderWrap.width( cardItemWidth * ( $( '.card-item').length - 1));
            comparison.prototype.heightSetter();
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
        this.heightSetter();
    };

    $(function () {
        var controller = new comparison();
        controller.run();
    });
});
