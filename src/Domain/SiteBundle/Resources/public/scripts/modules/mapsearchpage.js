define(
    ['jquery',  'abstract/view', 'underscore', 'tools/directions', 'tools/select', 'tools/mapspin', 'tools/reportTracker', 'bootstrap', 'select2', 'tools/star-rating'],
    function ( $, view, _, directions, select, MapSpin, ReportTracker ) {
    'use strict';

    var mapSearchPage = function () {
        this.events = {
            '.map-address click' : 'showMarker' //todo
        };

        this.urls = {
            search: Routing.generate( 'domain_search_map' )
        };

        this.storage = {
            mapSearchUrl: 'mapSearchUrl'
        };

        this.html = {
            containers: {
                searchContainer: '#searchContainer'
            },
            buttons: {
                redoSearch: '#redo-search-in-map',
                pagination: 'div.pagination a[data-page]',
                toggleFilters: '#filter-toggle',
                toggleSorting: '#sort-toggle',
                filterCategory: '#filter-category',
                filterNeighborhood: '#filter-Neighborhood',
                sortMatch: '#sort-match',
                sortDistance: '#sort-distance'
            },
            links: {
                sortMatch: '#sort-match-link',
                sortDistance: '#sort-distance-link'
            },
            checkboxes: {
                autoSearch: '#auto-search-in-map'
            },
            forms: {
                searchForm: '#header-search-form'
            },
            tabs: {
                sort: 'div.sort-bar .sort__options.sort',
                filter: 'div.sort-bar .sort__options.filter'
            }
        };

        this.ajax = {
            action: false,
            mapDelay: 1000,
            buttonDelay: 200
        };

        this.searchAjaxRequest = null;

        this.reportTracker = new ReportTracker;

        this.init();
        this.bindFilterEvents();

        return this;
    };

    mapSearchPage.prototype = new view;

    mapSearchPage.prototype.init = function () {
        this.map = null;
        this.markers = [];
        this.options = {
            itemsListScrollable : '#searchResults',
            mapContainer : 'map',
            mapOptions   : {
                center: new google.maps.LatLng( googleMapDefaultCenter ),
                zoom: googleMapDefaultZoom
            },
            directions: new directions
        };

        var markersBlock = $( '#map-markers' );
        if ( markersBlock.data( 'google-markers' ) ) {
            this.options.markers = markersBlock.data( 'google-markers' );
        }

        new select();

        this.options.directions.bindEventsDirections();

        this.initMap(this.options);
        this.handleMapSearch();
    };

    mapSearchPage.prototype.initMap = function ( options ) {
        this.map = new google.maps.Map( document.getElementById( options.mapContainer ), this.options.mapOptions );

        //pass google map to main.js for resizing event
        map = this.map;
        this.mapSpinner = new MapSpin( this.options.mapContainer );

        if (!_.isEmpty(this.options.markers)) {
            this.addMarkers( this.options.markers );
        }

        var bounds = new google.maps.LatLngBounds();

        _.each(this.markers, function ( markerItem ) {
            bounds.extend( markerItem.marker.getPosition() );
        });

        this.map.fitBounds( bounds );

        google.maps.event.addListenerOnce( this.map, 'bounds_changed', function( event ) {
            if ( this.getZoom() > googleMapMinZoom ) {
                this.setZoom( googleMapMinZoom );
            }
        });
    };

    mapSearchPage.prototype.updateMapMarkers = function ( markers ) {
        this.deleteMarkers();

        if ( !_.isEmpty( markers ) ) {
            this.addMarkers( markers );
        }
    };

    mapSearchPage.prototype.updateGoogleTagTargeting = function ( targeting ) {
        if ( targeting && typeof googletag != 'undefined' ) {
            googletag.pubads().clearTargeting();
            googletag.pubads().setTargeting( 'search', targeting.searchKeywords );
            googletag.pubads().refresh();
        }
    };

    mapSearchPage.prototype.addMarkers = function ( markers )
    {
        _.each( markers, this.addMarker.bind( this ) );
    };

    mapSearchPage.prototype.addMarker = function ( markerData )
    {
        var self = this;
        var marker = new google.maps.Marker({
            position: {
                lat: parseFloat( markerData.latitude ),
                lng: parseFloat( markerData.longitude )
            },
            map: this.map,
            title: markerData.name,
            labelContent: "",
            labelInBackground: false,
            labelAnchor: new google.maps.Point(3, 30),
            labelClass: "labels" // the CSS class for the label
        });

        var infoWindow = new google.maps.InfoWindow({
            content: this.getInfoHTML(
                markerData.name,
                markerData.address,
                markerData.reviewsCount,
                markerData.rating,
                markerData.logo,
                markerData.longitude,
                markerData.latitude,
                markerData.profileUrl
            )
        });

        marker.addListener( 'click', function( event ) {
            self.closeAllLables();
            self.scrollTo( markerData.id );
            infoWindow.open( self.map, marker );

            if (!self.mapMarkerTriggered) {
                self.reportTracker.trackEvent( 'mapMarkerButton', markerData.id );
            }

            self.mapMarkerTriggered = false;
        });

        if ( document.getElementById( 'show-on-map-' + markerData.id ) ) {
            google.maps.event.addDomListener(document.getElementById( 'show-on-map-' + markerData.id ), "click", function( e ) {
                self.map.setCenter( marker.getPosition() );

                self.reportTracker.trackEvent( 'mapShowButton', markerData.id );
                self.mapMarkerTriggered = true;

                google.maps.event.trigger( marker, 'click' );
            });
        }

        var markerObjec = {};
        this.markers[markerData.id] = {
            marker : marker,
            infoWindow : infoWindow
        }
    };

    mapSearchPage.prototype.scrollTo = function ( elementId )
    {
        var card = this.$( '#' + elementId );

        if ( card.offset() ) {
            var offset = card.offset().top + $( this.options.itemsListScrollable ).scrollTop()
                - $( this.options.itemsListScrollable ).height()/2 + card.height()/2;

            this.$( this.options.itemsListScrollable ).first()
                .animate({
                    scrollTop : offset
                }, 1500);
            this.highlightCard( elementId );
        }
    };

    mapSearchPage.prototype.showMarker = function ( event )
    {
        this.highlightMarker( $( event.target ).parents( '.card-item' ).data( 'id' ));
    };

    mapSearchPage.prototype.highlightMarker = function ( elementId )
    {
        new google.maps.event.trigger( this.markers[elementId].marker, 'click' );
    };

    mapSearchPage.prototype.highlightCard = function ( elementId )
    {
        this.deHighlightCards();
        this.$( "#" + elementId ).addClass( 'selected-card' );
    };

    mapSearchPage.prototype.deHighlightCards = function ()
    {
        this.$( '.selected-card' ).removeClass( 'selected-card' );
    };

    mapSearchPage.prototype.closeAllLables = function ()
    {
        _.each(this.markers, function( item ) {
            item.infoWindow.close()
        });
    };

    mapSearchPage.prototype.getInfoHTML = function ( name, address, reviewsCount, avgMark, icon, longitude, latitude, profileUrl )
    {
        var directionLink = this.options.directions.getDirection(null, latitude + ',' + longitude);

        var template = "<div class='business-info'>" +
            "<div>" + name + "</div>";

        if ( address ) {
            template += "<div>" + address + "</div>" +
            "<a href='" + directionLink + "' target='_blank'>Get Direction <span>&#187;</span></a>";
        }

        if ( reviewsCount ) {
            template += "<div class=\"reviews\"><div class=\"star-rating\">";

            for ( var i = 1; i < 6; i++ ) {
                if ( i <= avgMark ) {
                    var additionClass = ' fa-star-selected';
                } else {
                    additionClass = '';
                }

                template += "<span class='fa fa-star-o" + additionClass + "' data-rating=\"" + i + "\"></span>";
            }
            template += "<input type=\"hidden\" name=\"whatever\" class=\"rating-value\" value=\"" + avgMark + "\">" +
                "</div>" +
                "<a href='" + profileUrl + "#reviews' target='_blank'><span class=\"reviews-value\">" + reviewsCount + " Reviews</span></a>" +
                "</div>" +
                "</div>";
        }

        if ( !_.isUndefined(icon) && !_.isNull(icon) ) {
            template += "<div class='business-logo'>" +
                "<img width='60' src='" + icon + "'>" +
            "</div>";
        }

        return template;
    };

    mapSearchPage.prototype.bindFilterEvents = function () {
        $( '#filter-category, #filter-Neighborhood' ).on( 'change', function( e ) {
            var route = $( e.currentTarget ).find( 'option:selected' ).data( 'route' );

            window.location = route;
        });
    };

    mapSearchPage.prototype.successHandler = function( response ) {
        $( this.html.containers.searchContainer ).html( response.html );

        var markers = $.parseJSON( response.markers );

        this.updateMapMarkers( markers );
        this.updateGoogleTagTargeting( response.targeting );
        this.options.directions.bindEventsDirections();

        window.history.replaceState( this.storage.mapSearchUrl, response.seoData.seoTitle, response.staticUrl );

        $( document ).trigger( 'searchRequestReady' );
    };

    mapSearchPage.prototype.doRequest = function ( ajaxURL, data, delay ) {
        var that = this;

        if ( this.ajax.action ) {
            clearTimeout( this.ajax.action );
        }

        that.mapSpinner.requestLoadingStart();

        that.ajax.action = setTimeout(function() {
            if ( that.searchAjaxRequest ) {
                that.searchAjaxRequest.abort();
            }

            that.searchAjaxRequest = $.ajax({
                url: ajaxURL,
                type: 'GET',
                dataType: 'JSON',
                data: data,
                success: $.proxy( that.successHandler, that )
            });
        }, delay);
    };

    mapSearchPage.prototype.submitSearch = function ( delay, page ) {
        var searchData = this.getSearchData();

        this.disableSearchFilters();

        if ( page ) {
            searchData.page = page;
        }

        this.doRequest( this.urls.search, searchData, delay );
    };

    mapSearchPage.prototype.getSearchData = function () {
        var data = {};

        if ( typeof map != 'undefined' ) {
            $( this.html.forms.searchForm ).serializeArray().map(
                function ( x ) {
                    data[ x.name ] = x.value;
                }
            );

            var mapBounds = map.getBounds();

            data.tllt = mapBounds.f.b;
            data.tllg = mapBounds.b.b;
            data.brlt = mapBounds.f.f;
            data.brlg = mapBounds.b.f;

            data.geo = '';
        }

        return data;
    };

    mapSearchPage.prototype.handleMapSearch = function() {
        var that = this;

        $( this.html.buttons.redoSearch ).on( 'click', function() {
            that.submitSearch( that.ajax.buttonDelay );
        });

        $( document ).on( 'click', this.html.buttons.pagination, function () {
            var page = $( this ).data( 'page' );

            that.submitSearch( that.ajax.buttonDelay, page );
        });

        $( document ).on( 'autoSearchRequestTriggered', function() {
            that.submitSearch( that.ajax.mapDelay );
        });
    };

    mapSearchPage.prototype.setMapOnAll =  function( map ) {
        this.markers.forEach( function( item ) {
            item.marker.setMap( map );
        });
    };

    mapSearchPage.prototype.clearMarkers = function() {
        this.setMapOnAll( null );
    };

    mapSearchPage.prototype.deleteMarkers = function() {
        this.clearMarkers();
        this.markers = [];
    };

    mapSearchPage.prototype.disableSearchFilters = function() {
        this.disableButton( this.html.buttons.toggleFilters );
        this.disableButton( this.html.buttons.toggleSorting );
        this.disableButton( this.html.buttons.filterCategory );
        this.disableButton( this.html.buttons.filterNeighborhood );

        this.disableLink( this.html.links.sortDistance );
        this.disableLink( this.html.links.sortMatch );

        $( this.html.tabs.sort ).removeClass( 'sort--on' );
        $( this.html.tabs.filter ).removeClass( 'filter--on' );
    };

    mapSearchPage.prototype.disableButton = function( button ) {
        $( button ).attr( 'disabled', 'disabled');
    };

    mapSearchPage.prototype.disableLink = function( link ) {
        $( link ).addClass( 'disabledLink' );
    };

    return mapSearchPage;
});
