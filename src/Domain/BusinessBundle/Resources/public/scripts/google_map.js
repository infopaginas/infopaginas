var googleMapContainerId = 'google-map';

/**
 * Get address by coordinates
 */
function geocodePlace( latlng, infowindow ){
    var geocoder = new google.maps.Geocoder();

    var location = {
        'location': latlng
    };

    geocoder.geocode( location, function( results, status ) {
        if( results.length == 0 ) {
            return false;
        }

        var place = results.shift();
        infowindow.setContent( place.formatted_address );

        return results.shift();
    });
}

/**
 * Create marker
 */
function addMarker( latlng, map, infowindow, formattedAddress ) {
    var marker = new google.maps.Marker( {
        position: latlng,
        map: map,
        draggable: true,
        title: formattedAddress
    } );

    if( formattedAddress.length > 0 ){
        infowindow.open( map, marker );
    }

    infowindow.setContent( formattedAddress );

    // change address value when marker is dragged
    marker.addListener( 'dragend', function( event ) {
        var latlng = {
            lat: event.latLng.lat(),
            lng: event.latLng.lng()
        };

        geocodePlace(latlng, infowindow);
    } );

    // show and hide mark description
    marker.addListener( 'click', function( event ) {
        infowindow.open( map, marker );
    } );

    return marker;
}

/**
 * Update input value with new address
 */
function updateFieldValue( formattedAddress ) {
    addressInput.value = formattedAddress;
}

/**
 * Init map with search box
 */
function initAutocomplete() {
    var infowindow = new google.maps.InfoWindow;

    var $mapContainer = document.getElementById( googleMapContainerId );

    var map = new google.maps.Map( $mapContainer, {
        center: {
            lat: MAP_LAT,
            lng: MAP_LNG
        },
        zoom: MAP_ZOOM,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    } );
}
