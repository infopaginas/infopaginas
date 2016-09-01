define(['jquery'], function ( $ ) {
    'use strict';

    var comparison = function() {
        this.$ = function( selector ) {
            return $( '.compare-wrapper' ).find( selector );
        }
    };

    comparison.prototype.heightSetter = function(){
        comparison.prototype.resetHeightSetter();
        var listOfCardChildren= $( ".card-item" ).first().children();
        var listOfTitles = $( "#compare-block" ).first().children();
        listOfCardChildren.each( function( i, el ){
            var elementClass = el.className.split(' ').join('.');
            var elements = $( '.' + elementClass );
            var maxHeight = Math.max.apply( null, elements.map( function ()
            {
                return $( this ).height();
            }).get());
            elements.height( maxHeight );
            $( listOfTitles[i] ).height( maxHeight );
        });
    };

    comparison.prototype.resetHeightSetter = function(){
        var listOfCardChildren= $( ".card-item" ).first().children();
        listOfCardChildren.each( function( i, el ){
            var elementClass = el.className.split(' ').join('.');
            var elements = $( '.' + elementClass );
            elements.each( function( i, el ){
                el.style.height = "";
            })
        });
    };

    comparison.prototype.comparisonCarousel = function() {

        var sliderWrap = $( '.comparison-wrap' ),
            cardItemWidth = 220,
            closeButton = $( '.times' );

        closeButton.on( 'click', function() {
            $( this ).closest( '.card-item' ).remove();
            sliderWrap.width( cardItemWidth * ( $( '.card-item' ).length - 1));
            comparison.prototype.heightSetter();
        });

    };

    comparison.prototype.run = function() {
        this.comparisonCarousel();
        this.heightSetter();
    };

    $(function () {
        var controller = new comparison();
        controller.run();
    });
});
