define(
    ['jquery',  'abstract/view', 'underscore','bootstrap', 'select2', 'tools/select', 'tools/star-rating', 'async!https://maps.googleapis.com/maps/api/js?v=3&signed_in=false&libraries=drawing,places&key=AIzaSyACRiuSCjh3c3jgxC53StYJCvag6Ig8ZIw'], 
    function ( $, view, _ ) {
    'use strict';

    var mapSearchPage = function ( options ) {
        options.selector = options.selector || 'body';
        this.$ = function( selector ) {
            return $( options.selector ).find( selector );
        }

        this.events = {
            '.map-address click' : 'showMarker'
        }

        this.init( options );
        this.bindEvents();
        return this;
    }

    mapSearchPage.prototype = new view;

    mapSearchPage.prototype.init = function ( options ) {
        this.map = null;
        this.markers = [];
        this.options = {
            itemsListScrollable : '.map-view-aside',
            mapContainer : 'map-canvas',
            mapOptions   : {
                center: new google.maps.LatLng(18.2208, -66.5901),
                zoom: 8
            }
        };

        $.extend( this.options, options );

        this.initMap(this.options);
    }

    mapSearchPage.prototype.initMap = function ( options ) {
        this.map = new google.maps.Map(document.getElementById(options.mapContainer), this.options.mapOptions);

        function resizeMap() {
            var parentWidth = $('#search-results-map').width();
            var parentHeight = $('#search-results-map').height();
            if(parentWidth < 992){
                $('#map-canvas').width(parentWidth);
                $('#map-canvas').height(parentHeight - 150);
            } else{
                $('#map-canvas').width(parentWidth - 515);
                $('#map-canvas').height(parentHeight - 150);
            }
        }

        $(window).resize(resizeMap);

        if (!_.isEmpty(this.options.markers)) {
            this.addMarkers(this.options.markers);
        }

        var bounds = new google.maps.LatLngBounds();

        _.each(this.markers, function ( markerItem ) {
            bounds.extend( markerItem.marker.getPosition());
        })

        this.map.fitBounds(bounds);
    }

    mapSearchPage.prototype.addMarkers = function ( markers )
    {
        _.each(markers, this.addMarker.bind(this));
    }

    mapSearchPage.prototype.addMarker = function ( markerData )
    {
        var self = this;
        var marker = new google.maps.Marker({
            position: {
                lat: parseFloat(markerData.latitude),
                lng: parseFloat(markerData.longitude)
            },
            map: this.map,
            title: markerData.name,
            labelContent: "Ololoshenki",
            labelInBackground: false,
            labelAnchor: new google.maps.Point(3, 30),
            labelClass: "labels", // the CSS class for the label
        });

        var infoWindow = new google.maps.InfoWindow({
            content: this.getInfoHTML(markerData.name, markerData.address, markerData.reviewsCount)
        });

        marker.addListener('click', function(event) {
            self.closeAllLables();
            self.scrollTo(markerData.id);
            infoWindow.open(self.map, marker);
        });

        var markerObjec = {};
        this.markers[markerData.id] = { 
            marker : marker,
            infoWindow : infoWindow
        }
    }

    mapSearchPage.prototype.scrollTo = function ( elementId )
    {
        this.$(this.options.itemsListScrollable).first()
            .animate({
                scrollTop : this.$('#' + elementId).offset().top
            }, 1500);
        this.highlightCard( elementId ); 
    }

    mapSearchPage.prototype.showMarker = function ( event )
    {
        this.highlightMarker($(event.target).parents('.card-item').data('id'));
    }

    mapSearchPage.prototype.highlightMarker = function ( elementId )
    {
        new google.maps.event.trigger( this.markers[elementId].marker, 'click' );
    }

    mapSearchPage.prototype.highlightCard = function ( elementId )
    {
        this.deHighlightCards();
        this.$("#" + elementId).addClass('selected-card');
    }

    mapSearchPage.prototype.deHighlightCards = function ()
    {
        this.$('.selected-card').removeClass('selected-card');
    }

    mapSearchPage.prototype.closeAllLables = function ()
    {
        _.each(this.markers, function( item ) {
            item.infoWindow.close()
        });
    }

    mapSearchPage.prototype.getInfoHTML = function (name, address, reviewsCount, avgMark, icon)
    {
        var template = "<div class='business-info'>" +
            "<div>" + name + "</div>" +
            "<div>" + address + "</div>" +
                "<div class=\"reviews\">" +
                    "<div class=\"star-rating\">" +
                        "<span class=\"fa fa-star-o\" data-rating=\"1\"></span>" +
                        "<span class=\"fa fa-star-o\" data-rating=\"2\"></span>" +
                        "<span class=\"fa fa-star-o\" data-rating=\"3\"></span>" +
                        "<span class=\"fa fa-star-o\" data-rating=\"4\"></span>" +
                        "<span class=\"fa fa-star-o\" data-rating=\"5\"></span>" +
                        "<input type=\"hidden\" name=\"whatever\" class=\"rating-value\" value=\"" + avgMark + "\">" +
                    "</div>" +
                    "<span class=\"reviews-value\">" + reviewsCount + " Reviews</span>" +
                "</div>" +
            "</div>";

            if ( !_.isUndefined(icon) && !_.isNull(icon) ) {
                template += "<div class='business-logo'>" +
                    "<img src='" + icon + "'>" +
                "</div>";
            }

            return  template;
    }

    return mapSearchPage;
});
