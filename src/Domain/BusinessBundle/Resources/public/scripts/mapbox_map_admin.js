$.getScript( 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js', function()
{
    $.getScript( 'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v3.1.0/mapbox-gl-geocoder.min.js', function()
    {
        var googleMapContainerId = 'google-map';
        var width = 24;
        var height = 34;

        if( latitudeInput && longitudeInput ) {
            latitudeInput.value = MAP_LAT;
            longitudeInput.value = MAP_LNG;
        }

        var $googleMapContainer = document.getElementById( googleMapContainerId );
        mapboxgl.accessToken = API_KEY;
        var center = DEFAULT_CENTER.split( ', ' );

        var map = new mapboxgl.Map({
            container: $googleMapContainer,
            style: 'mapbox://styles/mapbox/streets-v9',
            center: {
                lat: center[0],
                lng: center[1]
            },
            zoom: MAP_ZOOM,
            attributionControl: false
        });

        map.addControl( new mapboxgl.NavigationControl( { showCompass: false } ), 'bottom-right' );
        map.dragRotate.disable();
        map.touchZoomRotate.disableRotation();

        var el = document.createElement( 'div' );
        el.className = 'marker';
        el.style.width = width + 'px';
        el.style.height = height + 'px';

        var marker = new mapboxgl.Marker( el, {offset: [0, -height / 2]} )
            .setLngLat( [MAP_LNG, MAP_LAT] )
            .setDraggable( true )
            .addTo( map );

        var popup = new mapboxgl.Popup({
            closeButton: false,
            closeOnClick: false,
            offset: { 'bottom': [0, -height] }
        });

        marker.on( 'dragend', function() {
            popup.remove();
            map.removeLayer( 'places' );
            map.removeSource( 'places' );
            updatePopupAfterMarkerUpdate( marker, popup );

            var lngLat = marker.getLngLat();

            $.ajax(REVERSE_GEOCODING_ENDPOINT + lngLat.lng + ',' + lngLat.lat + '.json?access_token=' + mapboxgl.accessToken, {
                success: handleAddressChange,
                timeout: 1000,
            });

            updateCoordinates( lngLat.lat, lngLat.lng );
        });

        var geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            zoom: MAP_ZOOM,
            placeholder: "Search Box"
        });

        map.addControl( geocoder );

        $( document ).on( 'ifChecked', 'input[data-layer]', function() {
            var layerId = this.id;

            popup.remove();
            map.removeLayer('places');
            map.removeSource('places');

            map.setStyle( 'mapbox://styles/mapbox/' + layerId + '-v9' );

            map.on( 'styledata', function() {
                if (!map.getLayer( 'places' )) {
                    updatePopupAfterMarkerUpdate( marker, popup );
                }
            });
        });

        map.on( 'load', function() {
            updatePopupAfterMarkerUpdate( marker, popup );

            var oldMarker = marker;
            var oldPopup = popup;
            var el = document.createElement( 'div' );
            el.className = 'marker';
            el.style.width = width + 'px';
            el.style.height = height + 'px';

            geocoder.on( 'result', function( ev ) {
                oldPopup.remove();
                oldMarker.remove();
                map.removeLayer( 'places' );
                map.removeSource( 'places' );

                var marker = new mapboxgl.Marker( el, {offset: [0, -height / 2]} )
                    .setLngLat( [ev.result.geometry.coordinates[0], ev.result.geometry.coordinates[1]] )
                    .setDraggable( true )
                    .addTo( map );

                updatePopupAfterMarkerUpdate( marker, popup );

                marker.on( 'dragend', function() {
                    oldPopup.remove();
                    oldMarker.remove();
                    map.removeLayer( 'places' );
                    map.removeSource( 'places' );
                    updatePopupAfterMarkerUpdate( marker, popup );

                    var lngLat = marker.getLngLat();

                    updateCoordinates( lngLat.lat, lngLat.lng );
                });

                updateCoordinates( ev.result.geometry.coordinates[1], ev.result.geometry.coordinates[0] );
            });
        });

        /**
         * Update input value with new coordinates
         */
        function updateCoordinates ( lat, lon ) {
            updateFieldValue( latitudeInput, lat );
            updateFieldValue( longitudeInput, lon );
        }

        function updateFieldValue ( field, value ) {
            if ( field ) {
                field.value = value;
                $( field ).change();
            }
        }

        function updatePopupAfterMarkerUpdate( marker, popup ) {
            var coords = marker.getLngLat();
            var markerCoords = [coords.lng, coords.lat];
            var businessName = $( '#' + formId + '_name' )[0].value;

            map.addLayer({
                "id": "places",
                "type": "symbol",
                "source": {
                    "type": "geojson",
                    "data": {
                        "type": "FeatureCollection",
                        "features": [{
                            "type": "Feature",
                            "properties": {
                                "description": businessName,
                                "icon": "circle"
                            },
                            "geometry": {
                                "type": "Point",
                                "coordinates": markerCoords
                            }
                        }]
                    }
                },
                "layout": {
                    "icon-image": "{icon}-11",
                    "icon-allow-overlap": false,
                    "icon-offset": [0, -height / 2]
                }
            });

            map.on( 'mouseenter', 'places', function( e ) {
                var coordinates = e.features[0].geometry.coordinates.slice();
                var description = e.features[0].properties.description;

                while ( Math.abs( e.lngLat.lng - coordinates[0] ) > 180 ) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                popup.setLngLat( coordinates )
                    .setHTML( description )
                    .addTo( map );
            });

            map.on( 'mouseleave', 'places', function() {
                popup.remove();
            });
        }

        function handleAddressChange ( data ) {
            var city = '';
            var postcode = '';
            var streetAddress = '';

            var features = data.features;

            features.forEach( function( item ) {
                switch ( item.place_type[ 0 ] ) {
                    case 'address':
                        if ( item.address ) {
                            streetAddress = item.address + ' ';
                        }
                        streetAddress += item.text;
                        break;
                    case 'postcode':
                        postcode = item.text;
                        break;
                    case 'place':
                        city = item.text;
                        break;
                }
            } );

            if ( confirm(
                errorList.address.confirm_address_update + '\n' +
                'City: ' + city + '\n' +
                'Zip Code: ' + postcode + '\n' +
                'Street Address: ' + streetAddress
            ) ) {
                updateFieldValue( cityInput, city );
                updateFieldValue( zipCodeInput, postcode );
                updateFieldValue( streetAddressInput, streetAddress );
            }
        }
    });
});
