define(['jquery'], function( $ ) {
    'use strict';

    var reportTracker = function() {
        this.urls = {
            interactions_report_tracker: Routing.generate( 'domain_business_reports_interactions_track' ),
            event_report_tracker: Routing.generate( 'domain_business_reports_event_track' )
        };
        this.html = {
            paramsBlock: '#trackingParams',
            paramsData: 'track-params'
        };
        this.status = false;

        this.init();
    };

    reportTracker.prototype.init = function() {
        this.addTrackingEvents();
        this.addTrackingMapEvents();
        this.addTrackingInteractionsEvents();
    };

    reportTracker.prototype.addTrackingEvents = function() {
        var trackingParamsBlock = $( this.html.paramsBlock );

        if ( trackingParamsBlock.length ) {
            this.status = true;
            var trackingParamsData = trackingParamsBlock.data( this.html.paramsData );

            this.doRequest( this.urls.event_report_tracker, trackingParamsData );
        }
    };

    reportTracker.prototype.addTrackingMapEvents = function() {
        var self = this;

        $( document ).on( 'trackingMapResult', function( event, data ) {
            self.doRequest( self.urls.event_report_tracker, data );
        });
    };

    reportTracker.prototype.addTrackingInteractionsEvents = function() {
        var self = this;

        $( document ).on( 'trackingInteractions', function( event, type, id ) {
            self.trackEvent( type, id );
        });
    };

    reportTracker.prototype.trackEvent = function( type, id ) {
        this.doRequest( this.urls.interactions_report_tracker, { 'type': type, 'id': id } );
    };

    reportTracker.prototype.doRequest = function ( ajaxURL, data ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            dataType: 'JSON',
            data: data
        });
    };

    return reportTracker;
});
