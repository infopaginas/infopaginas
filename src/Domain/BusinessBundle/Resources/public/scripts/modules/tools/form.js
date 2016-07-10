define(['jquery', 'bootstrap', 'alertify', 'tools/spin', 'tools/select', 'tools/formErrorsHandler'], function( $, bootstrap, alertify, Spin, select, FormErrorsHandler) {
    'use strict';

    var form = function( options ) {
        var $form = $( options.formId );

        this.formId = options.formId;

        this.errorHandler = new FormErrorsHandler( $form );

        this.spinnerContainerId = options.spinnerId;

        this.spinner = new Spin();
    };

    //action before ajax send
    form.prototype.beforeRequestSend = function () {
        this.errorHandler.disableFieldsHighlight();
        this.spinner.show( this.spinnerContainerId );
    };

    //actions then ajax request done
    form.prototype.onRequestComplete = function() {
        this.spinner.hide();
    };

    //actions on ajax success
    form.prototype.onRequestSuccess = function( response ) {
        if( response.success ) {
            alertify.success( response.message );
        } else {
            this.errorHandler.enableFieldsHighlight( response.errors );
            if ( this.errorHandler.tabSwitchRequired() ) {
                var $tab = $( this.formId ).find( '.error' ).parents( '.tab-pane' );
                var tabId = $tab.attr('id');
                $('a[href="#' + tabId + '"]').click();
            }
            alertify.error( response.message );
        }
    };

    //actions on ajax failure
    form.prototype.onRequestError = function( jqXHR, textStatus, errorThrown ) {
        this.errorHandler.enableFieldsHighlight();
        if ( this.errorHandler.tabSwitchRequired() ) {
            var $tab = $( this.formId ).find( '.error' ).parents( '.tabs-block li.active' );
            var href = $tab.find('a').attr('href');
            console.log(href);
        }
        alertify.error( errorThrown );
    };

    //ajax request
    form.prototype.doRequest = function ( ajaxURL, data ) {
        //no additional info required? - just serialize form
        if (typeof data === 'undefined') {
            data = this.getRequestData();
        } else {
            var formData = this.getRequestData();
            for (var i in data) {
                formData.push(data[i]);
            }
            data = formData;
        }

        console.log(data);

        $.ajax({
            url: ajaxURL,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: $.proxy(this.beforeRequestSend, this),
            complete: $.proxy(this.onRequestComplete, this),
            success: $.proxy(this.onRequestSuccess, this),
            error: $.proxy(this.onRequestError, this)
        });
    };

    form.prototype.getRequestData = function() {
        return $( this.formId).serializeArray();
    };

    return form;
});
