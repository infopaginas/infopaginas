$( document ).ready( function() {
    var button = $( '#show-more-button' );
    var page   = button.data( 'page' );
    var spinner = $( '#spinner' );
    var catalogBlock = $( '#emergency-catalog' );
    var serviceFilters = $( '.service-filter-block input[type="checkbox"]' );
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

    $( document ).on( 'click', '.filter-letters span.letter', function( e ) {
        if ( !requestSend ) {
            $( '.filter-letters span.letter.checked' ).removeClass( 'checked' );
            $( this ).addClass( 'checked' );

            getPageContent( false );
        }
    });

    $( document ).on( 'change', '.service-filter-block input[type="checkbox"]', function( e ) {
        if ( !requestSend ) {
            getPageContent( false );
        }
    });

    // sorting
    $( 'input[type=radio][name=order]' ).on( 'change', function() {
        if ( !requestSend ) {
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
            var characterFilter = $( '.filter-letters span.letter.checked' ).data( 'filter' );

            if ( usePage ) {
                page++;
            } else {
                page = 1;
                catalogBlock.empty();
            }

            var data = {
                page:  page,
                order: getSorting(),
                charFilter: characterFilter,
                serviceFilter: getServiceFilter()
            };

            if ( geoLocationAvailable ) {
                data.lat = latitude;
                data.lng = longitude;
            }

            disableButton( button );
            disableSorting();
            disableServiceFilters();
            showSpinner();

            $.ajax({
                url: Routing.generate( 'emergency_catalog', {areaSlug: areaSlug, categorySlug: categorySlug} ),
                data: data,
                dataType: 'JSON',
                type: 'POST',
                success: function( response ) {
                    if ( response.html ) {
                        $( '#emergency-catalog' ).append( response.html );
                        enableButton( button );
                    } else if ( !usePage ) {
                        enableButton( button );
                    }

                    requestSend = false;

                    enableSorting();
                    enableServiceFilters();
                    hideSpinner();
                }
            });
        }
    }

    function getSorting() {
        return $( 'input[type=radio][name=order]:checked' ).val();
    }

    function getServiceFilter() {
        var filters = $( '.service-filter-item input[type=checkbox]:checked');
        var serviceIds = [];

        $.each( filters, function() {
            var serviceId = $( this ).data( 'service-id' );

            serviceIds.push( serviceId );
        });

        return serviceIds;
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
        $( button ).removeClass( 'button--action' );
        $( button ).addClass( 'disabled' );
    }

    function enableButton( button ) {
        $( button ).removeAttr( 'disabled' );
        $( button ).removeClass( 'disabled' );
        $( button ).addClass( 'button--action' );
    }

    function enableServiceFilters() {
        serviceFilters.removeAttr( 'disabled' );
    }

    function disableServiceFilters() {
        serviceFilters.attr( 'disabled', 'disabled' );
    }
});
