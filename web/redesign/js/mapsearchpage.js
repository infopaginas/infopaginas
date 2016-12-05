define(
    ['jquery',  'abstract/view', 'underscore', 'tools/directions', 'tools/select', 'bootstrap', 'select2', 'tools/star-rating'],
    function ( $, view, _, directions, select ) {
    'use strict';

        var mapSearchRedesignPage = function () {

            var direct = new directions;
            direct.bindEventsDirections();

            var markersBlock = $( '#map-markers' );
            var markers = {};

            map = new google.maps.Map(  document.getElementById('map'), {
                center: new google.maps.LatLng( 18.2208, -66.5901 ),
                zoom: 8
            });
            var bounds = new google.maps.LatLngBounds();

            if ( markersBlock.html() ) {
                markers = JSON.parse( markersBlock.html() );
            }

            $.each(markers, function(key, value){
                createMarker( value, bounds );
            });

            if ( markers ) {
                map.fitBounds(bounds);
            }

            var currentMapCenter = null;
            google.maps.event.addListener(map, 'resize', function () {
                currentMapCenter = map.getCenter();
            });

            google.maps.event.addListener(map, 'bounds_changed', function () {
                if (currentMapCenter) {
                    map.setCenter(currentMapCenter);
                }
                currentMapCenter = null;
            });

            $( '#filter-category, #filter-Neighborhood' ).on( 'change', function( e ) {
                var route = $( e.currentTarget ).find( 'option:selected' ).data( 'route' );

                window.location = route;
            });

            return this;
        };

        function createMarker( value, bounds ) {
            var position = new google.maps.LatLng(value.latitude, value.longitude);

            bounds.extend(position);

            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: value.name
            });

            google.maps.event.addListener( marker, "click", function () {
                var infoWindow = new google.maps.InfoWindow(
                    {
                        content: getInfoHTML(
                            value.name,
                            value.address,
                            value.reviewsCount,
                            value.rating,
                            value.logo,
                            value.longitude,
                            value.latitude,
                            value.profileUrl
                        )
                    }
                );
                infoWindow.open( map, marker );
                //todo scroll and close infoWindow
            });
            return marker;
        }

        function scrollTo( elemId ) {
            var container = $( '#searchResults' );
            var elem = $( '#' + elemId );

            container.animate({
                scrollTop: elem.offset().top - container.offset().top + container.scrollTop()
            });
        }

        function getInfoHTML( name, address, reviewsCount, avgMark, icon, longitude, latitude, profileUrl )
        {
            var template = "<div class='business-info'>" +
                "<div>" + name + "</div>";

            if ( address ) {
                template += "<div>" + address + "</div>";
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

        return mapSearchRedesignPage;
});
