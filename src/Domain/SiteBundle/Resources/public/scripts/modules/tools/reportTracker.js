define(['jquery'], function( $ ) {
    'use strict';

    var reportTracker = function() {
        this.urls = {
            report_tracker: Routing.generate( 'domain_business_reports_interactions_track' )
        };
    };

    reportTracker.prototype.trackEvent = function( type, id ) {
        this.doRequest( this.urls.report_tracker, { 'type': type, 'id': id } );
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
