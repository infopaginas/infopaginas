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

        updateFieldValue( place.formatted_address, latlng.lat, latlng.lng );

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

    if( formattedAddress.length > 0 ) {
        infowindow.open( map, marker );
    }

    infowindow.setContent( formattedAddress );

    // change address value when marker is dragged
    marker.addListener( 'dragend', function( event ) {
        var latlng = {
            lat: event.latLng.lat(),
            lng: event.latLng.lng()
        };

        geocodePlace( latlng, infowindow );
        updateFieldValue( formattedAddress, latlng.lat, latlng.lng )
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
function updateFieldValue( formattedAddress, lat, lon ) {
    addressInput.value = formattedAddress;

    if( latitudeInput && longitudeInput ) {
        latitudeInput.value = lat;
        longitudeInput.value = lon;
    }
}

/**
 * Init map with search box
 */
function initAutocomplete() {
    var myLatLng = new google.maps.LatLng( MAP_LAT, MAP_LNG );
    var infowindow = new google.maps.InfoWindow;
    var googleMapContainerId = 'google-map';

    if( latitudeInput && longitudeInput ) {
        latitudeInput.value = MAP_LAT;
        longitudeInput.value = MAP_LNG;
    }

    var $googleMapContainer = document.getElementById( googleMapContainerId );

    var map = new google.maps.Map( $googleMapContainer, {
        center: {
            lat: MAP_LAT,
            lng: MAP_LNG
        },
        zoom: MAP_ZOOM,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    } );

    google.maps.event.trigger( map, 'resize' );

    var marker = addMarker( myLatLng, map, infowindow, MARKER_VALUE );

    // Create the search box and link it to the UI element.
    var input = document.getElementById( 'pac-input' );
    var searchBox = new google.maps.places.SearchBox( input );

    map.controls[google.maps.ControlPosition.TOP_LEFT].push( input );

    // Bias the SearchBox results towards current map's viewport.
    map.addListener( 'bounds_changed', function() {
        searchBox.setBounds( map.getBounds() );
    } );

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener( 'places_changed', function() {
        var places = searchBox.getPlaces();

        if( places.length == 0 ) {
            return;
        }

        marker.setMap( null );

        var place = places.shift();

        var latlng = place.geometry.location;

        marker = addMarker( latlng, map, infowindow, place.formatted_address );
        updateFieldValue( place.formatted_address, latlng.lat(), latlng.lng() );
    } );
}