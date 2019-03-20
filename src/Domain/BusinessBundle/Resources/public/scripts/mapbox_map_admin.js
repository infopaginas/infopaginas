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
            style: 'mapbox://styles/mapbox/basic-v9',
            center: {
                lat: center[0],
                lng: center[1]
            },
            zoom: MAP_ZOOM,
            attributionControl: false
        });

        map.addControl( new mapboxgl.NavigationControl(), 'bottom-right' );

        $( document ).on( 'ifChecked', 'input[data-layer]', function() {
            var layerId = this.id;
            map.setStyle( 'mapbox://styles/mapbox/' + layerId + '-v9' );
        });

        var el = document.createElement( 'div' );
        el.className = 'marker';
        el.style.width = width + 'px';
        el.style.height = height + 'px';

        var marker = new mapboxgl.Marker( el, {offset: [0, -height / 2]} )
            .setLngLat( [MAP_LNG, MAP_LAT] )
            .setDraggable( true )
            .addTo( map );

        marker.on( 'dragend', function() {
            var lngLat = marker.getLngLat();

            updateFieldValue( lngLat.lat, lngLat.lng );
        });

        var geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            zoom: MAP_ZOOM,
            placeholder: "Search Box"
        });

        map.addControl( geocoder );

        map.on( 'load', function() {
            var oldMarker = marker;
            var el = document.createElement( 'div' );
            el.className = 'marker';
            el.style.width = width + 'px';
            el.style.height = height + 'px';

            geocoder.on( 'result', function( ev ) {
                oldMarker.remove();

                var marker = new mapboxgl.Marker( el, {offset: [0, -height / 2]} )
                    .setLngLat( [ev.result.geometry.coordinates[0], ev.result.geometry.coordinates[1]] )
                    .setDraggable( true )
                    .addTo( map );

                marker.on( 'dragend', function() {
                    var lngLat = marker.getLngLat();

                    updateFieldValue( lngLat.lat, lngLat.lng );
                });

                updateFieldValue( ev.result.geometry.coordinates[1], ev.result.geometry.coordinates[0] );
            });
        });

        /**
         * Update input value with new address
         */
        function updateFieldValue( lat, lon ) {
            if( latitudeInput && longitudeInput ) {
                latitudeInput.value = lat;
                longitudeInput.value = lon;
            }
        }
    });
});
