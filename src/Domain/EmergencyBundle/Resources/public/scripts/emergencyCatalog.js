$( document ).ready( function() {
    var button = $( '#show-more-button' );
    var page   = button.data( 'page' );
    var spinner = $( '#spinner' );
    var catalogBlock = $( '#emergency-catalog' );
    var requestSend = false;
    var geoLocationAvailable = false;
    var latitude;
    var longitude;

    initGeoLocation();

    // get more elements
    button.on( 'click', function( e ) {
        e.preventDefault();

        getPageContent( true );
    });

    // working hours show/hide
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

    // sorting
    $( 'input[type=radio][name=order]' ).on( 'change', function() {
        if ( !requestSend ) {
            catalogBlock.empty();
            getPageContent( false );
        }
    });

    function initGeoLocation() {
        if ( 'geolocation' in navigator ) {
            navigator.geolocation.getCurrentPosition( function( position ) {
                latitude  = position.coords.latitude;
                longitude = position.coords.longitude;

                geoLocationAvailable = true;

                enableSorting();
            });
        }
    }

    function getPageContent( usePage ) {
        if ( !requestSend ) {
            requestSend = true;

            var areaSlug        = button.data( 'area' );
            var categorySlug    = button.data( 'category' );

            if ( usePage ) {
                page++;
            } else {
                page = 1;
            }

            var data = {
                page:  page,
                order: getSorting()
            };

            if ( geoLocationAvailable ) {
                data.lat = latitude;
                data.lng = longitude;
            }

            disableButton( button );
            disableSorting();
            showSpinner();

            $.ajax({
                url: Routing.generate( 'emergency_catalog', {areaSlug: areaSlug, categorySlug: categorySlug} ),
                data: data,
                dataType: 'JSON',
                type: 'POST',
                success: function( response ) {
                    if ( response.html ) {
                        $( '#emergency-catalog' ).append( response.html );
                        requestSend = false;
                        enableButton( button );
                    }

                    enableSorting();
                    hideSpinner();
                }
            });
        }
    }

    function getSorting() {
        return $( 'input[type=radio][name=order]:checked' ).val();
    }

    function showSpinner() {
        spinner.removeClass( 'hidden' );
    }

    function hideSpinner() {
        spinner.addClass( 'hidden' );
    }

    function disableSorting() {
        $( 'input[type=radio][name=order]' ).attr( 'disabled', 'disabled' );
    }

    function enableSorting() {
        if ( geoLocationAvailable ) {
            $( 'input[type=radio][name=order]' ).removeAttr( 'disabled' );
        }
    }

    function disableButton( button ) {
        $( button ).attr( 'disabled', 'disabled' );
    }

    function enableButton( button ) {
        $( button ).removeAttr( 'disabled' );
    }
});
