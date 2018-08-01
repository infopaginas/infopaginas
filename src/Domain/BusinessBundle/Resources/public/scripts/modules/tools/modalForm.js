define(
    ['jquery', 'business/tools/formModalErrorsHandler'],
    function( $, FormErrorsHandler ) {

        'use strict';

        var form = function( options ) {
            var $form = $( options.formId );

            this.formId = options.formId;
            this.modalId = options.modalId;
            this.defaultField = options.defaultField;

            this.errorHandler = new FormErrorsHandler( $form );

            if ( options.redirectUrl ) {
                this.userProfilePageURL = options.redirectUrl;
            } else {
                this.userProfilePageURL = Routing.generate( 'domain_site_user_profile' );
            }
        };

        //action before ajax send
        form.prototype.beforeRequestSend = function () {
            this.errorHandler.disableFieldsHighlight();
        };

        //actions then ajax request done
        form.prototype.onRequestComplete = function() {
            // handle request complete here
        };

        //actions on ajax success
        form.prototype.onRequestSuccess = function( response ) {
            if( response.success ) {
                $( this.modalId ).modalFunc({close: true});
                $( this.formId )[0].reset();
                document.location.href = this.userProfilePageURL;
            } else {
                if ( !$.isEmptyObject( response.errors ) ) {
                    this.errorHandler.enableFieldsHighlight( response.errors );
                } else {
                    var errors = this.getDefaultErrorObject( response.message );

                    this.errorHandler.enableFieldsHighlight( errors );
                }
            }
        };

        //actions on ajax failure
        form.prototype.onRequestError = function( jqXHR, textStatus, errorThrown ) {
            var message;

            this.errorHandler.enableFieldsHighlight();

            if (jqXHR.responseJSON !== 'undefined' && jqXHR.responseJSON.message !== 'undefined') {
                message = jqXHR.responseJSON.message;
            } else {
                message = errorThrown;
            }

            var errors = this.getDefaultErrorObject( message );

            this.errorHandler.enableFieldsHighlight( errors );
        };

        form.prototype.getDefaultErrorObject = function( error ) {
            var errorObject = {};

            errorObject[this.defaultField] = [error];

            return errorObject;
        };

        //ajax request
        form.prototype.doRequest = function ( ajaxURL, data ) {
            if( typeof data === 'undefined' ) {
                data = this.getRequestData();
            } else {
                var formData = this.getRequestData();
                for( var i in data ) {
                    formData.push( data[i] );
                }
                data = formData;
            }

            $.ajax({
                url: ajaxURL,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                beforeSend: $.proxy( this.beforeRequestSend, this ),
                complete: $.proxy( this.onRequestComplete, this ),
                success: $.proxy( this.onRequestSuccess, this ),
                error: $.proxy( this.onRequestError, this )
            });
        };

        form.prototype.getRequestData = function() {
            return $( this.formId ).serializeArray();
        };

        return form;
    }
);
