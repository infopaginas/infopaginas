$( document ).ready( function() {
    var button = $( '#show-more-button' );
    var page   = button.data( 'page' );
    var spinner = $( '#spinner' );
    var requestSend = false;

    button.on( 'click', function( e ) {
        e.preventDefault();

        if ( !requestSend ) {
            requestSend = true;
            var button          = $( this );
            var areaSlug        = button.data( 'area' );
            var categorySlug    = button.data( 'category' );

            disableButton( button );
            showSpinner();
            page++;

            $.ajax({
                url: Routing.generate( 'emergency_catalog', {areaSlug: areaSlug, categorySlug: categorySlug, page: page} ),
                dataType: 'JSON',
                type: 'POST',
                success: function( response ) {
                    if ( response.html ) {
                        $( '#emergency-catalog' ).append( response.html );
                        requestSend = false;
                        enableButton( button );
                    }

                    hideSpinner();
                }
            });
        }
    });

    $( document ).on ( 'click', '.working-hours-list .day[data-day]', function( e ) {
        var elem = $( this );
        var parent = elem.parent();
        var currentDayValue = elem.data( 'day' );
        var todayTextValue  = elem.data( 'text' );
        var currentDayBlock = elem.find( '.hour__day' );

        if ( parent.hasClass( 'hide-children' ) ) {
            parent.removeClass( 'hide-children' );
            currentDayBlock.html( currentDayValue );
        } else {
            parent.addClass( 'hide-children' );
            currentDayBlock.html( todayTextValue );
        }
    });

    function showSpinner() {
        spinner.removeClass( 'hidden' );
    }

    function hideSpinner() {
        spinner.addClass( 'hidden' );
    }

    function disableButton( button ) {
        $( button ).attr( 'disabled', 'disabled' );
    }

    function enableButton( button ) {
        $( button ).removeAttr( 'disabled' );
    }
});
