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

    reportTracker.prototype.beforeRequestHandler = function () {

    };

    reportTracker.prototype.completeHandler = function() {

    };

    reportTracker.prototype.successHandler = function( response ) {

    };

    reportTracker.prototype.errorHandler = function( jqXHR, textStatus, errorThrown ) {

    };

    reportTracker.prototype.doRequest = function ( ajaxURL, data ) {
        $.ajax({
            url: ajaxURL,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: $.proxy(this.beforeRequestHandler, this),
            complete: $.proxy(this.completeHandler, this),
            success: $.proxy(this.successHandler, this),
            error: $.proxy(this.errorHandler, this)
        });
    };

    return reportTracker;
});
