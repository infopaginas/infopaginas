document.addEventListener( 'jQueryLoaded', function() {
    var width  = 24;
    var height = 34;
    var mapSpinEvent = new CustomEvent( 'mapSpin' );

    var urls = {
        search: Routing.generate( 'domain_search_map' )
    };

    var storage = {
        mapSearchUrl: 'mapSearchUrl'
    };

    var html = {
        containers: {
            searchContainer: '#searchContainer',
            mapContainer:    '#map',
            mapMarkers:      '#map-markers'
        },
        buttons: {
            redoSearch: '#redo-search-in-map',
            pagination: 'div.pagination a[data-page]',
            toggleFilters: '#filter-toggle',
            toggleSorting: '#sort-toggle',
            filterCategory: '#filter-category',
            filterNeighborhood: '#filter-Neighborhood'
        },
        links: {
            sortMatch: '#sort-match-link',
            sortDistance: '#sort-distance-link',
            compareListView: '#compareListView'
        },
        forms: {
            searchForm: '#header-search-form',
            searchLocationInput: '#searchLocation'
        },
        tabs: {
            sort: 'div.sort-bar .sort__options.sort',
            filter: 'div.sort-bar .sort__options.filter',
            filterPanel: '#searchResults div.results'
        }
    };

    var ajax = {
        action: false,
        mapDelay: 1000,
        buttonDelay: 200
    };

    var searchAjaxRequest = null;

    init();
    bindFilterEvents();

    if ( $( '[data-target-coordinates]' ).data( 'targetCoordinates' ) ) {
        if ( navigator.geolocation ) {
            navigator.geolocation.getCurrentPosition(function( position ) {
                foundLocation( position, self );
            }, notAllowedLocation);

            function notAllowedLocation( error ) {
                getDirections( [sanJuanCoordinates] );
            }

            function foundLocation( position, self ) {
                var currentCoordinates = [position.coords.latitude + ', ' + position.coords.longitude];
                getDirections( currentCoordinates );
            }
        } else {
            getDirections( [sanJuanCoordinates] );
        }
    }

    function bindFilterEvents() {
        $( '#filter-category, #filter-Neighborhood' ).on( 'change', function( e ) {
            var route = $( e.currentTarget ).find( 'option:selected' ).data( 'route' );

            window.location = route;
        });
    }

    function getDirections( currentCoordinates ) {
        var canvas = map.getCanvasContainer();

        var targetCoordinates = [$( '[data-target-coordinates]' ).data( 'targetCoordinates' )];
        var targetCoordinatesArray = targetCoordinates[0].split( ',' );
        var currentCoordinatesArray = currentCoordinates[0].split( ', ' );

        var starter = [];
        var ender = [];

        currentCoordinatesArray.forEach(function( element ) {
            ender.push( +element );
        });

        targetCoordinatesArray.forEach(function( element ) {
            starter.push( +element );
        });

        var end = [];
        end.push( ender[1] );
        end.push( ender[0] );

        var start = [];
        start.push( starter[1] );
        start.push( starter[0] );

        function getRoute( start, end ) {
            var url = 'https://api.mapbox.com/directions/v5/mapbox/driving/' + start[0] + ',' + start[1] + ';'
                + end[0] + ',' + end[1] + '?steps=true&geometries=geojson&overview=full&access_token='
                + mapboxgl.accessToken;

            var req = new XMLHttpRequest();
            req.responseType = 'json';
            req.open( 'GET', url, true );

            req.onload = function() {
                if ( req.response.routes && req.status === 200 ) {
                    var data = req.response.routes[0];

                    if ( data ) {
                        var route = data.geometry.coordinates;
                        var geojson = {
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'LineString',
                                coordinates: route
                            }
                        };

                        if (map.getSource( 'route' )) {
                            map.getSource( 'route' ).setData( geojson );
                        } else {
                            map.addLayer({
                                id: 'route',
                                type: 'line',
                                source: {
                                    type: 'geojson',
                                    data: {
                                        type: 'Feature',
                                        properties: {},
                                        geometry: {
                                            type: 'LineString',
                                            coordinates: geojson
                                        }
                                    }
                                },
                                layout: {
                                    'line-join': 'round',
                                    'line-cap': 'round'
                                },
                                paint: {
                                    'line-color': '#3887be',
                                    'line-width': 6,
                                    'line-opacity': 0.75
                                }
                            });
                        }
                    }
                }
            };

            req.send();
        }

        map.on( 'load', function() {
            getRoute( start, end );
            getRoute( start, end );

            map.addLayer({
                id: 'start',
                type: 'circle',
                source: {
                    type: 'geojson',
                    data: {
                        type: 'FeatureCollection',
                        features: [{
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'Point',
                                coordinates: start
                            }
                        }
                        ]
                    }
                },
                paint: {
                    'circle-radius': 10,
                    'circle-color': '#3887be'
                }
            });

            map.addLayer({
                id: 'end',
                type: 'circle',
                source: {
                    type: 'geojson',
                    data: {
                        type: 'FeatureCollection',
                        features: [{
                            type: 'Feature',
                            properties: {},
                            geometry: {
                                type: 'Point',
                                coordinates: end
                            }
                        }
                        ]
                    }
                },
                paint: {
                    'circle-radius': 10,
                    'circle-color': '#f30'
                }
            });

            map.on( 'click', function( e ) {
                var coordsObj = e.lngLat;
                canvas.style.cursor = '';
                var coords = Object.keys( coordsObj ).map(function( key ) {
                    return coordsObj[key];
                });

                map.removeLayer( 'end' );
                map.removeSource( 'end' );

                var end = {
                    type: 'FeatureCollection',
                    features: [{
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'Point',
                            coordinates: coords
                        }
                    }
                    ]
                };
                if (map.getLayer( 'end' )) {
                    map.getSource( 'end' ).setData( end );
                } else {
                    map.addLayer({
                        id: 'end',
                        type: 'circle',
                        source: {
                            type: 'geojson',
                            data: {
                                type: 'FeatureCollection',
                                features: [{
                                    type: 'Feature',
                                    properties: {},
                                    geometry: {
                                        type: 'Point',
                                        coordinates: coords
                                    }
                                }]
                            }
                        },
                        paint: {
                            'circle-radius': 10,
                            'circle-color': '#f30'
                        }
                    });
                }

                getRoute( start, coords );
                getRoute( start, coords );

                fitBoundsOnRoute( start );
            });

            fitBoundsOnRoute( start, 0 );

            var focusZoom = true;

            map.on( 'idle', function() {
                if ( focusZoom ) {
                    map.flyTo({
                        zoom: 16,
                        speed: 0.2
                    });

                    setTimeout( function() {
                        map.stop();

                        map.flyTo({
                            zoom: 1,
                            speed: 0.2
                        });

                        setTimeout( function() {
                            map.stop();

                            $( html.containers.mapContainer ).removeClass( 'blur-effect' );
                            document.dispatchEvent( mapSpinEvent );
                        }, 500);
                    }, 500);

                    focusZoom = false;
                }
            })
        });
    }

    function fitBoundsOnRoute( start ) {
        var end = map.getSource( 'end' )['_options']['data']['features'][0]['geometry']['coordinates'];
        var bounds = [end, start];

        map.fitBounds( bounds, { padding: 50 } );
    }

    function init() {
        this.map = null;
        this.markers = [];
        this.options = {
            itemsListScrollable : '#searchResults',
            mapContainer        : 'map'
        };

        initMapRequestedListener();
    }

    function initMapRequestedListener() {
        mapRequested = true;
        initMapHandler();

        $( document ).on( 'mapScriptRequested', function() {
            mapRequested = true;

            if ( mapRequested && !mapScriptInit ) {
                initMapHandler();
            }
        });
    }

    function initMapHandler() {
        var mapContainer = $( html.containers.mapContainer );
        mapScriptInit = true;
        mapboxgl.accessToken = apiKey;

        var center = mapDefaultCenter.split( ', ' );

        this.options.mapOptions = {
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v9',
            center: {
                lat: center[0],
                lng: center[1]
            },
            zoom: mapDefaultZoom,
            attributionControl: false
        };

        if ( mapContainer.length ) {
            var markersBlock = $( html.containers.mapMarkers );

            if ( markersBlock.data( 'mapbox-markers' ) ) {
                this.options.markers = markersBlock.data( 'mapbox-markers' );
            }

            initMap( this.options );
            handleMapSearch();
        }
    }

    function handleMapSearch() {
        $( html.buttons.redoSearch ).on( 'click', function() {
            submitSearch( ajax.buttonDelay );
        });

        $( document ).on( 'click', html.buttons.pagination, function () {
            var page = $( this ).data( 'page' );
            submitSearch( ajax.buttonDelay, page );
        });

        $( document ).on( 'autoSearchRequestTriggered', function() {
            submitSearch( ajax.mapDelay );
        });
    }

    function submitSearch( delay, page ) {
        var searchData = getSearchData();

        disableSearchFilters();

        if ( page ) {
            searchData.page = page;
        }

        doRequest( urls.search, searchData, delay );
    }

    function doRequest( ajaxURL, data, delay ) {
        if ( ajax.action ) {
            clearTimeout( ajax.action );
        }

        ajax.action = setTimeout(function() {
            if ( searchAjaxRequest ) {
                searchAjaxRequest.abort();
            }

            searchAjaxRequest = $.ajax({
                url: ajaxURL,
                type: 'GET',
                dataType: 'JSON',
                data: data,
                success: $.proxy( successHandler )
            });
        }, delay);
    }

    function successHandler( response ) {
        $( html.containers.searchContainer ).html( response.html );

        var markers = $.parseJSON( response.markers );

        updateMapMarkers( markers );
        updateGoogleTagTargeting( response.targeting );

        window.history.replaceState( storage.mapSearchUrl, response.seoData.seoTitle, response.staticSearchUrl );

        document.title = response.seoData.seoTitle;
        $( 'meta[name=description]' ).attr( 'content', response.seoData.seoDescription );

        $( html.links.compareListView ).attr( 'href', response.staticCompareUrl );
        $( html.forms.searchLocationInput ).val( response.location );

        if ( response.trackingParams && !$.isEmptyObject( response.trackingParams ) ) {
            $( document ).trigger( 'trackingMapResult', response.trackingParams );
        }

        $( document ).trigger( 'searchRequestReady' );
    }

    function updateGoogleTagTargeting( targeting ) {
        if ( targeting && typeof googletag != 'undefined' ) {
            googletag.pubads().clearTargeting();
            googletag.pubads().setTargeting( 'search', targeting.searchKeywords );
            googletag.pubads().refresh();
        }
    }

    function updateGoogleTagTargeting( targeting ) {
        if ( targeting && typeof googletag != 'undefined' ) {
            googletag.pubads().clearTargeting();
            googletag.pubads().setTargeting( 'search', targeting.searchKeywords );
            googletag.pubads().refresh();
        }
    }

    function updateMapMarkers ( markers ) {
        deleteMarkers();

        if ( !_.isEmpty( markers ) ) {
            addMarkers( markers , true);
        }
    }

    function deleteMarkers() {
        clearMarkers();
        this.markers = [];
    }

    function clearMarkers() {
        setMapOnAll( null );
    }

    function setMapOnAll( map ) {
        this.markers.forEach(function( item ) {
            item.marker.remove();
        });
    }

    function getSearchData() {
        var data = {};

        if ( typeof map != 'undefined' ) {
            $( html.forms.searchForm ).serializeArray().map(
                function ( x ) {
                    data[ x.name ] = x.value;
                }
            );

            var mapBounds = map.getBounds();
            var mapCenter = map.getCenter();

            data.tllt = mapBounds._ne.lat;
            data.tllg = mapBounds._sw.lng;
            data.brlt = mapBounds._sw.lat;
            data.brlg = mapBounds._ne.lng;
            data.clt  = mapCenter.lat;
            data.clg  = mapCenter.lng;

            data.geo = '';
        }

        return data;
    }

    function disableSearchFilters() {
        disableButton( html.buttons.toggleFilters );
        disableButton( html.buttons.toggleSorting );
        disableButton( html.buttons.filterCategory );
        disableButton( html.buttons.filterNeighborhood );

        disableLink( html.links.sortDistance );
        disableLink( html.links.sortMatch );

        $( html.tabs.sort ).removeClass( 'sort--on' );
        $( html.tabs.filter ).removeClass( 'filter--on' );
        $( html.tabs.filterPanel ).removeClass( 'active__toggle' );
    }

    function disableButton( button ) {
        $( button ).attr( 'disabled', 'disabled' );
    }

    function disableLink( link ) {
        $( link ).addClass( 'disabledLink' );
    }

    function initMap ( options ) {
        this.map = new mapboxgl.Map( this.options.mapOptions );
        this.map.addControl( new mapboxgl.NavigationControl(), 'bottom-right' );

        var options = this.options;

        //pass map to main.js for resizing event
        map = this.map;

        var layerList = document.getElementById( 'menu' );

        if ( layerList ) {
            var inputs = layerList.getElementsByTagName( 'input' );

            for ( var i = 0; i < inputs.length; i++ ) {
                inputs[i].onclick = switchLayer;
            }
        }

        if ( !$( '[data-target-coordinates]' ).data( 'targetCoordinates' ) ) {
            document.dispatchEvent(mapSpinEvent);
        }

        if ( !_.isEmpty( this.options.markers ) ) {
            addMarkers( this.options.markers );
        }
    }

    function switchLayer( layer ) {
        var layerId = layer.target.id;
        map.setStyle( 'mapbox://styles/mapbox/' + layerId + '-v9' );
    }

    function addMarkers( markers, isSearchOnMap ) {
        var self = this;

        if ( navigator.geolocation ) {
            navigator.geolocation.getCurrentPosition(function( position ) {
                var youPos = {
                    id: 0,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    name: youPosText,
                    current: true
                };

                addMarker( youPos );
            });
        }

        _.each( markers, addMarker.bind( this ) );
    }

    function addMarker( markerData ) {
        var self = this;

        var infoWindow = new mapboxgl.Popup().setHTML( getInfoHTML( markerData ) );

        var el = document.createElement( 'div' );

        if ( _.has( markerData, 'current' ) ) {
            el.className = 'marker-yellow';
        } else {
            el.className = 'marker';
        }

        el.id = markerData.id;
        el.style.height = height + 'px';
        el.style.width = width + 'px';

        var marker = new mapboxgl.Marker( el, { offset: [0, -height / 2] }, { interactive: true } )
            .setLngLat( [parseFloat(markerData.longitude), parseFloat(markerData.latitude)] )
            .setPopup( infoWindow )
            .addTo( this.map );

        el.addEventListener('click', function( event ) {
            $( document ).trigger( 'disableAutoSearchInMap' );

            closeAllLables();
            scrollTo( markerData.id );

            if ( !self.mapMarkerTriggered ) {
                $( document ).trigger( 'trackingInteractions', ['mapMarkerButton', markerData.id] );
            }

            self.mapMarkerTriggered = false;
        });

        if ( document.getElementById( 'show-on-map-' + markerData.id ) ) {
            document.getElementById( 'show-on-map-' + markerData.id ).addEventListener('click', function( event ) {
                closeAllLables();
                marker.togglePopup();

                $( document ).trigger( 'trackingInteractions', ['mapShowButton', markerData.id] );
                self.mapMarkerTriggered = true;

                scrollTo( markerData.id );

                var bounds = new mapboxgl.LngLatBounds();
                bounds.extend( marker.getLngLat() );
                map.fitBounds( bounds );
            });
        }

        this.markers[markerData.id] = {
            marker : marker,
            infoWindow : infoWindow
        }
    }

    function closeAllLables()
    {
        _.each(this.markers, function( item ) {
            item.infoWindow.remove();
        });
    }

    function getInfoHTML( data )
    {
        var template;
        var item = $( '#' + data.id );

        var itemContent = item.find( 'div[data-item-content]' );
        var directionButton = item.find( 'a.get-dir' );

        if ( itemContent.length ) {
            var content = $( '<div class="map-info-window">' ).append( itemContent.clone() );

            if ( directionButton.length ) {
                content.find( '.item__summary' ).append( directionButton.clone() );
            }

            template = content.html();
        } else {
            template = "<div class='business-info'><div>" + data.name + "</div></div>";
        }

        return template;
    }

    function scrollTo( elementId )
    {
        var card = this.$( '#' + elementId );

        if ( card.offset() && elementId ) {
            var offset = card.offset().top + $( '#searchResults' ).scrollTop()
                - $( '#searchResults' ).height()/2 + card.height()/2;

            this.$( '#searchResults' ).first()
                .animate({
                    scrollTop : offset
                }, 1500);
            highlightCard( elementId );
        }
    }

    function highlightCard( elementId )
    {
        deHighlightCards();
        this.$( "#" + elementId ).addClass( 'selected-card' );
    }

    function deHighlightCards()
    {
        this.$( '.selected-card' ).removeClass( 'selected-card' );
    }
});
